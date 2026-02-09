<?php
/**
 * Check: Duplicate IDs
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Duplicate_IDs
 */
class ASFW_Check_Duplicate_IDs extends ASFW_Check_Base {

    public function get_id(): string {
        return 'duplicate-ids';
    }

    public function get_name(): string {
        return __('Duplicate IDs', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '4.1.1';
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
        $elements = $xpath->query('//*[@id]');
        if (!$elements) {
            return;
        }

        $id_counts = [];
        $id_elements = [];

        foreach ($elements as $element) {
            $id = $element->getAttribute('id');
            if (empty($id)) {
                continue;
            }

            if (!isset($id_counts[$id])) {
                $id_counts[$id]   = 0;
                $id_elements[$id] = $element;
            }
            ++$id_counts[$id];
        }

        foreach ($id_counts as $id => $count) {
            if ($count > 1) {
                $element = $id_elements[$id];
                $issues[] = new ASFW_Issue([
                    'check_id' => $this->get_id(),
                    'element'  => $dom->saveHTML($element),
                    'selector' => '#' . $id,
                    'message'  => sprintf(
                        /* translators: 1: element ID, 2: count */
                        __('ID "%1$s" is used %2$d times on this page', 'wp-accessibility-scanner'),
                        $id,
                        $count
                    ),
                    'impact'   => $this->get_severity(),
                    'wcag'     => $this->get_wcag(),
                    'fix_hint' => __('Ensure each id attribute value is unique on the page', 'wp-accessibility-scanner'),
                    'context'  => $this->get_context($element, $dom),
                ]);
            }
        }
    }
}
