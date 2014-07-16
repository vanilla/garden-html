<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2014 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\Html;

use Parsedown;

/**
 * A {@link Filter} that converts markdown to html with {@link Parsedown} object.
 */
class ParsedownFilter extends Filter {
    public function call($content) {
        if (!is_string($content)) {
            $content = Content::box($content)->getHtml();
        }
        $html = Parsedown::instance()->setBreaksEnabled(true)->text($content);
        return rtrim($html);
    }
}
