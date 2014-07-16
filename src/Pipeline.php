<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2014 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\Html;

/**
 * A {@link Filter} that lets you chain a list of filters together.
 */
class Pipeline extends Filter {
    /// Properties ///

    protected $defaultContext;

    /**
     * @var array[Filter] The array of filters in the pipeline.
     */
    protected $filters;

    /// Methods ///

    /**
     * Initialize a {@link Pipeline} object.
     *
     * @param array[Filter] $filters The filters in the pipeline.
     * @param array $defaultContext The default context for the filters.
     */
    public function __construct(array $filters, array $defaultContext = []) {
        $this->filters = $filters;
        $this->defaultContext = $defaultContext;
    }

    /**
     * {@inheritdoc}
     */
    public function call($content) {
        /* @var Filter $content */
        foreach ($this->filters as $filter) {
            $content = $filter->call($content);
        }

        return $content;
    }
}