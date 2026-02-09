<?php
/**
 * Quick fix handlers for accessibility issues
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Quick_Fix
 */
class ASFW_Quick_Fix {

    /**
     * Initialize quick fix AJAX handlers
     */
    public static function init() {
        add_action('wp_ajax_asfw_dismiss_issue', [__CLASS__, 'ajax_dismiss_issue']);
        add_action('wp_ajax_asfw_fix_issue', [__CLASS__, 'ajax_fix_issue']);
    }

    /**
     * AJAX handler: Dismiss/ignore an issue
     */
    public static function ajax_dismiss_issue() {
        check_ajax_referer('asfw_fix_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied.', 'wp-accessibility-scanner'));
        }

        $issue_id = isset($_POST['issue_id']) ? absint($_POST['issue_id']) : 0;
        if (!$issue_id) {
            wp_send_json_error(__('Invalid issue ID.', 'wp-accessibility-scanner'));
        }

        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $updated = $wpdb->update(
            $wpdb->prefix . 'asfw_issues',
            [
                'status'   => 'ignored',
                'fixed_at' => current_time('mysql'),
            ],
            ['id' => $issue_id],
            ['%s', '%s'],
            ['%d']
        );

        if (false === $updated) {
            wp_send_json_error(__('Failed to update issue.', 'wp-accessibility-scanner'));
        }

        wp_send_json_success([
            'issue_id' => $issue_id,
            'status'   => 'ignored',
        ]);
    }

    /**
     * AJAX handler: Mark an issue as fixed
     */
    public static function ajax_fix_issue() {
        check_ajax_referer('asfw_fix_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied.', 'wp-accessibility-scanner'));
        }

        $issue_id = isset($_POST['issue_id']) ? absint($_POST['issue_id']) : 0;
        if (!$issue_id) {
            wp_send_json_error(__('Invalid issue ID.', 'wp-accessibility-scanner'));
        }

        global $wpdb;

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery
        $updated = $wpdb->update(
            $wpdb->prefix . 'asfw_issues',
            [
                'status'   => 'fixed',
                'fixed_at' => current_time('mysql'),
            ],
            ['id' => $issue_id],
            ['%s', '%s'],
            ['%d']
        );

        if (false === $updated) {
            wp_send_json_error(__('Failed to update issue.', 'wp-accessibility-scanner'));
        }

        wp_send_json_success([
            'issue_id' => $issue_id,
            'status'   => 'fixed',
        ]);
    }
}
