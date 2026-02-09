<?php
/**
 * Settings management
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined( 'ABSPATH' ) || exit;

class ASFW_Settings {

	private static array $defaults = array(
		'asfw_scan_timeout'        => 30,
		'asfw_ignored_checks'      => array(),
		'asfw_email_notifications' => 'yes',
		'asfw_notification_email'  => '',
		'asfw_default_scan_level'  => 'A',
		'asfw_statement_org_name'  => '',
		'asfw_statement_email'     => '',
		'asfw_statement_phone'     => '',
		'asfw_statement_level'     => 'A',
	);

	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'register_settings' ) );
	}

	public static function register_settings() {
		register_setting(
			'asfw_settings',
			'asfw_scan_timeout',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 30,
			)
		);

		register_setting(
			'asfw_settings',
			'asfw_ignored_checks',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( __CLASS__, 'sanitize_ignored_checks' ),
				'default'           => array(),
			)
		);

		register_setting(
			'asfw_settings',
			'asfw_email_notifications',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'yes',
			)
		);

		register_setting(
			'asfw_settings',
			'asfw_notification_email',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_email',
				'default'           => '',
			)
		);

		register_setting(
			'asfw_settings',
			'asfw_default_scan_level',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_scan_level' ),
				'default'           => 'A',
			)
		);

		register_setting(
			'asfw_settings',
			'asfw_statement_org_name',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		register_setting(
			'asfw_settings',
			'asfw_statement_email',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_email',
				'default'           => '',
			)
		);

		register_setting(
			'asfw_settings',
			'asfw_statement_phone',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		register_setting(
			'asfw_settings',
			'asfw_statement_level',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( __CLASS__, 'sanitize_scan_level' ),
				'default'           => 'A',
			)
		);
	}

	public static function get( string $key ) {
		$default = self::$defaults[ $key ] ?? '';
		return get_option( $key, $default );
	}

	public static function sanitize_ignored_checks( $value ): array {
		if ( ! is_array( $value ) ) {
			return array();
		}
		return array_map( 'sanitize_text_field', $value );
	}

	public static function sanitize_scan_level( string $value ): string {
		return in_array( $value, array( 'A', 'AA' ), true ) ? $value : 'A';
	}
}
