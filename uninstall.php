<?php
/**
 * Uninstall handler
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('WP_UNINSTALL_PLUGIN') || exit;

global $wpdb;

// Drop custom tables.
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}asfw_issues"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}asfw_scans"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

// Delete all plugin options.
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'asfw\_%'"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
