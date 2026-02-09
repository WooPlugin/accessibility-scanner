=== Accessibility Scanner – WCAG Compliance ===
Contributors: w03d
Donate link: https://wooplugin.pro/accessibility-scanner
Tags: accessibility, wcag, ada, accessibility checker, accessibility scanner
Requires at least: 6.0
Tested up to: 6.9
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Scan your WordPress site for WCAG 2.2 accessibility issues. 20+ automated checks, quick fixes, compliance reports. No overlay widgets.

== Description ==

**Accessibility Scanner** scans your site for WCAG 2.2 accessibility issues and helps you fix them. Unlike overlay widgets that only cover 20-30% of WCAG criteria, this plugin performs real HTML analysis and provides actionable fixes.

The European Accessibility Act (EAA) became mandatory in June 2025. If your site serves EU customers, you must comply. This plugin helps you find and fix issues before they become legal problems.

= Why Choose This Plugin? =

* **Real Scanning** — Analyzes actual HTML, not cosmetic overlays
* **20+ WCAG 2.2 Checks** — Level A compliance checks included free
* **Quick Fixes** — Fix common issues directly from the dashboard
* **Compliance Statement** — Generate a WCAG-compliant accessibility statement
* **No Overlays** — No JavaScript widgets that don't actually fix accessibility
* **Lightweight** — No frontend impact, scanning happens server-side

= Free Features =

* **20+ WCAG 2.2 Level A Checks** — Missing alt text, empty links, form labels, heading structure, color contrast, ARIA validation, landmarks, and more
* **Accessibility Score** — 0-100 score per page showing compliance level
* **Quick Fixes** — Fix missing alt text, form labels, and empty links inline
* **Issue Dashboard** — Filter, sort, and manage issues by severity and type
* **Accessibility Statement Generator** — Create a compliant statement page
* **Unlimited Scans** — Scan any page as often as you need

= Pro Features =

Upgrade to [Pro](https://wooplugin.pro/accessibility-scanner) for full compliance:

* **Full Site Crawl** — Scan every page, post, and product automatically
* **50+ WCAG 2.2 Level AA Checks** — Complete AA compliance testing
* **Scheduled Scans** — Daily, weekly, or monthly automatic scanning
* **PDF Compliance Reports** — Professional reports for clients or legal documentation
* **WooCommerce Checks** — Product images, cart labels, checkout accessibility
* **Monitoring & Alerts** — Email and Slack notifications when issues arise
* **Bulk Fix** — Fix issues across your entire site at once
* **Agency White-Label** — Custom branding on PDF reports
* **Priority Support** — Direct developer support

= What Gets Checked? =

**Images:** Missing alt text, empty alt on informative images, images of text
**Forms:** Missing labels, error identification, input instructions
**Navigation:** Empty links, empty buttons, skip navigation, keyboard access
**Structure:** Heading hierarchy, landmarks, page title, document language
**ARIA:** Invalid roles, broken references, missing properties
**Visual:** Color contrast, focus visibility, target size, text spacing
**Media:** Auto-playing media, missing captions
**Tables:** Missing headers, empty header cells

= Who Needs This? =

* **Site owners** required to comply with EAA (European Accessibility Act)
* **Agencies** managing client sites that need compliance documentation
* **Developers** who want automated accessibility testing in WordPress
* **E-commerce stores** on WooCommerce that serve EU customers
* **Government & education** sites with legal accessibility requirements

= How It Works =

1. Install and activate the plugin
2. Go to Accessibility > Scanner
3. Enter a URL or click a quick-scan button
4. Review issues with severity ratings and fix suggestions
5. Apply quick fixes or fix manually
6. Generate an accessibility statement for your site

= Documentation & Support =

* [Getting Started Guide](https://wooplugin.pro/docs/accessibility/getting-started)
* [Support Forum](https://wordpress.org/support/plugin/wp-accessibility-scanner/)

= About WooPlugin =

We build focused, lightweight WordPress plugins that do one thing well. No bloat, no overlay widgets, no tracking. Just real accessibility scanning that works.

== External Services ==

This plugin fetches web pages for accessibility analysis using the WordPress HTTP API (`wp_remote_get`).

**Self-Site Scanning**
When you initiate a scan, the plugin fetches the HTML of the URL you provide and analyzes it locally on your server. No data is sent to any external service. The fetched HTML is parsed in memory and never stored externally.

This only happens when you explicitly click "Scan" in the plugin dashboard. No automatic or background requests are made in the free version.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate through 'Plugins' menu in WordPress
3. Go to Accessibility > Scanner to run your first scan
4. Review and fix issues from the Issues page

== Frequently Asked Questions ==

= How is this different from overlay widgets like accessiBe? =

Overlay widgets add a JavaScript layer on top of your site but don't fix the underlying HTML. They typically cover only 20-30% of WCAG criteria. Courts have ruled overlay widgets insufficient for legal compliance. This plugin scans your actual HTML and helps you fix real issues.

= Do I need WooCommerce? =

No. This plugin works with any WordPress site. WooCommerce-specific accessibility checks are available in the Pro version for stores that need them.

= What is WCAG 2.2? =

WCAG (Web Content Accessibility Guidelines) 2.2 is the current international standard for web accessibility. It has three levels: A (minimum), AA (recommended), and AAA (highest). Most laws require Level AA compliance.

= What is the European Accessibility Act (EAA)? =

The EAA is an EU directive that became mandatory in June 2025. It requires websites and apps serving EU customers to meet accessibility standards. Non-compliance can result in fines.

= How many scans do I get for free? =

The free version includes unlimited single-page scans. Pro adds full-site crawling (scan every page automatically), scheduled scans, Level AA checks, PDF reports, and more.

= Can this plugin fix issues automatically? =

Yes — common issues like missing alt text, empty links, and missing form labels can be fixed directly from the Issues page using Quick Fix. More complex issues require manual fixes, and the plugin provides guidance for each.

= Does this slow down my site? =

No. The plugin only runs in the WordPress admin. It adds zero frontend JavaScript or CSS. Scans use server-side HTML analysis that doesn't affect your visitors.

= What does the accessibility score mean? =

The score (0-100) represents what percentage of checks pass on a scanned page. 90+ is excellent, 70-89 needs improvement, below 70 has significant issues.

= Can I generate a compliance report? =

Free: View scan results in the dashboard. Pro: Generate professional PDF reports with your branding, suitable for legal documentation or client deliverables.

= Is this enough for legal compliance? =

This plugin helps you identify and fix accessibility issues, which is a critical part of compliance. Full legal compliance also requires manual testing, user testing, and ongoing monitoring. We recommend using this tool alongside professional accessibility audits for complete coverage.

== Screenshots ==

1. Dashboard showing accessibility score, issue counts, and recent scans
2. Scanner page with URL input and real-time scan progress
3. Issues list with severity filtering and quick fix buttons
4. Quick fix dialog for adding missing alt text
5. Accessibility statement generator
6. Settings page with scanning configuration

== Changelog ==

= 1.0.0 =
* Initial release
* 20+ WCAG 2.2 Level A automated checks
* Accessibility score dashboard
* Quick fix for common issues
* Accessibility statement generator
* Unlimited scans
* REST API support

== Upgrade Notice ==

= 1.0.0 =
Initial release.
