<?php
/**
 * Check: Positive tabindex
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Tabindex
 */
class ASFW_Check_Tabindex extends ASFW_Check_Base {

	public function get_id(): string {
		return 'tabindex';
	}

	public function get_name(): string {
		return __('Positive Tabindex', 'wp-accessibility-scanner');
	}

	public function get_wcag(): string {
		return '2.4.3';
	}

	public function get_level(): string {
		return 'A';
	}

	public function get_severity(): string {
		return 'moderate';
	}

	public function get_category(): string {
		return 'navigation';
	}

	public function is_fixable(): bool {
		return true;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		$elements = $xpath->query('//*[@tabindex]');
		if (!$elements) {
			return;
		}

		foreach ($elements as $element) {
			$tabindex = (int) $element->getAttribute('tabindex');
			if ($tabindex <= 0) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($element),
				'selector' => $this->get_selector($element),
				'message'  => sprintf(
					/* translators: %d: tabindex value */
					__('Element has a positive tabindex value of %d, which disrupts natural tab order', 'wp-accessibility-scanner'),
					$tabindex
				),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Remove the tabindex attribute or set it to 0 or -1 to maintain natural document tab order', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($element, $dom),
			]);
		}
	}
}
