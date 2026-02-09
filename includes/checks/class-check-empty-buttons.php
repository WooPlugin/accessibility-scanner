<?php
/**
 * Check: Empty buttons
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Empty_Buttons
 */
class ASFW_Check_Empty_Buttons extends ASFW_Check_Base {

    public function get_id(): string {
        return 'empty-buttons';
    }

    public function get_name(): string {
        return __('Empty Buttons', 'wp-accessibility-scanner');
    }

    public function get_wcag(): string {
        return '4.1.2';
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
        // Check <button> elements and elements with role="button".
        $buttons = $xpath->query('//button | //*[@role="button"]');
        if (!$buttons) {
            return;
        }

        foreach ($buttons as $button) {
            // Check for accessible name.
            if (trim($button->textContent)) {
                continue;
            }
            if (trim($button->getAttribute('aria-label'))) {
                continue;
            }
            if (trim($button->getAttribute('aria-labelledby'))) {
                continue;
            }
            if (trim($button->getAttribute('title'))) {
                continue;
            }
            if (trim($button->getAttribute('value')) && $button->tagName === 'input') {
                continue;
            }

            // Check for images with alt inside.
            $imgs = $xpath->query('.//img[@alt]', $button);
            if ($imgs && $imgs->length > 0) {
                $has_alt = false;
                foreach ($imgs as $img) {
                    if (trim($img->getAttribute('alt'))) {
                        $has_alt = true;
                        break;
                    }
                }
                if ($has_alt) {
                    continue;
                }
            }

            // Check for SVG with title.
            $svgs = $xpath->query('.//svg/title', $button);
            if ($svgs && $svgs->length > 0) {
                continue;
            }

            $issues[] = new ASFW_Issue([
                'check_id' => $this->get_id(),
                'element'  => $dom->saveHTML($button),
                'selector' => $this->get_selector($button),
                'message'  => __('Button has no accessible name', 'wp-accessibility-scanner'),
                'impact'   => $this->get_severity(),
                'wcag'     => $this->get_wcag(),
                'fix_hint' => __('Add text content, aria-label, or title to the button', 'wp-accessibility-scanner'),
                'context'  => $this->get_context($button, $dom),
            ]);
        }
    }
}
