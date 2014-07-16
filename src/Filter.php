<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2014 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\Html;

use pQuery\DomNode;

abstract class Filter {
    /// Properties ///
    protected $context;

    /// Methods ///

    /**
     * Call the filter on the given content.
     *
     * @param Content|string $content The content to filter.
     * @return Content|string Returns either a {@link Content} object or a string representing the filtered content.
     */
    abstract public function call($content);

    /**
     * @return mixed
     */
    public function &getContext() {
        return $this->context;
    }

    /**
     * @param mixed $context
     */
    public function setContext(&$context) {
        $this->context =& $context;
    }
}
 