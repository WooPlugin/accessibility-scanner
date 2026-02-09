<?php
/**
 * REST API endpoints
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined( 'ABSPATH' ) || exit;

class ASFW_REST_API {

	public static function init() {
		add_action( 'rest_api_init', [ __CLASS__, 'register_routes' ] );
	}

	public static function register_routes() {
		$namespace = 'asfw/v1';

		// POST /asfw/v1/scan - Start a new scan.
		register_rest_route(
			$namespace,
			'/scan',
			[
				'methods'             => 'POST',
				'callback'            => [ __CLASS__, 'start_scan' ],
				'permission_callback' => [ __CLASS__, 'check_permission' ],
				'args'                => [
					'url' => [
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'esc_url_raw',
						'validate_callback' => function ( $value ) {
							return filter_var( $value, FILTER_VALIDATE_URL ) !== false;
						},
					],
				],
			]
		);

		// GET /asfw/v1/scan/{id} - Get scan results.
		register_rest_route(
			$namespace,
			'/scan/(?P<id>\d+)',
			[
				'methods'             => 'GET',
				'callback'            => [ __CLASS__, 'get_scan' ],
				'permission_callback' => [ __CLASS__, 'check_permission' ],
				'args'                => [
					'id' => [
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// GET /asfw/v1/scans - List all scans.
		register_rest_route(
			$namespace,
			'/scans',
			[
				'methods'             => 'GET',
				'callback'            => [ __CLASS__, 'list_scans' ],
				'permission_callback' => [ __CLASS__, 'check_permission' ],
				'args'                => [
					'per_page' => [
						'type'              => 'integer',
						'default'           => 20,
						'sanitize_callback' => 'absint',
					],
					'page'     => [
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// DELETE /asfw/v1/scan/{id} - Delete a scan.
		register_rest_route(
			$namespace,
			'/scan/(?P<id>\d+)',
			[
				'methods'             => 'DELETE',
				'callback'            => [ __CLASS__, 'delete_scan' ],
				'permission_callback' => [ __CLASS__, 'check_permission' ],
				'args'                => [
					'id' => [
						'type'              => 'integer',
						'required'          => true,
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// GET /asfw/v1/issues - List issues.
		register_rest_route(
			$namespace,
			'/issues',
			[
				'methods'             => 'GET',
				'callback'            => [ __CLASS__, 'list_issues' ],
				'permission_callback' => [ __CLASS__, 'check_permission' ],
				'args'                => [
					'scan_id'  => [
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					],
					'severity' => [
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'status'   => [
						'type'              => 'string',
						'default'           => 'open',
						'sanitize_callback' => 'sanitize_text_field',
					],
					'per_page' => [
						'type'              => 'integer',
						'default'           => 50,
						'sanitize_callback' => 'absint',
					],
					'page'     => [
						'type'              => 'integer',
						'default'           => 1,
						'sanitize_callback' => 'absint',
					],
				],
			]
		);

		// GET /asfw/v1/stats - Dashboard statistics.
		register_rest_route(
			$namespace,
			'/stats',
			[
				'methods'             => 'GET',
				'callback'            => [ __CLASS__, 'get_stats' ],
				'permission_callback' => [ __CLASS__, 'check_permission' ],
			]
		);

		// GET /asfw/v1/score - Current score.
		register_rest_route(
			$namespace,
			'/score',
			[
				'methods'             => 'GET',
				'callback'            => [ __CLASS__, 'get_score' ],
				'permission_callback' => [ __CLASS__, 'check_permission' ],
			]
		);
	}

	public static function check_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	public static function start_scan( $request ) {
		$url = $request->get_param( 'url' );

		try {
			$result = ASFW_Scanner::scan( $url );
			ASFW_Scanner::increment_scan_count();
			$scan_id = ASFW_Scanner::save_scan( $result );

			return new WP_REST_Response(
				[
					'scan_id'        => $scan_id,
					'url'            => $result->url,
					'score'          => $result->score,
					'total_issues'   => $result->get_total_issues(),
					'critical_count' => $result->critical_count,
					'serious_count'  => $result->serious_count,
					'moderate_count' => $result->moderate_count,
					'minor_count'    => $result->minor_count,
					'duration'       => round( $result->duration, 2 ),
				],
				201
			);
		} catch ( Exception $e ) {
			return new WP_Error( 'scan_failed', $e->getMessage(), [ 'status' => 500 ] );
		}
	}

	public static function get_scan( $request ) {
		global $wpdb;
		$scan_id = $request->get_param( 'id' );

		$scan = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}asfw_scans WHERE id = %d",
			$scan_id
		) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		if ( ! $scan ) {
			return new WP_Error( 'not_found', __( 'Scan not found.', 'wp-accessibility-scanner' ), [ 'status' => 404 ] );
		}

		$issues = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}asfw_issues WHERE scan_id = %d ORDER BY FIELD(impact, 'critical', 'serious', 'moderate', 'minor')",
			$scan_id
		) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		$scan->issues = $issues;
		return new WP_REST_Response( $scan, 200 );
	}

	public static function list_scans( $request ) {
		global $wpdb;
		$per_page = min( $request->get_param( 'per_page' ), 100 );
		$page     = $request->get_param( 'page' );
		$offset   = ( $page - 1 ) * $per_page;

		$scans = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}asfw_scans ORDER BY created_at DESC LIMIT %d OFFSET %d",
			$per_page,
			$offset
		) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}asfw_scans" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		$response = new WP_REST_Response( $scans, 200 );
		$response->header( 'X-WP-Total', $total );
		$response->header( 'X-WP-TotalPages', (int) ceil( $total / $per_page ) );
		return $response;
	}

	public static function delete_scan( $request ) {
		global $wpdb;
		$scan_id = $request->get_param( 'id' );

		$exists = $wpdb->get_var( $wpdb->prepare(
			"SELECT id FROM {$wpdb->prefix}asfw_scans WHERE id = %d",
			$scan_id
		) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		if ( ! $exists ) {
			return new WP_Error( 'not_found', __( 'Scan not found.', 'wp-accessibility-scanner' ), [ 'status' => 404 ] );
		}

		// Delete issues first, then scan.
		$wpdb->delete( $wpdb->prefix . 'asfw_issues', [ 'scan_id' => $scan_id ], [ '%d' ] ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->delete( $wpdb->prefix . 'asfw_scans', [ 'id' => $scan_id ], [ '%d' ] ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return new WP_REST_Response( null, 204 );
	}

	public static function list_issues( $request ) {
		global $wpdb;
		$per_page = min( $request->get_param( 'per_page' ), 100 );
		$page     = $request->get_param( 'page' );
		$offset   = ( $page - 1 ) * $per_page;
		$scan_id  = $request->get_param( 'scan_id' );
		$severity = $request->get_param( 'severity' );
		$status   = $request->get_param( 'status' );

		$where = [ '1=1' ];
		$args  = [];

		if ( $scan_id ) {
			$where[] = 'scan_id = %d';
			$args[]  = $scan_id;
		}

		if ( $severity && in_array( $severity, [ 'critical', 'serious', 'moderate', 'minor' ], true ) ) {
			$where[] = 'impact = %s';
			$args[]  = $severity;
		}

		if ( $status && in_array( $status, [ 'open', 'fixed', 'ignored' ], true ) ) {
			$where[] = 'status = %s';
			$args[]  = $status;
		}

		$where_clause = implode( ' AND ', $where );
		$args[]       = $per_page;
		$args[]       = $offset;

		$issues = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}asfw_issues WHERE {$where_clause} ORDER BY FIELD(impact, 'critical', 'serious', 'moderate', 'minor') LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			...$args
		) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, PluginCheck.Security.DirectDB.UnescapedDBParameter

		return new WP_REST_Response( $issues, 200 );
	}

	public static function get_stats() {
		global $wpdb;

		$latest_scan = $wpdb->get_row(
			"SELECT * FROM {$wpdb->prefix}asfw_scans WHERE status = 'completed' ORDER BY created_at DESC LIMIT 1"
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		$total_scans = (int) $wpdb->get_var(
			"SELECT COUNT(*) FROM {$wpdb->prefix}asfw_scans WHERE status = 'completed'"
		); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		return new WP_REST_Response(
			[
				'latest_score' => (int) get_option( 'asfw_latest_score', 0 ),
				'total_scans'  => $total_scans,
				'latest_scan'  => $latest_scan,
			],
			200
		);
	}

	public static function get_score() {
		return new WP_REST_Response(
			[
				'score' => (int) get_option( 'asfw_latest_score', 0 ),
			],
			200
		);
	}
}
