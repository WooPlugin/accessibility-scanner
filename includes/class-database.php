<?php
/**
 * Database table creation
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Database
 */
class ASFW_Database {

    /**
     * Create custom tables using dbDelta
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $scans_table = "CREATE TABLE {$wpdb->prefix}asfw_scans (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            url TEXT NOT NULL,
            scan_type VARCHAR(20) NOT NULL DEFAULT 'single',
            status VARCHAR(20) NOT NULL DEFAULT 'pending',
            score TINYINT UNSIGNED DEFAULT NULL,
            total_issues INT UNSIGNED NOT NULL DEFAULT 0,
            critical_count INT UNSIGNED NOT NULL DEFAULT 0,
            serious_count INT UNSIGNED NOT NULL DEFAULT 0,
            moderate_count INT UNSIGNED NOT NULL DEFAULT 0,
            minor_count INT UNSIGNED NOT NULL DEFAULT 0,
            pages_scanned INT UNSIGNED NOT NULL DEFAULT 1,
            pages_total INT UNSIGNED NOT NULL DEFAULT 1,
            progress TINYINT UNSIGNED NOT NULL DEFAULT 0,
            scan_duration FLOAT DEFAULT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            completed_at DATETIME DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";

        $issues_table = "CREATE TABLE {$wpdb->prefix}asfw_issues (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            scan_id BIGINT UNSIGNED NOT NULL,
            page_url TEXT NOT NULL,
            check_id VARCHAR(50) NOT NULL,
            element_html TEXT DEFAULT NULL,
            selector VARCHAR(500) DEFAULT NULL,
            message TEXT NOT NULL,
            impact VARCHAR(20) NOT NULL DEFAULT 'moderate',
            wcag_criterion VARCHAR(20) DEFAULT NULL,
            wcag_level CHAR(3) DEFAULT NULL,
            fix_hint TEXT DEFAULT NULL,
            context TEXT DEFAULT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'open',
            fixed_at DATETIME DEFAULT NULL,
            PRIMARY KEY  (id),
            KEY scan_id (scan_id),
            KEY check_id (check_id),
            KEY impact (impact),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($scans_table);
        dbDelta($issues_table);

        update_option('asfw_db_version', ASFW_DB_VERSION);
    }
}
