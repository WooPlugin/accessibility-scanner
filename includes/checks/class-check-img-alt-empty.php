<?php
/**
 * Check: Empty alt on informative images
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Img_Alt_Empty
 */
class ASFW_Check_Img_Alt_Empty extends ASFW_Check_Base {

	public function get_id(): string {
		return 'img-alt-empty';
	}

	public function get_name(): string {
		return __('Empty Alt on Informative Images', 'wp-accessibility-scanner');
	}

	public function get_wcag(): string {
		return '1.1.1';
	}

	public function get_level(): string {
		return 'A';
	}

	public function get_severity(): string {
		return 'serious';
	}

	public function get_category(): string {
		return 'images';
	}

	public function is_fixable(): bool {
		return true;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		$images = $xpath->query('//img[@alt=""]');
		if (!$images) {
			return;
		}

		// Keywords that suggest an image is informative, not decorative.
		$informative_keywords = ['logo', 'banner', 'hero', 'product', 'team', 'photo'];

		foreach ($images as $img) {
			$flagged = false;

			// Check if the src contains informative keywords.
			$src = strtolower($img->getAttribute('src'));
			foreach ($informative_keywords as $keyword) {
				if (str_contains($src, $keyword)) {
					$flagged = true;
					break;
				}
			}

			// Check if the image is inside an <a>, <figure>, or <article> tag.
			if (!$flagged) {
				$parent = $img->parentNode;
				while ($parent && $parent instanceof DOMElement) {
					$tag = strtolower($parent->tagName);
					if ('a' === $tag || 'figure' === $tag || 'article' === $tag) {
						$flagged = true;
						break;
					}
					$parent = $parent->parentNode;
				}
			}

			if (!$flagged) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($img),
				'selector' => $this->get_selector($img),
				'message'  => __('Image appears to be informative but has an empty alt attribute', 'wp-accessibility-scanner'),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Add descriptive alt text that conveys the image content, or ensure the image is truly decorative', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($img, $dom),
			]);
		}
	}
}
