<?php
/**
 * PHPUnit bootstrap - stubs WordPress functions and loads check classes.
 */

// Stub ABSPATH so `defined('ABSPATH') || exit` doesn't kill the process.
if (!defined('ABSPATH')) {
    define('ABSPATH', '/');
}

// Stub __() translation function.
if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

// Stub sprintf if needed (should exist in PHP, but kept for safety).
// sprintf is a core PHP function, no stub needed.

$includes = dirname(__DIR__) . '/includes';

require_once $includes . '/class-check-interface.php';
require_once $includes . '/class-check-base.php';
require_once $includes . '/class-issue.php';

// Load all check classes.
foreach (glob($includes . '/checks/class-check-*.php') as $file) {
    require_once $file;
}
