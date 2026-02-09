<?php
/**
 * Check: Empty table headers
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Empty_Th
 */
class ASFW_Check_Empty_Th extends ASFW_Check_Base {

	public function get_id(): string {
		return 'empty-th';
	}

	public function get_name(): string {
		return __('Empty Table Headers', 'wp-accessibility-scanner');
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
		return 'tables';
	}

	public function is_fixable(): bool {
		return false;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		$headers = $xpath->query('//th');
		if (!$headers) {
			return;
		}

		foreach ($headers as $asfw_th) {
			$text = trim($asfw_th->textContent);
			if ('' !== $text) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($asfw_th),
				'selector' => $this->get_selector($asfw_th),
				'message'  => __('Table header is empty', 'wp-accessibility-scanner'),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Add descriptive text to the table header cell', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($asfw_th, $dom),
			]);
		}
	}
}
