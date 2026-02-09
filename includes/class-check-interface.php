<?php
/**
 * Check interface definition
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Interface ASFW_Check_Interface
 */
interface ASFW_Check_Interface {

    /**
     * Get check ID
     *
     * @return string
     */
    public function get_id(): string;

    /**
     * Get human-readable name
     *
     * @return string
     */
    public function get_name(): string;

    /**
     * Get WCAG criterion
     *
     * @return string
     */
    public function get_wcag(): string;

    /**
     * Get WCAG level (A, AA, AAA)
     *
     * @return string
     */
    public function get_level(): string;

    /**
     * Get severity (critical, serious, moderate, minor)
     *
     * @return string
     */
    public function get_severity(): string;

    /**
     * Get category (images, forms, structure, navigation, aria)
     *
     * @return string
     */
    public function get_category(): string;

    /**
     * Whether this issue can be auto-fixed
     *
     * @return bool
     */
    public function is_fixable(): bool;

    /**
     * Run the check against the DOM
     *
     * @param DOMDocument $dom    The parsed DOM.
     * @param DOMXPath    $xpath  XPath query interface.
     * @param array       $issues Array of issues (passed by reference).
     */
    public function run(DOMDocument $dom, DOMXPath $xpath, array &$issues): void;
}
