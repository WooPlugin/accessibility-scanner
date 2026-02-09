<?php
/**
 * Issues page
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Template variables

global $wpdb;

// Filters (from GET params - no nonce needed for read-only display filters).
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$asfw_filter_severity = isset($_GET['severity']) ? sanitize_text_field(wp_unslash($_GET['severity'])) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$asfw_filter_category = isset($_GET['category']) ? sanitize_text_field(wp_unslash($_GET['category'])) : '';
// phpcs:ignore WordPress.Security.NonceVerification.Recommended
$asfw_paged = isset($_GET['paged']) ? absint($_GET['paged']) : 1;

$asfw_per_page = 20;
$asfw_offset   = ($asfw_paged - 1) * $asfw_per_page;

// Get latest scan ID.
$asfw_latest_scan_id = (int) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
    "SELECT id FROM {$wpdb->prefix}asfw_scans WHERE status = 'completed' ORDER BY created_at DESC LIMIT 1"
);

// Build query.
$asfw_where = ["i.scan_id = %d", "i.status = 'open'"];
$asfw_args  = [$asfw_latest_scan_id];

$asfw_valid_severities = ['critical', 'serious', 'moderate', 'minor'];
if ($asfw_filter_severity && in_array($asfw_filter_severity, $asfw_valid_severities, true)) {
    $asfw_where[] = 'i.impact = %s';
    $asfw_args[]  = $asfw_filter_severity;
}

// Category filter: map category to check_ids.
if ($asfw_filter_category) {
    $asfw_category_checks = [];
    foreach (ASFW_Check_Registry::get_checks() as $asfw_check_item) {
        if ($asfw_check_item->get_category() === $asfw_filter_category) {
            $asfw_category_checks[] = $asfw_check_item->get_id();
        }
    }
    if ($asfw_category_checks) {
        $asfw_placeholders = implode(', ', array_fill(0, count($asfw_category_checks), '%s'));
        $asfw_where[]      = "i.check_id IN ($asfw_placeholders)";
        $asfw_args          = array_merge($asfw_args, $asfw_category_checks);
    }
}

$asfw_where_clause = implode(' AND ', $asfw_where);

// Count total.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare, PluginCheck.Security.DirectDB.UnescapedDBParameter
$asfw_total_count = (int) $wpdb->get_var(
    $wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->prefix}asfw_issues i WHERE {$asfw_where_clause}", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        ...$asfw_args
    )
);

// Fetch issues.
$asfw_query_args   = array_merge($asfw_args, [$asfw_per_page, $asfw_offset]);
// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber, PluginCheck.Security.DirectDB.UnescapedDBParameter
$asfw_issues_list = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT i.* FROM {$wpdb->prefix}asfw_issues i WHERE {$asfw_where_clause} ORDER BY FIELD(i.impact, 'critical', 'serious', 'moderate', 'minor'), i.id ASC LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        ...$asfw_query_args
    )
);

$asfw_total_pages = (int) ceil($asfw_total_count / $asfw_per_page);

// Get unique categories from registered checks.
$asfw_categories = [];
foreach (ASFW_Check_Registry::get_checks() as $check) {
    $asfw_categories[$check->get_category()] = $check->get_category();
}

// phpcs:enable
?>
<div class="wrap asfw-issues-page">
    <h1 class="asfw-page-title">
        <?php esc_html_e('Issues', 'wp-accessibility-scanner'); ?>
        <span class="asfw-issues-count">(<?php echo esc_html($asfw_total_count); ?>)</span>
    </h1>

    <?php if (!$asfw_latest_scan_id) : ?>
        <div class="asfw-card">
            <p class="asfw-empty-state">
                <?php esc_html_e('No scans have been run yet.', 'wp-accessibility-scanner'); ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=asfw-scanner')); ?>">
                    <?php esc_html_e('Run your first scan', 'wp-accessibility-scanner'); ?>
                </a>
            </p>
        </div>
    <?php else : ?>

    <!-- Filters -->
    <div class="asfw-filter-bar">
        <form method="get" class="asfw-filters-form">
            <input type="hidden" name="page" value="asfw-issues" />

            <select name="severity" class="asfw-filter-select" onchange="this.form.submit()">
                <option value=""><?php esc_html_e('All Severities', 'wp-accessibility-scanner'); ?></option>
                <?php foreach ($asfw_valid_severities as $asfw_sev) : ?>
                    <option value="<?php echo esc_attr($asfw_sev); ?>" <?php selected($asfw_filter_severity, $asfw_sev); ?>>
                        <?php echo esc_html(ucfirst($asfw_sev)); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="category" class="asfw-filter-select" onchange="this.form.submit()">
                <option value=""><?php esc_html_e('All Categories', 'wp-accessibility-scanner'); ?></option>
                <?php foreach ($asfw_categories as $cat) : ?>
                    <option value="<?php echo esc_attr($cat); ?>" <?php selected($asfw_filter_category, $cat); ?>>
                        <?php echo esc_html(ucfirst($cat)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Issue List -->
    <?php if ($asfw_issues_list) : ?>
        <div class="asfw-issues-list">
            <?php foreach ($asfw_issues_list as $asfw_issue_item) : ?>
                <?php $asfw_check = ASFW_Check_Registry::get_check($asfw_issue_item->check_id); ?>
                <div class="asfw-issue-row">
                    <div class="asfw-issue-header">
                        <span class="asfw-severity-badge asfw-severity-<?php echo esc_attr($asfw_issue_item->impact); ?>">
                            <?php echo esc_html(strtoupper($asfw_issue_item->impact)); ?>
                        </span>
                        <span class="asfw-issue-name">
                            <?php echo $asfw_check ? esc_html($asfw_check->get_name()) : esc_html($asfw_issue_item->check_id); ?>
                        </span>
                    </div>
                    <div class="asfw-issue-meta">
                        <?php if ($asfw_issue_item->wcag_criterion) : ?>
                            <span class="asfw-issue-wcag">WCAG <?php echo esc_html($asfw_issue_item->wcag_criterion); ?></span>
                        <?php endif; ?>
                        <?php if ($asfw_check) : ?>
                            <span class="asfw-issue-category"><?php echo esc_html(ucfirst($asfw_check->get_category())); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($asfw_issue_item->element_html) : ?>
                        <div class="asfw-issue-element">
                            <code><?php echo esc_html(wp_trim_words($asfw_issue_item->element_html, 20, '...')); ?></code>
                        </div>
                    <?php endif; ?>
                    <?php if ($asfw_issue_item->fix_hint) : ?>
                        <div class="asfw-issue-fix-hint">
                            <?php echo esc_html($asfw_issue_item->fix_hint); ?>
                        </div>
                    <?php endif; ?>
                    <div class="asfw-issue-actions">
                        <button type="button" class="button asfw-fix-btn" data-issue-id="<?php echo (int) $asfw_issue_item->id; ?>">
                            <?php esc_html_e('Mark Fixed', 'wp-accessibility-scanner'); ?>
                        </button>
                        <button type="button" class="button asfw-ignore-btn" data-issue-id="<?php echo (int) $asfw_issue_item->id; ?>">
                            <?php esc_html_e('Ignore', 'wp-accessibility-scanner'); ?>
                        </button>
                        <?php if ($asfw_issue_item->page_url) : ?>
                            <a href="<?php echo esc_url($asfw_issue_item->page_url); ?>" target="_blank" class="button">
                                <?php esc_html_e('View Page', 'wp-accessibility-scanner'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($asfw_total_pages > 1) : ?>
            <div class="asfw-pagination">
                <?php
                $asfw_base_url = admin_url('admin.php?page=asfw-issues');
                if ($asfw_filter_severity) {
                    $asfw_base_url = add_query_arg('severity', $asfw_filter_severity, $asfw_base_url);
                }
                if ($asfw_filter_category) {
                    $asfw_base_url = add_query_arg('category', $asfw_filter_category, $asfw_base_url);
                }
                ?>
                <span class="asfw-pagination-info">
                    <?php
                    printf(
                        /* translators: 1: first item, 2: last item, 3: total */
                        esc_html__('Showing %1$d-%2$d of %3$d', 'wp-accessibility-scanner'),
                        (int) ($asfw_offset + 1),
                        (int) min($asfw_offset + $asfw_per_page, $asfw_total_count),
                        (int) $asfw_total_count
                    );
                    ?>
                </span>
                <?php if ($asfw_paged > 1) : ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', $asfw_paged - 1, $asfw_base_url)); ?>" class="button">&laquo; <?php esc_html_e('Previous', 'wp-accessibility-scanner'); ?></a>
                <?php endif; ?>
                <?php if ($asfw_paged < $asfw_total_pages) : ?>
                    <a href="<?php echo esc_url(add_query_arg('paged', $asfw_paged + 1, $asfw_base_url)); ?>" class="button"><?php esc_html_e('Next', 'wp-accessibility-scanner'); ?> &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="asfw-card">
            <p class="asfw-empty-state"><?php esc_html_e('No issues matching your filters.', 'wp-accessibility-scanner'); ?></p>
        </div>
    <?php endif; ?>

    <?php endif; ?>
</div>
