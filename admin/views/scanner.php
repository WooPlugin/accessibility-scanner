<?php
/**
 * Scanner page
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

$asfw_home_url = home_url('/');
?>
<div class="wrap asfw-scanner-page">
	<h1 class="asfw-page-title"><?php esc_html_e('Scanner', 'wp-accessibility-scanner'); ?></h1>

	<!-- Scan Form -->
	<div class="asfw-card asfw-scan-form-card">
		<h2><?php esc_html_e('Scan a Page', 'wp-accessibility-scanner'); ?></h2>

		<div class="asfw-scan-input-row">
			<input type="url" id="asfw-scan-url" class="asfw-scan-url-input" placeholder="<?php esc_attr_e('https://yoursite.com/page', 'wp-accessibility-scanner'); ?>" value="<?php echo esc_attr($asfw_home_url); ?>" />
			<button type="button" id="asfw-scan-btn" class="button button-primary">
				<?php esc_html_e('Scan', 'wp-accessibility-scanner'); ?>
			</button>
		</div>

		<div class="asfw-quick-scan-buttons">
			<span class="asfw-quick-scan-label"><?php esc_html_e('Quick Scan:', 'wp-accessibility-scanner'); ?></span>
			<button type="button" class="button asfw-quick-scan-btn" data-url="<?php echo esc_attr($asfw_home_url); ?>">
				<?php esc_html_e('Homepage', 'wp-accessibility-scanner'); ?>
			</button>
		</div>
	</div>

	<!-- Progress Section (hidden by default) -->
	<div class="asfw-card asfw-scan-progress-card" id="asfw-scan-progress" style="display: none;">
		<div class="asfw-scan-progress-header">
			<span class="spinner is-active"></span>
			<span id="asfw-scan-status-text"><?php esc_html_e('Scanning...', 'wp-accessibility-scanner'); ?></span>
		</div>
	</div>

	<!-- Results Section (hidden by default) -->
	<div class="asfw-card asfw-scan-results-card" id="asfw-scan-results" style="display: none;">
		<h2><?php esc_html_e('Scan Results', 'wp-accessibility-scanner'); ?></h2>

		<div class="asfw-results-summary">
			<div class="asfw-result-score">
				<span class="asfw-result-score-value" id="asfw-result-score">0</span>
				<span class="asfw-result-score-label"><?php esc_html_e('Score', 'wp-accessibility-scanner'); ?></span>
			</div>
			<div class="asfw-result-stats">
				<div class="asfw-result-stat">
					<span class="asfw-result-stat-value" id="asfw-result-total">0</span>
					<span class="asfw-result-stat-label"><?php esc_html_e('Issues', 'wp-accessibility-scanner'); ?></span>
				</div>
				<div class="asfw-result-stat">
					<span class="asfw-result-stat-value asfw-severity-critical-text" id="asfw-result-critical">0</span>
					<span class="asfw-result-stat-label"><?php esc_html_e('Critical', 'wp-accessibility-scanner'); ?></span>
				</div>
				<div class="asfw-result-stat">
					<span class="asfw-result-stat-value asfw-severity-serious-text" id="asfw-result-serious">0</span>
					<span class="asfw-result-stat-label"><?php esc_html_e('Serious', 'wp-accessibility-scanner'); ?></span>
				</div>
				<div class="asfw-result-stat">
					<span class="asfw-result-stat-value" id="asfw-result-duration">0</span>
					<span class="asfw-result-stat-label"><?php esc_html_e('Seconds', 'wp-accessibility-scanner'); ?></span>
				</div>
			</div>
		</div>

		<div class="asfw-result-actions">
			<a href="<?php echo esc_url(admin_url('admin.php?page=asfw-issues')); ?>" class="button button-primary">
				<?php esc_html_e('View Issues', 'wp-accessibility-scanner'); ?>
			</a>
			<a href="<?php echo esc_url(admin_url('admin.php?page=asfw-dashboard')); ?>" class="button">
				<?php esc_html_e('Go to Dashboard', 'wp-accessibility-scanner'); ?>
			</a>
		</div>
	</div>

	<!-- Error Section (hidden by default) -->
	<div class="asfw-card asfw-scan-error-card" id="asfw-scan-error" style="display: none;">
		<div class="asfw-notice asfw-notice-error">
			<span id="asfw-scan-error-message"></span>
		</div>
	</div>

	<!-- Pro Upsell -->
	<div class="asfw-card asfw-pro-upsell-card">
		<h2><?php esc_html_e('Full Site Scan', 'wp-accessibility-scanner'); ?> <span class="asfw-pro-badge"><?php esc_html_e('PRO', 'wp-accessibility-scanner'); ?></span></h2>
		<p><?php esc_html_e('Scan all pages, posts, and products automatically with a single click.', 'wp-accessibility-scanner'); ?></p>
		<?php
		$asfw_upgrade_url = add_query_arg([
			'utm_source'   => 'plugin',
			'utm_medium'   => 'scanner-page',
			'utm_campaign' => 'free-to-pro',
		], 'https://wooplugin.pro/accessibility-scanner#pricing');
		?>
		<a href="<?php echo esc_url($asfw_upgrade_url); ?>" class="button" target="_blank">
			<?php esc_html_e('Upgrade to unlock', 'wp-accessibility-scanner'); ?> &rarr;
		</a>
	</div>
</div>
