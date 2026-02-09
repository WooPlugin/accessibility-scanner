<?php
/**
 * Check: Color contrast for large text
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Color_Contrast_Large
 */
class ASFW_Check_Color_Contrast_Large extends ASFW_Check_Base {

	/**
	 * Minimum contrast ratio for large text (WCAG AA).
	 *
	 * @var float
	 */
	private const MIN_RATIO = 3.0;

	/**
	 * Large text threshold in pixels (18pt = 24px).
	 *
	 * @var float
	 */
	private const LARGE_TEXT_PX = 24.0;

	/**
	 * Large bold text threshold in pixels (14pt = 18.66px).
	 *
	 * @var float
	 */
	private const LARGE_BOLD_TEXT_PX = 18.66;

	public function get_id(): string {
		return 'color-contrast-large';
	}

	public function get_name(): string {
		return __('Color Contrast (Large Text)', 'wp-accessibility-scanner');
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

			// Must have font-size in inline styles to determine large text.
			$font_size = $this->extract_style_property($style, 'font-size');
			if (null === $font_size) {
				continue;
			}

			if (!$this->is_large_text($style)) {
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
					__('Color contrast ratio is %1$.2f:1, which is below the required %2$.1f:1 for large text', 'wp-accessibility-scanner'),
					$ratio,
					self::MIN_RATIO
				),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Increase the contrast between the text color and background color to at least 3:1 for large text', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($asfw_element, $dom),
			]);
		}
	}

	/**
	 * Determine if the element qualifies as large text based on inline styles
	 *
	 * Large text is 18pt (24px) or larger, or 14pt (18.66px) and bold.
	 *
	 * @param string $style The inline style string.
	 * @return bool
	 */
	private function is_large_text(string $style): bool {
		$font_size_value = $this->extract_style_property($style, 'font-size');
		if (null === $font_size_value) {
			return false;
		}

		$size_px = $this->parse_font_size_to_px($font_size_value);
		if (null === $size_px) {
			return false;
		}

		// 18pt (24px) or larger is always large text.
		if ($size_px >= self::LARGE_TEXT_PX) {
			return true;
		}

		// 14pt (18.66px) and bold is large text.
		if ($size_px >= self::LARGE_BOLD_TEXT_PX) {
			$font_weight = $this->extract_style_property($style, 'font-weight');
			if (null !== $font_weight) {
				$weight = strtolower(trim($font_weight));
				if ('bold' === $weight || 'bolder' === $weight || (is_numeric($weight) && (int) $weight >= 700)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Parse a font-size value to pixels
	 *
	 * @param string $value The font-size CSS value.
	 * @return float|null The size in pixels or null if unparsable.
	 */
	private function parse_font_size_to_px(string $value): ?float {
		$value = strtolower(trim($value));

		if (preg_match('/^([\d.]+)\s*px$/', $value, $matches)) {
			return (float) $matches[1];
		}

		if (preg_match('/^([\d.]+)\s*pt$/', $value, $matches)) {
			// 1pt = 1.333px.
			return (float) $matches[1] * 1.333;
		}

		if (preg_match('/^([\d.]+)\s*em$/', $value, $matches)) {
			// Assume 1em = 16px (browser default).
			return (float) $matches[1] * 16.0;
		}

		if (preg_match('/^([\d.]+)\s*rem$/', $value, $matches)) {
			// Assume 1rem = 16px (browser default).
			return (float) $matches[1] * 16.0;
		}

		return null;
	}

	/**
	 * Extract a CSS property value from an inline style string
	 *
	 * @param string $style    The inline style string.
	 * @param string $property The CSS property name.
	 * @return string|null The property value or null if not found.
	 */
	private function extract_style_property(string $style, string $property): ?string {
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
