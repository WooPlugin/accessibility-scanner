<?php
/**
 * Check: Table headers
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Table_Headers
 */
class ASFW_Check_Table_Headers extends ASFW_Check_Base {

	public function get_id(): string {
		return 'table-headers';
	}

	public function get_name(): string {
		return __('Table Headers', 'wp-accessibility-scanner');
	}

	public function get_wcag(): string {
		return '1.3.1';
	}

	public function get_level(): string {
		return 'A';
	}

	public function get_severity(): string {
		return 'serious';
	}

	public function get_category(): string {
		return 'tables';
	}

	public function is_fixable(): bool {
		return false;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		$tables = $xpath->query('//table');
		if (!$tables) {
			return;
		}

		foreach ($tables as $table) {
			// Check if the table has data cells.
			$td_cells = $xpath->query('.//td', $table);
			if (!$td_cells || 0 === $td_cells->length) {
				continue;
			}

			// Check if the table has header cells.
			$th_cells = $xpath->query('.//th', $table);
			if ($th_cells && $th_cells->length > 0) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($table),
				'selector' => $this->get_selector($table),
				'message'  => __('Data table is missing header cells', 'wp-accessibility-scanner'),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Add <th> elements to identify row and column headers in the table', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($table, $dom),
			]);
		}
	}
}
