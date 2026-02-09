<?php
/**
 * Check: Redundant title attributes
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Title_Redundant
 */
class ASFW_Check_Title_Redundant extends ASFW_Check_Base {

	public function get_id(): string {
		return 'title-redundant';
	}

	public function get_name(): string {
		return __('Redundant Title Attributes', 'wp-accessibility-scanner');
	}

	public function get_wcag(): string {
		return 'advisory';
	}

	public function get_level(): string {
		return 'A';
	}

	public function get_severity(): string {
		return 'minor';
	}

	public function get_category(): string {
		return 'structure';
	}

	public function is_fixable(): bool {
		return true;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		$elements = $xpath->query('//*[@title]');
		if (!$elements) {
			return;
		}

		foreach ($elements as $element) {
			$title = strtolower(trim($element->getAttribute('title')));
			if ('' === $title) {
				continue;
			}

			$is_redundant = false;

			// Compare with visible text content.
			$text_content = strtolower(trim($element->textContent));
			if ($text_content && $text_content === $title) {
				$is_redundant = true;
			}

			// Compare with aria-label.
			if (!$is_redundant) {
				$aria_label = strtolower(trim($element->getAttribute('aria-label')));
				if ($aria_label && $aria_label === $title) {
					$is_redundant = true;
				}
			}

			if (!$is_redundant) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($element),
				'selector' => $this->get_selector($element),
				'message'  => __('Element has a title attribute that duplicates its visible text or aria-label', 'wp-accessibility-scanner'),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Remove the redundant title attribute, or provide additional useful information in it', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($element, $dom),
			]);
		}
	}
}
