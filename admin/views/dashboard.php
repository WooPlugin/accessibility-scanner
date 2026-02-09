<?php
/**
 * Dashboard page
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables

global $wpdb;

$asfw_latest_score = (int) get_option('asfw_latest_score', 0);

// Get latest scan stats.
$asfw_latest_scan = $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	"SELECT * FROM {$wpdb->prefix}asfw_scans WHERE status = 'completed' ORDER BY created_at DESC LIMIT 1"
);

// Get recent scans.
$asfw_recent_scans = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	"SELECT id, url, score, total_issues, created_at FROM {$wpdb->prefix}asfw_scans WHERE status = 'completed' ORDER BY created_at DESC LIMIT 5"
);

// Get top issues by check_id from latest scan.
$asfw_top_issues = [];
if ($asfw_latest_scan) {
	$asfw_top_issues = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare(
			"SELECT check_id, COUNT(*) as issue_count FROM {$wpdb->prefix}asfw_issues WHERE scan_id = %d AND status = 'open' GROUP BY check_id ORDER BY issue_count DESC LIMIT 5",
			$asfw_latest_scan->id
		)
	);
}

// Score color.
$asfw_score_color = 'var(--asfw-score-poor)';
if ($asfw_latest_score >= 90) {
	$asfw_score_color = 'var(--asfw-score-excellent)';
} elseif ($asfw_latest_score >= 70) {
	$asfw_score_color = 'var(--asfw-score-good)';
} elseif ($asfw_latest_score >= 50) {
	$asfw_score_color = 'var(--asfw-score-needs-work)';
}

// Score label.
$asfw_score_label = __('Poor', 'wp-accessibility-scanner');
if ($asfw_latest_score >= 90) {
	$asfw_score_label = __('Excellent', 'wp-accessibility-scanner');
} elseif ($asfw_latest_score >= 70) {
	$asfw_score_label = __('Good', 'wp-accessibility-scanner');
} elseif ($asfw_latest_score >= 50) {
	$asfw_score_label = __('Needs Improvement', 'wp-accessibility-scanner');
}

// Severity counts from latest scan.
$asfw_total_issues   = $asfw_latest_scan ? (int) $asfw_latest_scan->total_issues : 0;
$asfw_critical_count = $asfw_latest_scan ? (int) $asfw_latest_scan->critical_count : 0;
$asfw_serious_count  = $asfw_latest_scan ? (int) $asfw_latest_scan->serious_count : 0;
$asfw_moderate_count = $asfw_latest_scan ? (int) $asfw_latest_scan->moderate_count : 0;
$asfw_minor_count    = $asfw_latest_scan ? (int) $asfw_latest_scan->minor_count : 0;

// phpcs:enable
?>
<div class="wrap asfw-dashboard">
	<h1 class="asfw-page-title"><?php esc_html_e('Accessibility Dashboard', 'wp-accessibility-scanner'); ?></h1>

	<!-- Score Card -->
	<div class="asfw-score-card">
		<div class="asfw-score-circle" style="--score-color: <?php echo esc_attr($asfw_score_color); ?>">
			<span class="asfw-score-value"><?php echo esc_html($asfw_latest_score); ?></span>
		</div>
		<div class="asfw-score-details">
			<span class="asfw-score-label" style="color: <?php echo esc_attr($asfw_score_color); ?>">
				<?php echo esc_html($asfw_latest_score); ?>/100 &mdash; <?php echo esc_html($asfw_score_label); ?>
			</span>
			<span class="asfw-score-meta">
				<?php if ($asfw_latest_scan) : ?>
					<?php
					printf(
						/* translators: %s: time ago */
						esc_html__('Last scan: %s ago', 'wp-accessibility-scanner'),
						esc_html(human_time_diff(strtotime($asfw_latest_scan->created_at), time()))
					);
					?>
				<?php else : ?>
					<?php esc_html_e('No scans yet', 'wp-accessibility-scanner'); ?>
				<?php endif; ?>
			</span>
			<a href="<?php echo esc_url(admin_url('admin.php?page=asfw-scanner')); ?>" class="button button-primary asfw-scan-now-btn">
				<?php esc_html_e('Scan Now', 'wp-accessibility-scanner'); ?>
			</a>
		</div>
	</div>

	<!-- Stats Grid -->
	<div class="asfw-stats-grid">
		<div class="asfw-stat-card">
			<span class="stat-number"><?php echo esc_html($asfw_total_issues); ?></span>
			<span class="stat-label"><?php esc_html_e('Total Issues', 'wp-accessibility-scanner'); ?></span>
		</div>
		<div class="asfw-stat-card asfw-severity-critical-card">
			<span class="stat-number"><?php echo esc_html($asfw_critical_count); ?></span>
			<span class="stat-label"><?php esc_html_e('Critical', 'wp-accessibility-scanner'); ?></span>
		</div>
		<div class="asfw-stat-card asfw-severity-serious-card">
			<span class="stat-number"><?php echo esc_html($asfw_serious_count); ?></span>
			<span class="stat-label"><?php esc_html_e('Serious', 'wp-accessibility-scanner'); ?></span>
		</div>
		<div class="asfw-stat-card asfw-severity-moderate-card">
			<span class="stat-number"><?php echo esc_html($asfw_moderate_count); ?></span>
			<span class="stat-label"><?php esc_html_e('Moderate', 'wp-accessibility-scanner'); ?></span>
		</div>
		<div class="asfw-stat-card asfw-severity-minor-card">
			<span class="stat-number"><?php echo esc_html($asfw_minor_count); ?></span>
			<span class="stat-label"><?php esc_html_e('Minor', 'wp-accessibility-scanner'); ?></span>
		</div>
	</div>

	<!-- Cards Grid -->
	<div class="asfw-cards-grid">
		<!-- Recent Scans -->
		<div class="asfw-card">
			<h2><?php esc_html_e('Recent Scans', 'wp-accessibility-scanner'); ?></h2>
			<?php if ($asfw_recent_scans) : ?>
				<table class="asfw-recent-scans-table">
					<thead>
						<tr>
							<th><?php esc_html_e('URL', 'wp-accessibility-scanner'); ?></th>
							<th><?php esc_html_e('Score', 'wp-accessibility-scanner'); ?></th>
							<th><?php esc_html_e('Issues', 'wp-accessibility-scanner'); ?></th>
							<th><?php esc_html_e('When', 'wp-accessibility-scanner'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($asfw_recent_scans as $asfw_scan) : ?>
							<tr>
								<td class="asfw-scan-url-cell" title="<?php echo esc_attr($asfw_scan->url); ?>">
									<?php echo esc_html(wp_parse_url($asfw_scan->url, PHP_URL_PATH) ?: '/'); ?>
								</td>
								<td><strong><?php echo esc_html($asfw_scan->score); ?></strong></td>
								<td><?php echo esc_html($asfw_scan->total_issues); ?></td>
								<td class="asfw-time-ago"><?php echo esc_html(human_time_diff(strtotime($asfw_scan->created_at), time())); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<p class="asfw-empty-state"><?php esc_html_e('No scans yet. Go to the Scanner page to run your first scan.', 'wp-accessibility-scanner'); ?></p>
			<?php endif; ?>
		</div>

		<!-- Top Issues -->
		<div class="asfw-card">
			<h2><?php esc_html_e('Top Issues', 'wp-accessibility-scanner'); ?></h2>
			<?php if ($asfw_top_issues) : ?>
				<table class="asfw-top-issues-table">
					<tbody>
						<?php foreach ($asfw_top_issues as $asfw_issue_item) : ?>
							<?php $asfw_check = ASFW_Check_Registry::get_check($asfw_issue_item->check_id); ?>
							<tr>
								<td>
									<?php if ($asfw_check) : ?>
										<?php echo esc_html($asfw_check->get_name()); ?>
									<?php else : ?>
										<?php echo esc_html($asfw_issue_item->check_id); ?>
									<?php endif; ?>
								</td>
								<td class="asfw-issue-count"><?php echo esc_html($asfw_issue_item->issue_count); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<p class="asfw-view-all">
					<a href="<?php echo esc_url(admin_url('admin.php?page=asfw-issues')); ?>">
						<?php esc_html_e('View All Issues', 'wp-accessibility-scanner'); ?> &rarr;
					</a>
				</p>
			<?php else : ?>
				<p class="asfw-empty-state"><?php esc_html_e('No issues found yet.', 'wp-accessibility-scanner'); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>
