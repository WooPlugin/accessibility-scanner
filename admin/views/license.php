<?php
/**
 * License page
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

$asfw_upgrade_url = add_query_arg([
	'utm_source'   => 'plugin',
	'utm_medium'   => 'license-page',
	'utm_campaign' => 'free-to-pro',
], 'https://wooplugin.pro/accessibility-scanner-pro#pricing');
?>
<div class="wrap asfw-license-page">
	<h1 class="asfw-page-title"><?php esc_html_e('License', 'wp-accessibility-scanner'); ?></h1>

	<!-- Plugin Info -->
	<div class="asfw-card">
		<h2><?php esc_html_e('Plugin Information', 'wp-accessibility-scanner'); ?></h2>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e('Plugin', 'wp-accessibility-scanner'); ?></th>
				<td><strong><?php esc_html_e('Accessibility Scanner for WordPress', 'wp-accessibility-scanner'); ?></strong></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('Version', 'wp-accessibility-scanner'); ?></th>
				<td><?php echo esc_html(ASFW_VERSION); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e('License', 'wp-accessibility-scanner'); ?></th>
				<td>
					<span class="asfw-status-badge asfw-status-free"><?php esc_html_e('Free', 'wp-accessibility-scanner'); ?></span>
				</td>
			</tr>
		</table>
	</div>

	<!-- Upgrade Card -->
	<div class="asfw-card asfw-upgrade-card">
		<h2><?php esc_html_e('Upgrade to Pro', 'wp-accessibility-scanner'); ?></h2>
		<p><?php esc_html_e('Unlock the full power of accessibility scanning with Pro features:', 'wp-accessibility-scanner'); ?></p>

		<div class="asfw-feature-comparison">
			<table class="widefat">
				<thead>
					<tr>
						<th><?php esc_html_e('Feature', 'wp-accessibility-scanner'); ?></th>
						<th><?php esc_html_e('Free', 'wp-accessibility-scanner'); ?></th>
						<th><?php esc_html_e('Pro', 'wp-accessibility-scanner'); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php esc_html_e('WCAG 2.2 Level A Checks', 'wp-accessibility-scanner'); ?></td>
						<td>22</td>
						<td>22</td>
					</tr>
					<tr>
						<td><?php esc_html_e('WCAG 2.2 Level AA Checks', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>30+</td>
					</tr>
					<tr>
						<td><?php esc_html_e('Single Page Scans', 'wp-accessibility-scanner'); ?></td>
						<td><?php esc_html_e('Unlimited', 'wp-accessibility-scanner'); ?></td>
						<td><?php esc_html_e('Unlimited', 'wp-accessibility-scanner'); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e('Full Site Crawl', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>&check;</td>
					</tr>
					<tr>
						<td><?php esc_html_e('Scheduled Scans', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>&check;</td>
					</tr>
					<tr>
						<td><?php esc_html_e('PDF Compliance Reports', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>&check;</td>
					</tr>
					<tr>
						<td><?php esc_html_e('WooCommerce Checks', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>&check;</td>
					</tr>
					<tr>
						<td><?php esc_html_e('Bulk Fix', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>&check;</td>
					</tr>
					<tr>
						<td><?php esc_html_e('Email & Slack Alerts', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>&check;</td>
					</tr>
					<tr>
						<td><?php esc_html_e('White-Label Reports', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>&check;</td>
					</tr>
					<tr>
						<td><?php esc_html_e('Priority Support', 'wp-accessibility-scanner'); ?></td>
						<td>&mdash;</td>
						<td>&check;</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="asfw-pricing-cards">
			<div class="asfw-pricing-card">
				<h3><?php esc_html_e('Pro', 'wp-accessibility-scanner'); ?></h3>
				<div class="asfw-price">$99<span>/<?php esc_html_e('year', 'wp-accessibility-scanner'); ?></span></div>
				<p><?php esc_html_e('1 site', 'wp-accessibility-scanner'); ?></p>
			</div>
			<div class="asfw-pricing-card asfw-pricing-featured">
				<h3><?php esc_html_e('Business', 'wp-accessibility-scanner'); ?></h3>
				<div class="asfw-price">$149<span>/<?php esc_html_e('year', 'wp-accessibility-scanner'); ?></span></div>
				<p><?php esc_html_e('5 sites', 'wp-accessibility-scanner'); ?></p>
			</div>
			<div class="asfw-pricing-card">
				<h3><?php esc_html_e('Agency', 'wp-accessibility-scanner'); ?></h3>
				<div class="asfw-price">$249<span>/<?php esc_html_e('year', 'wp-accessibility-scanner'); ?></span></div>
				<p><?php esc_html_e('Unlimited sites', 'wp-accessibility-scanner'); ?></p>
			</div>
		</div>

		<p class="asfw-upgrade-cta">
			<a href="<?php echo esc_url($asfw_upgrade_url); ?>" class="button button-primary button-hero" target="_blank">
				<?php esc_html_e('Get Pro Now', 'wp-accessibility-scanner'); ?> &rarr;
			</a>
		</p>
	</div>
</div>
