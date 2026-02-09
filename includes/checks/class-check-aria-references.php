<?php
/**
 * Check: Broken ARIA references
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Aria_References
 */
class ASFW_Check_Aria_References extends ASFW_Check_Base {

	public function get_id(): string {
		return 'aria-references';
	}

	public function get_name(): string {
		return __('Broken ARIA References', 'wp-accessibility-scanner');
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
		return 'aria';
	}

	public function is_fixable(): bool {
		return false;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		$elements = $xpath->query('//*[@aria-labelledby or @aria-describedby]');
		if (!$elements) {
			return;
		}

		foreach ($elements as $asfw_element) {
			$missing_ids = [];

			foreach (['aria-labelledby', 'aria-describedby'] as $asfw_attr) {
				$value = $asfw_element->getAttribute($asfw_attr);
				if ('' === $value) {
					continue;
				}

				$ref_ids = preg_split('/\s+/', trim($value));
				foreach ($ref_ids as $asfw_ref_id) {
					if ('' === $asfw_ref_id) {
						continue;
					}

					$referenced = $xpath->query('//*[@id="' . $asfw_ref_id . '"]');
					if (!$referenced || 0 === $referenced->length) {
						$missing_ids[] = $asfw_ref_id;
					}
				}
			}

			if (empty($missing_ids)) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($asfw_element),
				'selector' => $this->get_selector($asfw_element),
				'message'  => sprintf(
					/* translators: %s: comma-separated list of missing IDs */
					__('ARIA attribute references missing ID(s): %s', 'wp-accessibility-scanner'),
					implode(', ', $missing_ids)
				),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Ensure all IDs referenced by aria-labelledby and aria-describedby exist in the document', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($asfw_element, $dom),
			]);
		}
	}
}
