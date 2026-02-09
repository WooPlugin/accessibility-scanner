<?php
/**
 * Check: Missing iframe title
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Iframe_Title
 */
class ASFW_Check_Iframe_Title extends ASFW_Check_Base {

    public function get_id(): string {
        return 'iframe-title';
    }

    public function get_name(): string {
        return __('Iframe Title', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '4.1.2';
    }

    public function get_level(): string {
        return 'A';
    }

    public function get_severity(): string {
        return 'moderate';
    }

    public function get_category(): string {
        return 'structure';
    }

    public function is_fixable(): bool {
        return false;
    }

    public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
        $iframes = $xpath->query('//iframe[not(@title) or @title=""]');
        if (!$iframes) {
            return;
        }

        foreach ($iframes as $iframe) {
            // Skip hidden iframes.
            if ($iframe->getAttribute('aria-hidden') === 'true') {
                continue;
            }
            if ($iframe->getAttribute('hidden') !== '') {
                // Only skip if 'hidden' is actually present as an attribute.
                $has_hidden = $iframe->hasAttribute('hidden');
                if ($has_hidden) {
                    continue;
                }
            }

            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => $dom->saveHTML($iframe),
                'selector' => $this->get_selector($iframe),
                'message'  => __('Iframe is missing a title attribute', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add a title attribute that describes the content of the iframe', 'wp-accessibility-scanner'),
                'context'  => $this->get_context($iframe, $dom),
            ]);
        }
    }
}
