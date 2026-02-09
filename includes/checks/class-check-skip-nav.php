<?php
/**
 * Check: Skip navigation link
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Skip_Nav
 */
class ASFW_Check_Skip_Nav extends ASFW_Check_Base {

	public function get_id(): string {
		return 'skip-nav';
	}

	public function get_name(): string {
		return __('Skip Navigation', 'wp-accessibility-scanner');
	}

	public function get_wcag(): string {
		return '2.4.1';
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
		return false;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		// Look for skip links among the first links in the body.
		$links = $xpath->query('//body//a[position() <= 10]');
		if (!$links) {
			// If no body or links found, check if body exists at all.
			$body = $xpath->query('//body');
			if (!$body || 0 === $body->length) {
				return;
			}
		}

		$skip_keywords = ['skip', 'main', 'content'];
		$has_skip_link = false;

		if ($links && $links->length > 0) {
			foreach ($links as $link) {
				$href = $link->getAttribute('href');
				if (!$href || '#' !== substr($href, 0, 1) || '#' === $href) {
					continue;
				}

				$text = strtolower(trim($link->textContent));
				foreach ($skip_keywords as $keyword) {
					if (str_contains($text, $keyword)) {
						$has_skip_link = true;
						break 2;
					}
				}

				// Also check aria-label for visually hidden skip links.
				$aria_label = strtolower(trim($link->getAttribute('aria-label')));
				if ($aria_label) {
					foreach ($skip_keywords as $keyword) {
						if (str_contains($aria_label, $keyword)) {
							$has_skip_link = true;
							break 2;
						}
					}
				}
			}
		}

		if ($has_skip_link) {
			return;
		}

		// Report the issue on the body element.
		$body = $xpath->query('//body');
		if (!$body || 0 === $body->length) {
			return;
		}

		$body_element = $body->item(0);

		$issues[] = new ASFW_Issue([
			'check_id' => $this->get_id(),
			'element'  => '<body>',
			'selector' => 'body',
			'message'  => __('Page is missing a skip navigation link', 'wp-accessibility-scanner'),
			'impact'   => $this->get_severity(),
			'wcag'     => $this->get_wcag(),
			'fix_hint' => __('Add a skip navigation link as the first focusable element in the body, e.g. <a href="#main-content">Skip to content</a>', 'wp-accessibility-scanner'),
			'context'  => $this->get_context($body_element, $dom),
		]);
	}
}
