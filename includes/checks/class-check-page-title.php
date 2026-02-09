<?php
/**
 * Check: Missing page title
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Page_Title
 */
class ASFW_Check_Page_Title extends ASFW_Check_Base {

    public function get_id(): string {
        return 'page-title';
    }

    public function get_name(): string {
        return __('Page Title', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '2.4.2';
    }

    public function get_level(): string {
        return 'A';
    }

    public function get_severity(): string {
        return 'serious';
    }

    public function get_category(): string {
        return 'structure';
    }

    public function is_fixable(): bool {
        return false;
    }

    public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
        $titles = $xpath->query('//head/title');

        if (!$titles || $titles->length === 0 || !trim($titles->item(0)->textContent)) {
            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => '<title>',
                'selector' => 'head > title',
                'message'  => __('Page is missing a title or title is empty', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add a descriptive <title> element inside <head>', 'wp-accessibility-scanner'),
                'context'  => '<head>...</head>',
            ]);
        }
    }
}
