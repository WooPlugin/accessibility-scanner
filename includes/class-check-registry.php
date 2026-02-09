<?php
/**
 * Check registry
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Check_Registry
 */
class ASFW_Check_Registry {

    /** @var ASFW_Check_Interface[] */
    private static array $checks = [];

    /**
     * Register a check
     *
     * @param ASFW_Check_Interface $check The check instance.
     */
    public static function register(ASFW_Check_Interface $check): void {
        self::$checks[$check->get_id()] = $check;
    }

    /**
     * Get all checks, optionally filtered by WCAG level
     *
     * @param string $level Maximum WCAG level to include.
     * @return ASFW_Check_Interface[]
     */
    public static function get_checks(string $level = 'A'): array {
        $levels = ['A' => 1, 'AA' => 2, 'AAA' => 3];
        $max    = $levels[$level] ?? 1;

        return array_filter(self::$checks, function ($check) use ($levels, $max) {
            return ($levels[$check->get_level()] ?? 0) <= $max;
        });
    }

    /**
     * Get a specific check by ID
     *
     * @param string $id Check ID.
     * @return ASFW_Check_Interface|null
     */
    public static function get_check(string $id): ?ASFW_Check_Interface {
        return self::$checks[$id] ?? null;
    }
}
