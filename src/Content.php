<?php
/**
 * @author Todd Burry <todd@vanillaforums.com>
 * @copyright 2009-2014 Vanilla Forums Inc.
 * @license MIT
 */

namespace Garden\Html;

use pQuery;
use pQuery\DomNode;

/**
 * A class to store result content from html filters.
 */
class Content {
    /// Properties ///

    /**
     * @var array An array of additional data that goes with the content.
     */
    protected $data;

    /**
     * @var DomNode The document fragment to be manipulated.
     */
    protected $doc;

    /**
     * @var string The string representation of the document.
     */
    protected $html;

    /// Methods ///

    /**
     * Initialize a {@link Content} object.
     *
     * @param string|DomNode $doc The html or document.
     * @param array $data The result data of the content.
     */
    public function __construct($doc, $data = []) {
        if (is_string($doc)) {
            $this->html = $doc;
            $this->doc = null;
        } else {
            $this->doc = $doc;
            $this->html = null;
        }
        $this->data = $data;
    }

    /**
     * Get the string representation of the object.
     *
     * @return string Returns the content html.
     */
    public function __toString() {
        return $this->getHtml();
    }

    /**
     * Box some content into a valid {@link Content} object.
     *
     * @param mixed $content The content to box.
     * @return Content Returns the content object.
     */
    public static function box($content) {
        if ($content instanceof Content) {
            return $content;
        } else {
            return new Content($content);
        }
    }

    /**
     * Gets the {@link DomNode} to be manipulated.
     *
     * If the filter was provided a string, parse into an {@link IQuery} the first time this method is called.
     *
     * @return DomNode Returns the document fragment.
     */
    public function getDoc() {
        if ($this->doc === null) {
            $this->doc = pQuery::parseStr($this->html);
            $this->html = null;
        }
        return $this->doc;
    }

    /**
     * Get the string representation of the document.
     *
     * If a {@link DomNode} was provided to the Filter, it is serialized into a string when this method is called.
     *
     * @return string Returns the html fragment.
     */
    public function getHtml() {
        if ($this->html === null) {
            return $this->doc->html();
        }
        return $this->html;
    }

    /**
     * Set the html representation of the document.
     *
     * @param string $html The html string for the document.
     */
    public function setHtml($html) {
        $this->html = $html;
        $this->doc = null;
    }

    /**
     * Get the data at a given {@link $key}.
     *
     * @param string $key The key of the data.
     * @param mixed The default value to return if $key is not found.
     * @return mixed Returns the value at {@link $key} or {@link $default} if key is not found.
     */
    public function getData($key, $default = null) {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }

    /**
     * Set the data at a given {@link $key}.
     *
     * @param string $key The key of the data.
     * @param mixed $value The value to set.
     * @@return Content Returns $this for fluent calls.
     */
    public function setData($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Check whether or not a node has a parent with a given tag(s).
     *
     * @param DomNode $node The node to check.
     * @param array|string $tags A tag or array of tags to check against.
     * @return bool Returns true if the node has an ancestor or false otherwise.
     */
    public static function hasAncestor(DomNode $node, $tags) {
        $tags = (array)$tags;

        $limit = 50;
        while ($node->parent !== null && $limit > 0) {
            if (in_array(strtolower($node->parent->tag), $tags)) {
                return true;
            }

            $node = $node->parent;
            $limit--;
        }
        return false;
    }
}
 