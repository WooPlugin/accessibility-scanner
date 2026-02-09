<?php
/**
 * Admin functionality
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Admin
 */
class ASFW_Admin {

    /**
     * Our page slugs
     *
     * @var string[]
     */
    private static array $our_pages = ['asfw-dashboard', 'asfw-scanner', 'asfw-issues', 'asfw-settings', 'asfw-license', 'asfw-upgrade-pro'];

    /**
     * Initialize admin
     */
    public static function init() {
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_scripts'], 999);
        add_action('admin_menu', [__CLASS__, 'add_menu']);
        add_action('admin_notices', [__CLASS__, 'render_pro_banner']);
        add_action('admin_notices', [__CLASS__, 'render_plugin_header']);

        // AJAX handlers.
        add_action('wp_ajax_asfw_start_scan', [__CLASS__, 'ajax_start_scan']);
    }

    /**
     * Enqueue admin scripts and styles
     *
     * @param string $hook Current admin page hook.
     */
    public static function enqueue_scripts($hook) {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

        $is_our_page = in_array($page, self::$our_pages, true)
            || strpos($hook, 'accessibility-scanner_page_') === 0
            || $hook === 'toplevel_page_asfw-dashboard';

        // Always enqueue CSS (menu badge needs it).
        wp_enqueue_style(
            'asfw-admin',
            ASFW_PLUGIN_URL . 'admin/css/admin.css',
            [],
            ASFW_VERSION
        );

        if ($is_our_page) {
            wp_add_inline_style('asfw-admin', '#contextual-help-link-wrap { display: none !important; }');
        }

        if (!$is_our_page) {
            return;
        }

        wp_enqueue_script(
            'asfw-admin',
            ASFW_PLUGIN_URL . 'admin/js/admin.js',
            [],
            ASFW_VERSION,
            true
        );

        $upgrade_url = add_query_arg([
            'utm_source'   => 'plugin',
            'utm_medium'   => 'menu',
            'utm_campaign' => 'free-to-pro',
        ], 'https://wooplugin.pro/accessibility-scanner-pro#pricing');

        wp_localize_script('asfw-admin', 'asfwScanner', [
            'ajaxUrl'        => admin_url('admin-ajax.php'),
            'nonce'          => wp_create_nonce('asfw_scan_nonce'),
            'fixNonce'       => wp_create_nonce('asfw_fix_nonce'),
            'statementNonce' => wp_create_nonce('asfw_statement_nonce'),
            'upgradeUrl'     => $upgrade_url,
            'strings'        => [
                'scanning'        => __('Scanning...', 'wp-accessibility-scanner'),
                'completed'       => __('Scan complete!', 'wp-accessibility-scanner'),
                'error'           => __('Error:', 'wp-accessibility-scanner'),
            ],
        ]);

        // Upgrade link script.
        wp_add_inline_script('asfw-admin', '(function() { var link = document.querySelector(\'a[href*="page=asfw-upgrade-pro"]\'); if (link) { link.href = asfwScanner.upgradeUrl; link.target = "_blank"; } })();');
    }

    /**
     * Add admin menu
     */
    public static function add_menu() {
        add_menu_page(
            __('Accessibility', 'wp-accessibility-scanner'),
            __('Accessibility', 'wp-accessibility-scanner'),
            'manage_options',
            'asfw-dashboard',
            [__CLASS__, 'render_dashboard'],
            'dashicons-universal-access-alt',
            57
        );

        add_submenu_page(
            'asfw-dashboard',
            __('Dashboard', 'wp-accessibility-scanner'),
            __('Dashboard', 'wp-accessibility-scanner'),
            'manage_options',
            'asfw-dashboard',
            [__CLASS__, 'render_dashboard']
        );

        add_submenu_page(
            'asfw-dashboard',
            __('Scanner', 'wp-accessibility-scanner'),
            __('Scanner', 'wp-accessibility-scanner'),
            'manage_options',
            'asfw-scanner',
            [__CLASS__, 'render_scanner']
        );

        add_submenu_page(
            'asfw-dashboard',
            __('Issues', 'wp-accessibility-scanner'),
            __('Issues', 'wp-accessibility-scanner'),
            'manage_options',
            'asfw-issues',
            [__CLASS__, 'render_issues']
        );

        add_submenu_page(
            'asfw-dashboard',
            __('Settings', 'wp-accessibility-scanner'),
            __('Settings', 'wp-accessibility-scanner'),
            'manage_options',
            'asfw-settings',
            [__CLASS__, 'render_settings']
        );

        add_submenu_page(
            'asfw-dashboard',
            __('License', 'wp-accessibility-scanner'),
            __('License', 'wp-accessibility-scanner'),
            'manage_options',
            'asfw-license',
            [__CLASS__, 'render_license']
        );

        add_submenu_page(
            'asfw-dashboard',
            __('Upgrade to Pro', 'wp-accessibility-scanner'),
            __('Upgrade to Pro', 'wp-accessibility-scanner') . ' <span class="asfw-menu-badge">Pro</span>',
            'manage_options',
            'asfw-upgrade-pro',
            '__return_null'
        );
    }

    /**
     * Render Pro banner
     */
    public static function render_pro_banner() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if (!in_array($page, self::$our_pages, true)) {
            return;
        }

        $upgrade_url = add_query_arg([
            'utm_source'   => 'plugin',
            'utm_medium'   => 'top-banner',
            'utm_campaign' => 'free-to-pro',
        ], 'https://wooplugin.pro/accessibility-scanner-pro#pricing');
        ?>
        <div class="asfw-pro-banner">
            <?php
            printf(
                /* translators: %s: upgrade link */
                esc_html__("You're using Accessibility Scanner FREE VERSION. To unlock more features consider %s.", 'wp-accessibility-scanner'),
                '<a href="' . esc_url($upgrade_url) . '" target="_blank">' . esc_html__('upgrading to Pro', 'wp-accessibility-scanner') . '</a>'
            );
            ?>
        </div>
        <?php
    }

    /**
     * Render plugin header bar
     */
    public static function render_plugin_header() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
        if (!in_array($page, self::$our_pages, true)) {
            return;
        }

        $upgrade_url = add_query_arg([
            'utm_source'   => 'plugin',
            'utm_medium'   => 'header',
            'utm_campaign' => 'free-to-pro',
        ], 'https://wooplugin.pro/accessibility-scanner-pro#pricing');
        ?>
        <div class="asfw-plugin-header">
            <div class="asfw-plugin-header-left">
                <span class="asfw-plugin-header-logo">
                    <svg viewBox="0 0 612 612" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect width="612" height="612" rx="80" fill="#7f54b3"/>
                        <g transform="translate(-150, 750) scale(0.15, -0.15)" fill="white" stroke="none">
                            <path d="M2707 4519 c-171 -26 -340 -212 -397 -435 -15 -59 -20 -111 -20 -221 0 -124 -3 -148 -20 -180 -24 -45 -24 -55 -5 -92 31 -61 121 -65 156 -8 25 43 24 70 -6 105 -24 28 -25 36 -25 153 0 154 18 241 70 345 103 204 272 289 418 210 77 -43 147 -122 192 -219 50 -108 60 -161 60 -323 0 -121 -2 -139 -20 -161 -22 -29 -26 -89 -7 -114 36 -47 97 -51 138 -10 33 33 37 70 14 115 -11 21 -15 62 -15 155 0 84 -7 156 -19 217 -61 296 -285 498 -514 463z"/>
                            <path d="M3279 4520 c-26 -5 -67 -17 -93 -28 -47 -21 -116 -69 -116 -81 0 -3 14 -21 30 -39 l30 -33 46 32 c48 33 119 59 160 59 56 0 139 -43 200 -104 91 -91 163 -258 164 -378 l0 -28 -221 0 -220 0 3 -52 3 -53 101 -3 101 -3 12 -307 c7 -169 18 -458 26 -642 41 -978 47 -1142 41 -1147 -3 -3 -354 22 -778 56 -577 46 -777 65 -787 76 -12 11 -8 89 23 517 20 277 41 571 47 653 35 506 57 774 63 784 4 6 35 11 77 11 l69 0 0 55 0 55 -79 0 c-64 0 -84 -4 -112 -23 -66 -44 -61 -5 -138 -1071 l-71 -980 21 -43 c13 -26 34 -50 52 -60 24 -12 134 -24 442 -49 225 -18 583 -47 795 -65 212 -17 414 -31 450 -31 54 0 106 13 315 82 154 50 259 90 274 103 56 52 56 36 -4 644 -31 307 -73 740 -95 963 -22 223 -45 420 -51 438 -6 18 -27 45 -47 60 -31 23 -46 27 -118 30 -90 4 -94 7 -94 77 0 51 -39 176 -78 250 -47 90 -145 193 -219 233 -51 27 -75 34 -158 46 -11 2 -41 0 -66 -4z m570 -1430 c28 -245 64 -593 82 -773 l33 -329 71 -40 c118 -68 136 -80 131 -85 -2 -3 -56 12 -119 33 l-114 37 -59 -45 c-75 -57 -181 -128 -190 -128 -5 0 43 52 105 114 111 113 113 115 107 153 -18 113 -116 1426 -115 1528 1 69 1 69 10 25 5 -25 31 -245 58 -490z"/>
                            <path d="M2926 4218 c-35 -75 -60 -163 -71 -250 l-6 -48 -215 0 -214 0 0 -55 0 -55 343 2 342 3 0 50 0 50 -74 3 -73 3 6 57 c7 65 26 130 51 184 17 35 17 37 -4 71 -42 68 -47 67 -85 -15z"/>
                        </g>
                    </svg>
                </span>
                <span class="asfw-plugin-header-name">WooPlugin</span>
                <a href="<?php echo esc_url($upgrade_url); ?>" class="asfw-upgrade-btn" target="_blank">
                    <?php esc_html_e('Upgrade to Pro', 'wp-accessibility-scanner'); ?>
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                </a>
            </div>
            <div class="asfw-plugin-header-right">
                <div class="asfw-help-dropdown">
                    <button type="button" class="asfw-header-btn asfw-help-toggle">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/></svg>
                        <?php esc_html_e('Help', 'wp-accessibility-scanner'); ?>
                    </button>
                    <div class="asfw-help-dropdown-menu">
                        <div class="asfw-help-section">
                            <h4><?php esc_html_e('Resources', 'wp-accessibility-scanner'); ?></h4>
                            <a href="https://wooplugin.pro/docs/accessibility" target="_blank">
                                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/></svg>
                                <?php esc_html_e('Documentation', 'wp-accessibility-scanner'); ?>
                            </a>
                            <a href="https://wordpress.org/support/plugin/wp-accessibility-scanner/" target="_blank">
                                <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-2 0c0 .993-.241 1.929-.668 2.754l-1.524-1.525a3.997 3.997 0 00.078-2.183l1.562-1.562C15.802 8.249 16 9.1 16 10zm-5.165 3.913l1.58 1.58A5.98 5.98 0 0110 16a5.976 5.976 0 01-2.516-.552l1.562-1.562a4.006 4.006 0 001.789.027zm-4.677-2.796a4.002 4.002 0 01-.041-2.08l-.08.08-1.53-1.533A5.98 5.98 0 004 10c0 .954.223 1.856.619 2.657l1.54-1.54zm1.088-6.45A5.974 5.974 0 0110 4c.954 0 1.856.223 2.657.619l-1.54 1.54a4.002 4.002 0 00-2.346.033L7.246 4.668zM12 10a2 2 0 11-4 0 2 2 0 014 0z" clip-rule="evenodd"/></svg>
                                <?php esc_html_e('Support', 'wp-accessibility-scanner'); ?>
                            </a>
                            <a href="https://wordpress.org/support/plugin/wp-accessibility-scanner/reviews/#new-post" target="_blank">
                                <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                <?php esc_html_e('Leave a Review', 'wp-accessibility-scanner'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * AJAX handler: Start a scan
     */
    public static function ajax_start_scan() {
        check_ajax_referer('asfw_scan_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied.', 'wp-accessibility-scanner'));
        }

        $url = isset($_POST['url']) ? esc_url_raw(wp_unslash($_POST['url'])) : '';
        if (empty($url)) {
            wp_send_json_error(__('Please enter a valid URL.', 'wp-accessibility-scanner'));
        }

        try {
            $result = ASFW_Scanner::scan($url);
            ASFW_Scanner::increment_scan_count();
            $scan_id = ASFW_Scanner::save_scan($result);

            wp_send_json_success([
                'scan_id'          => $scan_id,
                'score'            => $result->score,
                'total_issues'     => $result->get_total_issues(),
                'critical_count'   => $result->critical_count,
                'serious_count'    => $result->serious_count,
                'moderate_count'   => $result->moderate_count,
                'minor_count'      => $result->minor_count,
                'duration'         => round($result->duration, 2),
                'url'              => $result->url,
            ]);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * Render dashboard page
     */
    public static function render_dashboard() {
        include ASFW_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    /**
     * Render scanner page
     */
    public static function render_scanner() {
        include ASFW_PLUGIN_DIR . 'admin/views/scanner.php';
    }

    /**
     * Render issues page
     */
    public static function render_issues() {
        include ASFW_PLUGIN_DIR . 'admin/views/issues.php';
    }

    /**
     * Render settings page
     */
    public static function render_settings() {
        include ASFW_PLUGIN_DIR . 'admin/views/settings.php';
    }

    /**
     * Render license page
     */
    public static function render_license() {
        include ASFW_PLUGIN_DIR . 'admin/views/license.php';
    }
}
