<?php
/**
 * Check: Invalid ARIA roles
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Aria_Roles
 */
class ASFW_Check_Aria_Roles extends ASFW_Check_Base {

	/**
	 * Valid WAI-ARIA roles.
	 *
	 * @var array
	 */
	private const VALID_ROLES = [
		'alert',
		'alertdialog',
		'application',
		'article',
		'banner',
		'button',
		'cell',
		'checkbox',
		'columnheader',
		'combobox',
		'complementary',
		'contentinfo',
		'definition',
		'dialog',
		'directory',
		'document',
		'feed',
		'figure',
		'form',
		'grid',
		'gridcell',
		'group',
		'heading',
		'img',
		'link',
		'list',
		'listbox',
		'listitem',
		'log',
		'main',
		'marquee',
		'math',
		'menu',
		'menubar',
		'menuitem',
		'menuitemcheckbox',
		'menuitemradio',
		'navigation',
		'none',
		'note',
		'option',
		'presentation',
		'progressbar',
		'radio',
		'radiogroup',
		'region',
		'row',
		'rowgroup',
		'rowheader',
		'scrollbar',
		'search',
		'searchbox',
		'separator',
		'slider',
		'spinbutton',
		'status',
		'switch',
		'tab',
		'table',
		'tablist',
		'tabpanel',
		'term',
		'textbox',
		'timer',
		'toolbar',
		'tooltip',
		'tree',
		'treegrid',
		'treeitem',
	];

	public function get_id(): string {
		return 'aria-roles';
	}

	public function get_name(): string {
		return __('Invalid ARIA Roles', 'wp-accessibility-scanner');
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
		$elements = $xpath->query('//*[@role]');
		if (!$elements) {
			return;
		}

		foreach ($elements as $asfw_element) {
			$role = strtolower(trim($asfw_element->getAttribute('role')));
			if ('' === $role) {
				continue;
			}

			if (in_array($role, self::VALID_ROLES, true)) {
				continue;
			}

			$issues[] = new ASFW_Issue([
				'check_id' => $this->get_id(),
				'element'  => $dom->saveHTML($asfw_element),
				'selector' => $this->get_selector($asfw_element),
				'message'  => sprintf(
					/* translators: %s: the invalid role value */
					__('Element has an invalid ARIA role: "%s"', 'wp-accessibility-scanner'),
					$role
				),
				'impact'   => $this->get_severity(),
				'wcag'     => $this->get_wcag(),
				'fix_hint' => __('Use a valid WAI-ARIA role or remove the role attribute', 'wp-accessibility-scanner'),
				'context'  => $this->get_context($asfw_element, $dom),
			]);
		}
	}
}
