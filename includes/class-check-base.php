<?php
/**
 * Abstract base class for checks
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Base
 */
abstract class ASFW_Check_Base implements ASFW_Check_Interface {

    /**
     * Get a CSS-like selector for an element
     *
     * @param DOMElement $element The DOM element.
     * @return string
     */
    protected function get_selector(DOMElement $element): string {
        $parts = [];
        $node  = $element;

        while ($node && $node instanceof DOMElement) {
            $tag = $node->tagName;
            $id  = $node->getAttribute('id');

            if ($id) {
                $parts[] = $tag . '#' . $id;
                break;
            }

            $class = $node->getAttribute('class');
            if ($class) {
                $classes = preg_split('/\s+/', trim($class));
                $tag    .= '.' . implode('.', array_slice($classes, 0, 2));
            }

            $parts[] = $tag;
            $node    = $node->parentNode;
        }

        return implode(' > ', array_reverse($parts));
    }

    /**
     * Get surrounding HTML context for an element
     *
     * @param DOMElement  $element The DOM element.
     * @param DOMDocument $dom     The DOM document.
     * @return string
     */
    protected function get_context(DOMElement $element, DOMDocument $dom): string {
        $html = $dom->saveHTML($element);
        if (strlen($html) > 200) {
            $html = substr($html, 0, 200) . '...';
        }
        return $html;
    }
}
