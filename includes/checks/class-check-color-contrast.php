<?php
/**
 * Check: Color contrast for normal text
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Color_Contrast
 */
class ASFW_Check_Color_Contrast extends ASFW_Check_Base {

	/**
	 * Minimum contrast ratio for normal text (WCAG AA).
	 *
	 * @var float
	 */
	private const MIN_RATIO = 4.5;

	public function get_id(): string {
		return 'color-contrast';
	}

	public function get_name(): string {
		return __('Color Contrast (Normal Text)', 'wp-accessibility-scanner');
	}

	public function get_wcag(): string {
		return '1.4.3';
	}

	public function get_level(): string {
		return 'A';
	}

	public function get_severity(): string {
		return 'serious';
	}

	public function get_category(): string {
		return 'color';
	}

	public function is_fixable(): bool {
		return false;
	}

	public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void {
		$elements = $xpath->query('//*[contains(@style, "color")]');
		if (!$elements) {
			return;
		}

		foreach ($elements as $asfw_element) {
			$style = $asfw_element->getAttribute('style');
			if ('' === $style) {
				continue;
			}

			$fg_color = $this->extract_style_property($style, 'color');
			$bg_color = $this->extract_style_property($style, 'background-color');

			// Also check shorthand background property for color.
			if (null === $bg_color) {
				$bg_color = $this->extract_style_property($style, 'background');
			}

			// Only flag when both foreground and background are specified inline.
			if (null === $fg_color || null === $bg_color) {
				continue;
			}

			$fg_rgb = $this->parse_color($fg_color);
			$bg_rgb = $this->parse_color($bg_color);

			if (null === $fg_rgb || null === $bg_rgb) {
				continue;
			}

			$fg_luminance = $this->relative_luminance($fg_rgb);
			$bg_luminance = $this->relative_luminance($bg_rgb);
			$ratio        = $this->contrast_ratio($fg_luminance, $bg_luminance);

			if ($ratio >= self::MIN_RATIO) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($asfw_element),
				'selector' => $this->get_selector($asfw_element),
				'message'  => sprintf(
					/* translators: 1: contrast ratio, 2: required ratio */
					__('Color contrast ratio is %1$.2f:1, which is below the required %2$.1f:1 for normal text', 'wp-accessibility-scanner'),
					$ratio,
					self::MIN_RATIO
				),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Increase the contrast between the text color and background color to at least 4.5:1', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($asfw_element, $dom),
			]);
		}
	}

	/**
	 * Extract a CSS property value from an inline style string
	 *
	 * @param string $style    The inline style string.
	 * @param string $property The CSS property name.
	 * @return string|null The property value or null if not found.
	 */
	private function extract_style_property(string $style, string $property): ?string {
		// Use a regex that matches the property name as a whole word.
		// For "color", we need to avoid matching "background-color".
		if ('color' === $property) {
			$pattern = '/(?<![a-z-])color\s*:\s*([^;]+)/i';
		} else {
			$pattern = '/' . preg_quote($property, '/') . '\s*:\s*([^;]+)/i';
		}

		if (preg_match($pattern, $style, $matches)) {
			return trim($matches[1]);
		}

		return null;
	}

	/**
	 * Parse a CSS color value to an RGB array
	 *
	 * Supports hex (#fff, #ffffff) and rgb(r, g, b) formats.
	 *
	 * @param string $color The CSS color value.
	 * @return array|null Array of [r, g, b] integers or null if unparsable.
	 */
	private function parse_color(string $color): ?array {
		$color = trim(strtolower($color));

		// Hex format: #fff or #ffffff.
		if (preg_match('/^#([0-9a-f]{3,8})$/', $color, $matches)) {
			$hex = $matches[1];

			if (3 === strlen($hex)) {
				$r = hexdec($hex[0] . $hex[0]);
				$g = hexdec($hex[1] . $hex[1]);
				$b = hexdec($hex[2] . $hex[2]);
				return [$r, $g, $b];
			}

			if (6 === strlen($hex) || 8 === strlen($hex)) {
				$r = hexdec(substr($hex, 0, 2));
				$g = hexdec(substr($hex, 2, 2));
				$b = hexdec(substr($hex, 4, 2));
				return [$r, $g, $b];
			}
		}

		// rgb(r, g, b) format.
		if (preg_match('/^rgb\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*\)/', $color, $matches)) {
			return [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
		}

		// rgba(r, g, b, a) format - ignore alpha.
		if (preg_match('/^rgba\s*\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*[\d.]+\s*\)/', $color, $matches)) {
			return [(int) $matches[1], (int) $matches[2], (int) $matches[3]];
		}

		return null;
	}

	/**
	 * Calculate relative luminance per WCAG 2.0 formula
	 *
	 * @param array $rgb Array of [r, g, b] integers (0-255).
	 * @return float Relative luminance value.
	 */
	private function relative_luminance(array $rgb): float {
		$channels = [];
		foreach ($rgb as $asfw_channel) {
			$srgb = $asfw_channel / 255;
			$channels[] = ($srgb <= 0.03928)
				? $srgb / 12.92
				: pow(($srgb + 0.055) / 1.055, 2.4);
		}

		return 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
	}

	/**
	 * Calculate contrast ratio between two luminance values
	 *
	 * @param float $l1 First luminance value.
	 * @param float $l2 Second luminance value.
	 * @return float Contrast ratio.
	 */
	private function contrast_ratio(float $l1, float $l2): float {
		$lighter = max($l1, $l2);
		$darker  = min($l1, $l2);

		return ($lighter + 0.05) / ($darker + 0.05);
	}
}
