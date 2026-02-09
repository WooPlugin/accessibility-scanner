<?php
/**
 * Check: Heading structure
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Heading_Structure
 */
class ASFW_Check_Heading_Structure extends ASFW_Check_Base {

    public function get_id(): string {
        return 'heading-structure';
    }

    public function get_name(): string {
        return __('Heading Structure', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '1.3.1';
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
        $headings = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //h6');
        if (!$headings) {
            return;
        }

        // Check for missing h1.
        $h1s = $xpath->query('//h1');
        if (!$h1s || $h1s->length === 0) {
            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => '',
                'selector' => 'h1',
                'message'  => __('Page is missing a first-level heading (h1)', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add an <h1> element to identify the main content of the page', 'wp-accessibility-scanner'),
                'context'  => '',
            ]);
        }

        // Check for skipped heading levels.
        $levels = [];
        foreach ($headings as $heading) {
            $level    = (int) substr($heading->tagName, 1);
            $levels[] = ['level' => $level, 'element' => $heading];
        }

        for ($i = 1; $i < count($levels); $i++) {
            $current  = $levels[$i]['level'];
            $previous = $levels[$i - 1]['level'];

            if ($current > $previous + 1) {
                $element = $levels[$i]['element'];
                $issues[] = new ASFW_Issue([
                    'check_id' => $this->get_id(),
                    'element'  => $dom->saveHTML($element),
                    'selector' => $this->get_selector($element),
                    'message'  => sprintf(
                        /* translators: 1: current heading level, 2: previous heading level */
                        __('Heading level h%1$d skips from h%2$d (should not skip levels)', 'wp-accessibility-scanner'),
                        $current,
                        $previous
                    ),
                    'impact'   => $this->get_severity(),
                    'wcag'     => $this->get_wcag(),
                    'fix_hint' => __('Use sequential heading levels without skipping (e.g., h2 should follow h1, not h3)', 'wp-accessibility-scanner'),
                    'context'  => $this->get_context($element, $dom),
                ]);
            }
        }
    }
}
