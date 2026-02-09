<?php
/**
 * Check: Links opening in new window without warning
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Link_New_Window
 */
class ASFW_Check_Link_New_Window extends ASFW_Check_Base {

	public function get_id(): string {
		return 'link-new-window';
	}

	public function get_name(): string {
		return __('Links Opening in New Window', 'wp-accessibility-scanner');
	}

	public function get_wcag(): string {
		return '2.4.4';
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
		$links = $xpath->query('//a[@target="_blank"]');
		if (!$links) {
			return;
		}

		foreach ($links as $asfw_link) {
			if ($this->has_new_window_warning($asfw_link, $xpath)) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($asfw_link),
				'selector' => $this->get_selector($asfw_link),
				'message'  => __('Link opens in a new window without warning', 'wp-accessibility-scanner'),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Add screen reader text such as "(opens in a new tab)" or an aria-label indicating the link opens in a new window', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($asfw_link, $dom),
			]);
		}
	}

	/**
	 * Check if a link indicates it opens in a new window
	 *
	 * @param DOMElement $link  The link element.
	 * @param DOMXPath   $xpath XPath instance.
	 * @return bool
	 */
	private function has_new_window_warning(DOMElement $link, DOMXPath $xpath): bool {
		$warning_patterns = [
			'new window',
			'new tab',
			'opens in',
			'external',
		];

		// Check link text content.
		$text = strtolower(trim($link->textContent));
		foreach ($warning_patterns as $asfw_pattern) {
			if (false !== strpos($text, $asfw_pattern)) {
				return true;
			}
		}

		// Check aria-label.
		$aria_label = strtolower(trim($link->getAttribute('aria-label')));
		if ('' !== $aria_label) {
			foreach ($warning_patterns as $asfw_pattern) {
				if (false !== strpos($aria_label, $asfw_pattern)) {
					return true;
				}
			}
		}

		// Check for screen reader text children.
		$sr_elements = $xpath->query(
			'.//*[contains(@class, "screen-reader-text") or contains(@class, "sr-only")]',
			$link
		);
		if ($sr_elements && $sr_elements->length > 0) {
			foreach ($sr_elements as $asfw_sr) {
				$sr_text = strtolower(trim($asfw_sr->textContent));
				foreach ($warning_patterns as $asfw_pattern) {
					if (false !== strpos($sr_text, $asfw_pattern)) {
						return true;
					}
				}
			}
		}

		return false;
	}
}
