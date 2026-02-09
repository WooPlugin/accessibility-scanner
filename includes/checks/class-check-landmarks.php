<?php
/**
 * Check: Missing landmark regions
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Landmarks
 */
class ASFW_Check_Landmarks extends ASFW_Check_Base {

    public function get_id(): string {
        return 'landmarks';
    }

    public function get_name(): string {
        return __('Landmark Regions', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '1.3.1';
    }

    public function get_level(): string {
        return 'A';
    }

    public function get_severity(): string {
        return 'minor';
    }

    public function get_category(): string {
        return 'structure';
    }

    public function is_fixable(): bool {
        return false;
    }

    public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
        // Check for <main> or role="main".
        $main = $xpath->query('//main | //*[@role="main"]');
        if (!$main || $main->length === 0) {
            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => '',
                'selector' => 'main',
                'message'  => __('Page is missing a main landmark region', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add a <main> element to wrap the primary content of the page', 'wp-accessibility-scanner'),
                'context'  => '',
            ]);
        }
    }
}
