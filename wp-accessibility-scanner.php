<?php
/**
 * Plugin Name: Accessibility Scanner – WCAG Compliance
 * Plugin URI: https://wooplugin.pro/accessibility-scanner
 * Description: Scan your WordPress site for WCAG 2.2 accessibility issues. Find problems, fix them, prove compliance. By WooPlugin.
 * Version: 1.0.0
 * Author: WooPlugin
 * Author URI: https://wooplugin.pro
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-accessibility-scanner
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

// Plugin constants.
define('ASFW_VERSION', '1.0.0');
define('ASFW_PLUGIN_FILE', __FILE__);
define('ASFW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ASFW_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ASFW_DB_VERSION', '1.0.0');

/**
 * Initialize the plugin
 */
function asfw_init() {
    // Include required files.
    require_once ASFW_PLUGIN_DIR . 'includes/class-database.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-check-interface.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-check-base.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-issue.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-scan-result.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-check-registry.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-scanner.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-admin.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-settings.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-rest-api.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-statement-generator.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-review-notice.php';
    require_once ASFW_PLUGIN_DIR . 'includes/class-quick-fix.php';

    // Register all checks.
    asfw_register_checks();

    // Initialize components.
    ASFW_Admin::init();
    ASFW_Settings::init();
    ASFW_REST_API::init();
    ASFW_Statement_Generator::init();
    ASFW_Quick_Fix::init();
    ASFW_Review_Notice::init();
}
add_action('plugins_loaded', 'asfw_init');

/**
 * Register accessibility checks
 */
function asfw_register_checks() {
    $checks_dir = ASFW_PLUGIN_DIR . 'includes/checks/';
    foreach (glob($checks_dir . 'class-check-*.php') as $file) {
        require_once $file;
    }

    $check_classes = [
        'ASFW_Check_Img_Alt',
        'ASFW_Check_Img_Alt_Empty',
        'ASFW_Check_Form_Labels',
        'ASFW_Check_Document_Lang',
        'ASFW_Check_Empty_Links',
        'ASFW_Check_Empty_Buttons',
        'ASFW_Check_Page_Title',
        'ASFW_Check_Duplicate_IDs',
        'ASFW_Check_Heading_Structure',
        'ASFW_Check_Iframe_Title',
        'ASFW_Check_Landmarks',
        'ASFW_Check_Autoplay_Media',
        'ASFW_Check_Table_Headers',
        'ASFW_Check_Tabindex',
        'ASFW_Check_Skip_Nav',
        'ASFW_Check_Title_Redundant',
        'ASFW_Check_Empty_Th',
        'ASFW_Check_Aria_References',
        'ASFW_Check_Aria_Roles',
        'ASFW_Check_Link_New_Window',
        'ASFW_Check_Color_Contrast',
        'ASFW_Check_Color_Contrast_Large',
    ];

    foreach ($check_classes as $class) {
        if (class_exists($class)) {
            ASFW_Check_Registry::register(new $class());
        }
    }
}

/**
 * Activation hook
 */
function asfw_activate() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-database.php';
    ASFW_Database::create_tables();

    if (!get_option('asfw_installed_time')) {
        update_option('asfw_installed_time', time());
    }

    if (!get_option('asfw_total_scans')) {
        update_option('asfw_total_scans', 0);
    }
}
register_activation_hook(__FILE__, 'asfw_activate');

/**
 * Deactivation hook
 */
function asfw_deactivate() {
    wp_clear_scheduled_hook('asfw_scheduled_scan');
}
register_deactivation_hook(__FILE__, 'asfw_deactivate');

/**
 * Add action links to plugins page
 */
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function ($links) {
    $upgrade_url = add_query_arg([
        'utm_source'   => 'plugin',
        'utm_medium'   => 'plugins-page',
        'utm_campaign' => 'free-to-pro',
    ], 'https://wooplugin.pro/accessibility-scanner#pricing');

    $custom_links = [
        'upgrade'  => '<a href="' . esc_url($upgrade_url) . '" target="_blank" style="color: #16a34a; font-weight: 600;">' .
            esc_html__('Upgrade to Pro', 'wp-accessibility-scanner') . '</a>',
        'settings' => '<a href="' . esc_url(admin_url('admin.php?page=asfw-settings')) . '">' .
            esc_html__('Settings', 'wp-accessibility-scanner') . '</a>',
        'docs'     => '<a href="https://wooplugin.pro/docs/accessibility/getting-started" target="_blank">' .
            esc_html__('Docs', 'wp-accessibility-scanner') . '</a>',
        'support'  => '<a href="https://wordpress.org/support/plugin/wp-accessibility-scanner/" target="_blank">' .
            esc_html__('Support', 'wp-accessibility-scanner') . '</a>',
    ];

    return array_merge($custom_links, $links);
});
