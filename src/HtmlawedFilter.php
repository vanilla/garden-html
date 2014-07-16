<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2014 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\Html;

use pQuery\DomNode;
use Htmlawed\Htmlawed;

/**
 * A {@link Filter} that passes the data through htmLawed.
 *
 * In order to use this filter you'll need to require the vanilla/htmLawed component:
 *
 * ```
 * "require": {
 *     "vanilla/htmlawed": "~1.0.1"
 * }
 * ```
 */
class HtmlawedFilter extends Filter {
    /**
     * Call the filter on the given content.
     *
     * @param Content|string|DomNode $content The content to filter.
     * @return Content|string Returns either a {@link Content} object or a string representing the filtered content.
     */
    public function call($content) {
        if (is_string($content)) {
            $result = Htmlawed::filter($content);
            return $result;
        } else {
            $content = Content::box($content);
            $html = Htmlawed::filter($content->getHtml());
            $content->setHtml($html);
            return $content;
        }
    }
}
