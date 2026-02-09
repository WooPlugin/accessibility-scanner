<?php
/**
 * Scanner engine
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Scanner
 */
class ASFW_Scanner {

    /**
     * Scan a URL for accessibility issues
     *
     * @param string $url URL to scan.
     * @return ASFW_Scan_Result
     * @throws Exception If URL cannot be fetched.
     */
    public static function scan(string $url): ASFW_Scan_Result {
        $start = microtime(true);

        $html = self::fetch_page($url);

        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath  = new DOMXPath($dom);
        $issues = [];
        $checks = ASFW_Check_Registry::get_checks('A');

        $ignored = (array) get_option('asfw_ignored_checks', []);

        foreach ($checks as $check) {
            if (in_array($check->get_id(), $ignored, true)) {
                continue;
            }
            $check->run($dom, $xpath, $issues);
        }

        $duration = microtime(true) - $start;

        return new ASFW_Scan_Result($url, $issues, $duration);
    }

    /**
     * Fetch page HTML
     *
     * @param string $url URL to fetch.
     * @return string HTML content.
     * @throws Exception If request fails.
     */
    private static function fetch_page(string $url): string {
        $args = [
            'timeout'    => (int) get_option('asfw_scan_timeout', 30),
            'user-agent' => 'ASFW Scanner/' . ASFW_VERSION,
            'sslverify'  => false,
        ];

        // When scanning own site, connect to localhost:80 with correct Host header
        // so WordPress doesn't redirect. Needed in Docker where the external port
        // (e.g. 8088) isn't reachable from inside the container.
        $site_url = site_url('/');
        if (str_starts_with($url, $site_url)) {
            $parsed    = wp_parse_url($site_url);
            $host_header = $parsed['host'];
            if (!empty($parsed['port'])) {
                $host_header .= ':' . $parsed['port'];
            }
            $path = substr($url, strlen($site_url));
            $url  = 'http://localhost/' . $path; // phpcs:ignore PluginCheck.CodeAnalysis.Localhost.Found -- Intentional: connects to local web server for self-scanning.
            $args['headers'] = ['Host' => $host_header];
            $args['redirection'] = 0;
        }

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            throw new Exception(esc_html($response->get_error_message()));
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code >= 400) {
            /* translators: %d: HTTP status code */
            throw new Exception(esc_html(sprintf(__('HTTP error %d', 'wp-accessibility-scanner'), $code)));
        }

        return wp_remote_retrieve_body($response);
    }

    /**
     * Increment the lifetime scan counter
     */
    public static function increment_scan_count(): void {
        $total = (int) get_option('asfw_total_scans', 0);
        update_option('asfw_total_scans', $total + 1);
    }

    /**
     * Save scan result to database
     *
     * @param ASFW_Scan_Result $result The scan result.
     * @return int|false Scan ID or false on failure.
     */
    public static function save_scan(ASFW_Scan_Result $result) {
        global $wpdb;

        $inserted = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prefix . 'asfw_scans',
            [
                'url'            => $result->url,
                'scan_type'      => 'single',
                'status'         => 'completed',
                'score'          => $result->score,
                'total_issues'   => $result->get_total_issues(),
                'critical_count' => $result->critical_count,
                'serious_count'  => $result->serious_count,
                'moderate_count' => $result->moderate_count,
                'minor_count'    => $result->minor_count,
                'pages_scanned'  => 1,
                'pages_total'    => 1,
                'progress'       => 100,
                'scan_duration'  => round($result->duration, 2),
                'completed_at'   => current_time('mysql'),
            ],
            ['%s', '%s', '%s', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%d', '%f', '%s']
        );

        if (!$inserted) {
            return false;
        }

        $scan_id = (int) $wpdb->insert_id;

        // Save issues.
        foreach ($result->issues as $issue) {
            $data             = $issue->to_array();
            $data['scan_id']  = $scan_id;
            $data['page_url'] = $result->url;
            $data['wcag_level'] = 'A';

            $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                $wpdb->prefix . 'asfw_issues',
                $data,
                '%s'
            );
        }

        // Update latest score.
        update_option('asfw_latest_score', $result->score);

        return $scan_id;
    }
}
