<?php
/**
 * Review notice
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined( 'ABSPATH' ) || exit;

class ASFW_Review_Notice {

	private const DELAY_DAYS  = 7;
	private const SNOOZE_DAYS = 30;

	public static function init() {
		add_action( 'admin_notices', array( __CLASS__, 'maybe_show_notice' ) );
		add_action( 'wp_ajax_asfw_dismiss_review', array( __CLASS__, 'dismiss_review' ) );
	}

	public static function maybe_show_notice() {
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->id, array( 'dashboard', 'toplevel_page_asfw-dashboard' ), true ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$installed = (int) get_option( 'asfw_installed_time', 0 );
		if ( ! $installed || ( time() - $installed ) < ( self::DELAY_DAYS * DAY_IN_SECONDS ) ) {
			return;
		}

		$dismissed = get_user_meta( get_current_user_id(), 'asfw_review_dismissed', true );
		if ( 'permanent' === $dismissed ) {
			return;
		}

		if ( $dismissed && (int) $dismissed > time() ) {
			return;
		}

		$total_scans = (int) get_option( 'asfw_total_scans', 0 );
		if ( $total_scans < 1 ) {
			return;
		}

		self::render_notice();
	}

	private static function render_notice() {
		$review_url = 'https://wordpress.org/support/plugin/wp-accessibility-scanner/reviews/#new-post';
		?>
		<div class="notice notice-info is-dismissible asfw-review-notice" id="asfw-review-notice">
			<p>
				<?php
				printf(
					/* translators: 1: plugin name, 2: review link open tag, 3: review link close tag */
					esc_html__( 'Enjoying %1$s? Please consider %2$sleaving a review%3$s to help others discover it. Thank you!', 'wp-accessibility-scanner' ),
					'<strong>' . esc_html__( 'Accessibility Scanner', 'wp-accessibility-scanner' ) . '</strong>',
					'<a href="' . esc_url( $review_url ) . '" target="_blank">',
					'</a>'
				);
				?>
			</p>
			<p>
				<a href="<?php echo esc_url( $review_url ); ?>" class="button button-primary" target="_blank">
					<?php esc_html_e( 'Leave a Review', 'wp-accessibility-scanner' ); ?>
				</a>
				<button type="button" class="button asfw-review-snooze" data-action="snooze">
					<?php esc_html_e( 'Maybe Later', 'wp-accessibility-scanner' ); ?>
				</button>
				<button type="button" class="button asfw-review-dismiss-permanent" data-action="dismiss">
					<?php esc_html_e( 'Already Did', 'wp-accessibility-scanner' ); ?>
				</button>
			</p>
		</div>
		<?php
		$asfw_nonce    = wp_create_nonce( 'asfw_review_nonce' );
		$asfw_ajax_url = admin_url( 'admin-ajax.php' );
		$asfw_script   = '(function() {
			var notice = document.getElementById("asfw-review-notice");
			if (!notice) return;
			notice.querySelectorAll("[data-action]").forEach(function(btn) {
				btn.addEventListener("click", function() {
					var formData = new FormData();
					formData.append("action", "asfw_dismiss_review");
					formData.append("nonce", "' . esc_js( $asfw_nonce ) . '");
					formData.append("type", btn.dataset.action);
					fetch("' . esc_url( $asfw_ajax_url ) . '", { method: "POST", credentials: "same-origin", body: formData });
					notice.style.display = "none";
				});
			});
		})();';
		wp_register_script( 'asfw-review-notice', false, array(), ASFW_VERSION, true ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
		wp_enqueue_script( 'asfw-review-notice' );
		wp_add_inline_script( 'asfw-review-notice', $asfw_script );
	}

	public static function dismiss_review() {
		check_ajax_referer( 'asfw_review_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'snooze';

		if ( 'dismiss' === $type ) {
			update_user_meta( get_current_user_id(), 'asfw_review_dismissed', 'permanent' );
		} else {
			update_user_meta( get_current_user_id(), 'asfw_review_dismissed', time() + ( self::SNOOZE_DAYS * DAY_IN_SECONDS ) );
		}

		wp_send_json_success();
	}
}
