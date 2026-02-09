<?php
/**
 * Check: Missing form labels
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Form_Labels
 */
class ASFW_Check_Form_Labels extends ASFW_Check_Base {

    public function get_id(): string {
        return 'form-labels';
    }

    public function get_name(): string {
        return __('Form Labels', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '1.3.1';
    }

    public function get_level(): string {
        return 'A';
    }

    public function get_severity(): string {
        return 'critical';
    }

    public function get_category(): string {
        return 'forms';
    }

    public function is_fixable(): bool {
        return true;
    }

    public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
        $inputs = $xpath->query('//input[@type!="hidden" and @type!="submit" and @type!="button" and @type!="reset" and @type!="image"] | //select | //textarea');
        if (!$inputs) {
            return;
        }

        foreach ($inputs as $input) {
            // Skip if has aria-label or aria-labelledby.
            if ($input->getAttribute('aria-label') || $input->getAttribute('aria-labelledby')) {
                continue;
            }

            // Skip if has title attribute.
            if ($input->getAttribute('title')) {
                continue;
            }

            // Check for associated <label>.
            $id = $input->getAttribute('id');
            if ($id) {
                $label = $xpath->query('//label[@for="' . $id . '"]');
                if ($label && $label->length > 0) {
                    continue;
                }
            }

            // Check if wrapped in a <label>.
            $parent = $input->parentNode;
            while ($parent) {
                if ($parent instanceof DOMElement && $parent->tagName === 'label') {
                    continue 2;
                }
                $parent = $parent->parentNode;
            }

            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => $dom->saveHTML($input),
                'selector' => $this->get_selector($input),
                'message'  => __('Form input is missing an associated label', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add a <label> element with a matching "for" attribute, or use aria-label', 'wp-accessibility-scanner'),
                'context'  => $this->get_context($input, $dom),
            ]);
        }
    }
}
