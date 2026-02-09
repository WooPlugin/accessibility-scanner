<?php
/**
 * Check: Missing document language
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Document_Lang
 */
class ASFW_Check_Document_Lang extends ASFW_Check_Base {

    public function get_id(): string {
        return 'document-lang';
    }

    public function get_name(): string {
        return __('Document Language', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '3.1.1';
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
        return true;
    }

    public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
        $html_elements = $xpath->query('//html');
        if (!$html_elements || $html_elements->length === 0) {
            return;
        }

        $html = $html_elements->item(0);
        $lang = $html->getAttribute('lang');

        if (empty($lang)) {
            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => '<html>',
                'selector' => 'html',
                'message'  => __('Document is missing a lang attribute on the <html> element', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add a lang attribute to the <html> element, e.g. <html lang="en">', 'wp-accessibility-scanner'),
                'context'  => '<html>',
            ]);
        }
    }
}
