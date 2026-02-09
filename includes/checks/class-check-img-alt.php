<?php
/**
 * Check: Missing image alt text
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Img_Alt
 */
class ASFW_Check_Img_Alt extends ASFW_Check_Base {

    public function get_id(): string {
        return 'img-alt';
    }

    public function get_name(): string {
        return __('Image Alt Text', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '1.1.1';
    }

    public function get_level(): string {
        return 'A';
    }

    public function get_severity(): string {
        return 'critical';
    }

    public function get_category(): string {
        return 'images';
    }

    public function is_fixable(): bool {
        return true;
    }

    public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
        $images = $xpath->query('//img[not(@alt)]');
        if (!$images) {
            return;
        }

        foreach ($images as $img) {
            // Skip tracking pixels and tiny images.
            $width  = $img->getAttribute('width');
            $height = $img->getAttribute('height');
            if (($width && (int) $width <= 1) || ($height && (int) $height <= 1)) {
                continue;
            }

            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => $dom->saveHTML($img),
                'selector' => $this->get_selector($img),
                'message'  => __('Image is missing alt text', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add an alt attribute describing the image content', 'wp-accessibility-scanner'),
                'context'  => $this->get_context($img, $dom),
            ]);
        }
    }
}
