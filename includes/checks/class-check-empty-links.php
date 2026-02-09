<?php
/**
 * Check: Empty links
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Empty_Links
 */
class ASFW_Check_Empty_Links extends ASFW_Check_Base {

    public function get_id(): string {
        return 'empty-links';
    }

    public function get_name(): string {
        return __('Empty Links', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '2.4.4';
    }

    public function get_level(): string {
        return 'A';
    }

    public function get_severity(): string {
        return 'serious';
    }

    public function get_category(): string {
        return 'navigation';
    }

    public function is_fixable(): bool {
        return true;
    }

    public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
        $links = $xpath->query('//a[@href]');
        if (!$links) {
            return;
        }

        foreach ($links as $link) {
            // Skip skip-links and anchors.
            $href = $link->getAttribute('href');
            if ($href === '#' || str_starts_with($href, '#')) {
                // Only flag if it also has no text.
            }

            // Check for accessible name.
            if ($this->has_accessible_name($link, $xpath)) {
                continue;
            }

            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => $dom->saveHTML($link),
                'selector' => $this->get_selector($link),
                'message'  => __('Link has no accessible text', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add text content, aria-label, or an alt attribute to an image inside the link', 'wp-accessibility-scanner'),
                'context'  => $this->get_context($link, $dom),
            ]);
        }
    }

    /**
     * Check if element has an accessible name
     *
     * @param DOMElement $element The element.
     * @param DOMXPath   $xpath   XPath instance.
     * @return bool
     */
    private function has_accessible_name(DOMElement $element, DOMXPath $xpath): bool {
        // aria-label.
        if (trim($element->getAttribute('aria-label'))) {
            return true;
        }

        // aria-labelledby.
        if (trim($element->getAttribute('aria-labelledby'))) {
            return true;
        }

        // title.
        if (trim($element->getAttribute('title'))) {
            return true;
        }

        // Text content.
        if (trim($element->textContent)) {
            return true;
        }

        // Image with alt inside the link.
        $imgs = $xpath->query('.//img[@alt]', $element);
        if ($imgs && $imgs->length > 0) {
            foreach ($imgs as $img) {
                if (trim($img->getAttribute('alt'))) {
                    return true;
                }
            }
        }

        // SVG with title.
        $svgs = $xpath->query('.//svg/title', $element);
        if ($svgs && $svgs->length > 0) {
            return true;
        }

        return false;
    }
}
