<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2014 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\Html;

use pQuery\TextNode;

/**
 *
 */
class MentionsFilter extends Filter {

    /**
     * @var array An array of tag names that should not have mentions formatted within them.
     */
    protected $ignoreParents = ['pre', 'cod', 'a'];

    /**
     * @var string The regular expression used to search for @mentions.
     * @copyright 2012 GitHub Inc. and Jerry Cheung
     * @link https://github.com/jch/html-pipeline/blob/master/lib/html/pipeline/%40mention_filter.rb
     */
    protected $mentionPattern = <<<PATTERN
`
(?:^|\W)                   # beginning of string or non-word char
@((?>[a-z0-9][a-z0-9-]*))  # @username
(?!/)                      # without a trailing slash
(?=
  \.+[ \t\W]|              # dots followed by space or non-word character
  \.+$|                    # dots at end of line
  [^0-9a-zA-Z_.]|          # non-word character except dot
  $                        # end of line
)
`iux
PATTERN;

    /**
     * @var array The usernames that were mentioned during a filter.
     */
    protected $mentionedUsernames;

    /**
     * {@inheritdoc}
     */
    public function call($content) {
        $content = Content::box($content);
        $this->mentionedUsernames = [];
        $doc = $content->getDoc();

        /* @var TextNode $node */
        foreach ($doc->query('text()') as $node) {
            $text = $node->text();
            if (strpos($text, '@') === false) {
                continue;
            }

            // Don't format mentions in ignored tags.
            if (Content::hasAncestor($node, $this->ignoreParents)) {
                continue;
            }

            $text = $this->replaceMentions($text, $node);
            $node->text($text);
        }
        $content->setData('mentions', array_reverse(array_keys($this->mentionedUsernames)));
        return $content;
    }

    /**
     * Find the usernames that begin with a string.
     *
     * When formatting mentions, usernames with spaces cause a problem because a simple regex cannot determine
     * when a mention has a space or not.
     *
     * @param string $beginsWith The string to search for.
     * @return array[string] Returns the full usernames.
     */
    public function findUsernames($beginsWith) {
        return [$beginsWith];
    }

    /**
     * Replace mentions with an html link and capture the mentioned users.
     *
     * @param string $text The text to replace.
     * @param DomNode $node The node that the text belongs to.
     * @return string Returns the text with mentions replaced.
     */
    public function replaceMentions($text, $node) {
        if (!preg_match_all($this->mentionPattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
            return;
        }

        $mentions = $matches[1];
        // Walk through the mentions backwards so that changing the string won't affect future mentions.
        for ($i = count($mentions) - 1; $i >= 0; $i--) {
            list($username, $pos) = $mentions[$i];

            // Look up the user based on the start of the name.
            $usernames = $this->findUsernames($username);
            $this->replaceMentionInternal($usernames, $pos, $node, $text);
        }
        return $text;
    }

    /**
     * Replace a mention with the appropriate html.
     *
     * @param array $usernames All of the usernames to check.
     * @param int $pos The position to look at.
     * @param DomNode $node The node that the text is contained in.
     * @param string $text The full text to do the replacement on.
     */
    protected function replaceMentionInternal($usernames, $pos, $node, &$text) {
        foreach ($usernames as $username) {
            // Check to see if the username actually matches in the text.
            if (substr_compare($text, $username, $pos, strlen($username)) === 0) {
                $hname = htmlspecialchars($username);
                $text = substr($text, 0, $pos - 1)."<a href=\"#\" data-username=\"$hname\">@$hname</a>".substr($text, $pos + strlen($username));
                $this->mentionedUsernames[$username] = true;
                break;
            }
        }
    }
}
