<?php
/**
 * Accessibility statement generator
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined( 'ABSPATH' ) || exit;

class ASFW_Statement_Generator {

	public static function init() {
		add_action( 'wp_ajax_asfw_generate_statement', [ __CLASS__, 'ajax_generate_statement' ] );
		add_shortcode( 'asfw_statement', [ __CLASS__, 'render_shortcode' ] );
	}

	public static function ajax_generate_statement() {
		check_ajax_referer( 'asfw_statement_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Permission denied.', 'wp-accessibility-scanner' ) );
		}

		$org_name = get_option( 'asfw_statement_org_name', get_bloginfo( 'name' ) );
		$email    = get_option( 'asfw_statement_email', get_option( 'admin_email' ) );
		$phone    = get_option( 'asfw_statement_phone', '' );
		$level    = get_option( 'asfw_statement_level', 'A' );
		$score    = (int) get_option( 'asfw_latest_score', 0 );

		$content = self::generate_content( $org_name, $email, $phone, $level, $score );

		// Check if statement page already exists.
		$page_id = (int) get_option( 'asfw_statement_page_id', 0 );
		if ( $page_id && get_post( $page_id ) ) {
			wp_update_post(
				[
					'ID'           => $page_id,
					'post_content' => $content,
				]
			);
		} else {
			$page_id = wp_insert_post(
				[
					'post_title'   => __( 'Accessibility Statement', 'wp-accessibility-scanner' ),
					'post_content' => $content,
					'post_status'  => 'draft',
					'post_type'    => 'page',
					'post_author'  => get_current_user_id(),
				]
			);

			if ( $page_id && ! is_wp_error( $page_id ) ) {
				update_option( 'asfw_statement_page_id', $page_id );
			}
		}

		if ( is_wp_error( $page_id ) ) {
			wp_send_json_error( $page_id->get_error_message() );
		}

		wp_send_json_success(
			[
				'page_id'  => $page_id,
				'edit_url' => get_edit_post_link( $page_id, 'raw' ),
				'view_url' => get_permalink( $page_id ),
			]
		);
	}

	private static function generate_content( string $org_name, string $email, string $phone, string $level, int $score ): string {
		$date       = wp_date( get_option( 'date_format' ) );
		$site_name  = get_bloginfo( 'name' );
		$site_url   = home_url( '/' );
		$level_text = $level === 'AA' ? 'Level AA' : 'Level A';

		$content = "<!-- wp:heading -->\n<h2>" . esc_html__( 'Accessibility Statement', 'wp-accessibility-scanner' ) . "</h2>\n<!-- /wp:heading -->\n\n";

		$content .= "<!-- wp:paragraph -->\n<p>";
		$content .= sprintf(
			/* translators: 1: organization name, 2: site name */
			esc_html__( '%1$s is committed to ensuring digital accessibility for people with disabilities. We are continually improving the user experience for everyone and applying the relevant accessibility standards on %2$s.', 'wp-accessibility-scanner' ),
			esc_html( $org_name ),
			esc_html( $site_name )
		);
		$content .= "</p>\n<!-- /wp:paragraph -->\n\n";

		$content .= "<!-- wp:heading {\"level\":3} -->\n<h3>" . esc_html__( 'Conformance Status', 'wp-accessibility-scanner' ) . "</h3>\n<!-- /wp:heading -->\n\n";

		$content .= "<!-- wp:paragraph -->\n<p>";
		$content .= sprintf(
			/* translators: 1: site name, 2: WCAG level */
			esc_html__( '%1$s strives to conform to the Web Content Accessibility Guidelines (WCAG) 2.2 %2$s. These guidelines explain how to make web content more accessible for people with disabilities.', 'wp-accessibility-scanner' ),
			esc_html( $site_name ),
			esc_html( $level_text )
		);
		$content .= "</p>\n<!-- /wp:paragraph -->\n\n";

		if ( $score > 0 ) {
			$content .= "<!-- wp:paragraph -->\n<p>";
			$content .= sprintf(
				/* translators: 1: score, 2: date */
				esc_html__( 'Our most recent accessibility audit scored %1$d/100 on %2$s.', 'wp-accessibility-scanner' ),
				$score,
				esc_html( $date )
			);
			$content .= "</p>\n<!-- /wp:paragraph -->\n\n";
		}

		$content .= "<!-- wp:heading {\"level\":3} -->\n<h3>" . esc_html__( 'Feedback', 'wp-accessibility-scanner' ) . "</h3>\n<!-- /wp:heading -->\n\n";

		$content .= "<!-- wp:paragraph -->\n<p>";
		$content .= esc_html__( 'We welcome your feedback on the accessibility of this site. Please let us know if you encounter accessibility barriers:', 'wp-accessibility-scanner' );
		$content .= "</p>\n<!-- /wp:paragraph -->\n\n";

		$content .= "<!-- wp:list -->\n<ul>\n";
		if ( $email ) {
			$content .= '<li>' . sprintf(
				/* translators: %s: email address */
				esc_html__( 'E-mail: %s', 'wp-accessibility-scanner' ),
				esc_html( $email )
			) . "</li>\n";
		}
		if ( $phone ) {
			$content .= '<li>' . sprintf(
				/* translators: %s: phone number */
				esc_html__( 'Phone: %s', 'wp-accessibility-scanner' ),
				esc_html( $phone )
			) . "</li>\n";
		}
		$content .= "</ul>\n<!-- /wp:list -->\n\n";

		$content .= "<!-- wp:paragraph -->\n<p><em>";
		$content .= sprintf(
			/* translators: %s: date */
			esc_html__( 'This statement was last updated on %s.', 'wp-accessibility-scanner' ),
			esc_html( $date )
		);
		$content .= "</em></p>\n<!-- /wp:paragraph -->";

		return $content;
	}

	public static function render_shortcode(): string {
		$org_name = get_option( 'asfw_statement_org_name', get_bloginfo( 'name' ) );
		$email    = get_option( 'asfw_statement_email', get_option( 'admin_email' ) );
		$phone    = get_option( 'asfw_statement_phone', '' );
		$level    = get_option( 'asfw_statement_level', 'A' );
		$score    = (int) get_option( 'asfw_latest_score', 0 );

		return self::generate_content( $org_name, $email, $phone, $level, $score );
	}
}
