<?php
/**
 * Settings page
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables

$asfw_scan_timeout    = (int) get_option('asfw_scan_timeout', 30);
$asfw_ignored_checks  = (array) get_option('asfw_ignored_checks', []);
$asfw_email_notif     = get_option('asfw_email_notifications', 'yes');
$asfw_notif_email     = get_option('asfw_notification_email', '');
$asfw_scan_level      = get_option('asfw_default_scan_level', 'A');
$asfw_stmt_org        = get_option('asfw_statement_org_name', '');
$asfw_stmt_email      = get_option('asfw_statement_email', '');
$asfw_stmt_phone      = get_option('asfw_statement_phone', '');
$asfw_stmt_level      = get_option('asfw_statement_level', 'A');
$asfw_all_checks      = ASFW_Check_Registry::get_checks();
$asfw_statement_page  = (int) get_option('asfw_statement_page_id', 0);

// phpcs:enable
?>
<div class="wrap asfw-settings-page">
	<h1 class="asfw-page-title"><?php esc_html_e('Settings', 'wp-accessibility-scanner'); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields('asfw_settings'); ?>

		<!-- Scanning Settings -->
		<div class="asfw-card">
			<h2><?php esc_html_e('Scanning', 'wp-accessibility-scanner'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="asfw_default_scan_level"><?php esc_html_e('Default WCAG Level', 'wp-accessibility-scanner'); ?></label>
					</th>
					<td>
						<select name="asfw_default_scan_level" id="asfw_default_scan_level">
							<option value="A" <?php selected($asfw_scan_level, 'A'); ?>><?php esc_html_e('Level A', 'wp-accessibility-scanner'); ?></option>
							<option value="AA" disabled><?php esc_html_e('Level AA (Pro)', 'wp-accessibility-scanner'); ?></option>
						</select>
						<p class="description"><?php esc_html_e('Level AA requires Pro.', 'wp-accessibility-scanner'); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="asfw_scan_timeout"><?php esc_html_e('Scan Timeout', 'wp-accessibility-scanner'); ?></label>
					</th>
					<td>
						<input type="number" name="asfw_scan_timeout" id="asfw_scan_timeout" value="<?php echo esc_attr($asfw_scan_timeout); ?>" min="5" max="120" class="small-text" />
						<?php esc_html_e('seconds', 'wp-accessibility-scanner'); ?>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e('Skip Checks', 'wp-accessibility-scanner'); ?></th>
					<td>
						<fieldset>
							<?php foreach ($asfw_all_checks as $asfw_check_item) : ?>
								<label>
									<input type="checkbox" name="asfw_ignored_checks[]" value="<?php echo esc_attr($asfw_check_item->get_id()); ?>" <?php checked(in_array($asfw_check_item->get_id(), $asfw_ignored_checks, true)); ?> />
									<?php echo esc_html($asfw_check_item->get_name()); ?>
									<span class="description">(WCAG <?php echo esc_html($asfw_check_item->get_wcag()); ?>)</span>
								</label><br />
							<?php endforeach; ?>
						</fieldset>
						<p class="description"><?php esc_html_e('Selected checks will be skipped during scans.', 'wp-accessibility-scanner'); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<!-- Notification Settings -->
		<div class="asfw-card">
			<h2><?php esc_html_e('Notifications', 'wp-accessibility-scanner'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e('Email on Scan Complete', 'wp-accessibility-scanner'); ?></th>
					<td>
						<label>
							<input type="checkbox" name="asfw_email_notifications" value="yes" <?php checked($asfw_email_notif, 'yes'); ?> />
							<?php esc_html_e('Send email when a scan completes', 'wp-accessibility-scanner'); ?>
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="asfw_notification_email"><?php esc_html_e('Notification Email', 'wp-accessibility-scanner'); ?></label>
					</th>
					<td>
						<input type="email" name="asfw_notification_email" id="asfw_notification_email" value="<?php echo esc_attr($asfw_notif_email); ?>" class="regular-text" placeholder="<?php echo esc_attr(get_option('admin_email')); ?>" />
						<p class="description"><?php esc_html_e('Defaults to admin email if empty.', 'wp-accessibility-scanner'); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<!-- Schedule (Pro) -->
		<div class="asfw-card">
			<h2><?php esc_html_e('Schedule', 'wp-accessibility-scanner'); ?> <span class="asfw-pro-badge"><?php esc_html_e('PRO', 'wp-accessibility-scanner'); ?></span></h2>

			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e('Auto-scan Frequency', 'wp-accessibility-scanner'); ?></th>
					<td>
						<select disabled>
							<option><?php esc_html_e('Disabled', 'wp-accessibility-scanner'); ?></option>
						</select>
						<p class="description">
							<?php
							$asfw_upgrade_url = add_query_arg([
								'utm_source'   => 'plugin',
								'utm_medium'   => 'settings',
								'utm_campaign' => 'free-to-pro',
							], 'https://wooplugin.pro/accessibility-scanner-pro#pricing');
							?>
							<a href="<?php echo esc_url($asfw_upgrade_url); ?>" target="_blank">
								<?php esc_html_e('Upgrade to unlock scheduling', 'wp-accessibility-scanner'); ?> &rarr;
							</a>
						</p>
					</td>
				</tr>
			</table>
		</div>

		<!-- Accessibility Statement -->
		<div class="asfw-card">
			<h2><?php esc_html_e('Accessibility Statement', 'wp-accessibility-scanner'); ?></h2>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="asfw_statement_org_name"><?php esc_html_e('Organization Name', 'wp-accessibility-scanner'); ?></label>
					</th>
					<td>
						<input type="text" name="asfw_statement_org_name" id="asfw_statement_org_name" value="<?php echo esc_attr($asfw_stmt_org); ?>" class="regular-text" placeholder="<?php echo esc_attr(get_bloginfo('name')); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="asfw_statement_email"><?php esc_html_e('Contact Email', 'wp-accessibility-scanner'); ?></label>
					</th>
					<td>
						<input type="email" name="asfw_statement_email" id="asfw_statement_email" value="<?php echo esc_attr($asfw_stmt_email); ?>" class="regular-text" placeholder="<?php echo esc_attr(get_option('admin_email')); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="asfw_statement_phone"><?php esc_html_e('Contact Phone', 'wp-accessibility-scanner'); ?></label>
					</th>
					<td>
						<input type="tel" name="asfw_statement_phone" id="asfw_statement_phone" value="<?php echo esc_attr($asfw_stmt_phone); ?>" class="regular-text" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="asfw_statement_level"><?php esc_html_e('Compliance Level', 'wp-accessibility-scanner'); ?></label>
					</th>
					<td>
						<select name="asfw_statement_level" id="asfw_statement_level">
							<option value="A" <?php selected($asfw_stmt_level, 'A'); ?>><?php esc_html_e('Level A', 'wp-accessibility-scanner'); ?></option>
							<option value="AA" <?php selected($asfw_stmt_level, 'AA'); ?>><?php esc_html_e('Level AA', 'wp-accessibility-scanner'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php esc_html_e('Generate', 'wp-accessibility-scanner'); ?></th>
					<td>
						<button type="button" id="asfw-generate-statement" class="button">
							<?php esc_html_e('Generate Statement Page', 'wp-accessibility-scanner'); ?>
						</button>
						<?php if ($asfw_statement_page && get_post($asfw_statement_page)) : ?>
							<a href="<?php echo esc_url(get_edit_post_link($asfw_statement_page)); ?>" class="button">
								<?php esc_html_e('Edit Existing Statement', 'wp-accessibility-scanner'); ?>
							</a>
						<?php endif; ?>
						<p class="description"><?php esc_html_e('Creates a draft page with your accessibility statement. Save settings first.', 'wp-accessibility-scanner'); ?></p>
						<div id="asfw-statement-result" style="display: none;"></div>
					</td>
				</tr>
			</table>
		</div>

		<?php submit_button(); ?>
	</form>
</div>
