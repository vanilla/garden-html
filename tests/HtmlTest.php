<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2014 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\Html\Test;

use Garden\Html\MentionsFilter;
use Garden\Html\ParsedownFilter;
use Garden\Html\Pipeline;

class HtmlTest extends \PHPUnit_Framework_TestCase {
    public function testParsedownFilter() {
        $filter = new ParsedownFilter();
        $html = $filter->call('# Hello World');

        $this->assertEquals('<h1>Hello World</h1>', $html);
    }

    public function testMentionsFilter() {
        $filter = new MentionsFilter();
        $content = $filter->call('<p>@todd and @tim! what <i>are</i> you @ doing!!!!</p>');

        $this->assertEquals(['todd', 'tim'], $content->getData('mentions'));
    }

    public function testSimplePipeline() {
        $pipeline = new Pipeline([
            new ParsedownFilter(),
            new MentionsFilter()
        ]);

        $md = <<<MARKDOWN
Hey I'm just looking at **you** and **@todd**, but not [@tim](http://google.com).

1. Help.
2. Me.
MARKDOWN;


        $content = $pipeline->call($md);
        $html = $content->getHtml();
    }
}
