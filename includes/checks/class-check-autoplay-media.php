<?php
/**
 * Check: Auto-playing media
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Autoplay_Media
 */
class ASFW_Check_Autoplay_Media extends ASFW_Check_Base {

	public function get_id(): string {
		return 'autoplay-media';
	}

	public function get_name(): string {
		return __('Auto-playing Media', 'wp-accessibility-scanner');
	}

	public function get_wcag(): string {
		return '1.4.2';
	}

	public function get_level(): string {
		return 'A';
	}

	public function get_severity(): string {
		return 'serious';
	}

	public function get_category(): string {
		return 'media';
	}

	public function is_fixable(): bool {
		return false;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		$elements = $xpath->query('//video[@autoplay] | //audio[@autoplay]');
		if (!$elements) {
			return;
		}

		foreach ($elements as $element) {
			$tag_name = strtolower($element->tagName);

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($element),
				'selector' => $this->get_selector($element),
				'message'  => sprintf(
					/* translators: %s: HTML element name (video or audio) */
					__('%s element is set to auto-play', 'wp-accessibility-scanner'),
					'<' . $tag_name . '>'
				),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Remove the autoplay attribute or provide a mechanism to pause or stop the media', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($element, $dom),
			]);
		}
	}
}
