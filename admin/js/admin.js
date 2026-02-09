/**
 * Accessibility Scanner Admin JavaScript
 *
 * Uses vanilla JavaScript - no jQuery dependency
 *
 * @package Accessibility_Scanner_For_WordPress
 */

(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initScanner();
        initQuickScanButtons();
        initHelpDropdown();
        initUpgradeLink();
        initStatementGenerator();
        initIssueActions();
    });

    /**
     * Initialize scanner functionality
     */
    function initScanner() {
        var scanBtn = document.getElementById('asfw-scan-btn');
        if (!scanBtn) {
            return;
        }

        scanBtn.addEventListener('click', function () {
            startScan();
        });

        // Allow Enter key in URL input.
        var urlInput = document.getElementById('asfw-scan-url');
        if (urlInput) {
            urlInput.addEventListener('keypress', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    startScan();
                }
            });
        }
    }

    /**
     * Start a scan
     */
    function startScan() {
        var urlInput = document.getElementById('asfw-scan-url');
        var scanBtn = document.getElementById('asfw-scan-btn');
        var progressCard = document.getElementById('asfw-scan-progress');
        var resultsCard = document.getElementById('asfw-scan-results');
        var errorCard = document.getElementById('asfw-scan-error');

        if (!urlInput || !urlInput.value.trim()) {
            return;
        }

        // Disable button and show progress.
        scanBtn.disabled = true;
        scanBtn.textContent = asfwScanner.strings.scanning;
        if (progressCard) {
            progressCard.style.display = 'block';
        }
        if (resultsCard) {
            resultsCard.style.display = 'none';
        }
        if (errorCard) {
            errorCard.style.display = 'none';
        }

        var formData = new FormData();
        formData.append('action', 'asfw_start_scan');
        formData.append('nonce', asfwScanner.nonce);
        formData.append('url', urlInput.value.trim());

        fetch(asfwScanner.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                if (progressCard) {
                    progressCard.style.display = 'none';
                }

                if (data.success) {
                    showResults(data.data);
                } else {
                    showError(data.data || 'Unknown error');
                }
            })
            .catch(function (error) {
                if (progressCard) {
                    progressCard.style.display = 'none';
                }
                showError(error.message);
            })
            .finally(function () {
                scanBtn.disabled = false;
                scanBtn.textContent = 'Scan';
            });
    }

    /**
     * Show scan results
     *
     * @param {Object} data Scan result data
     */
    function showResults(data) {
        var resultsCard = document.getElementById('asfw-scan-results');
        if (!resultsCard) {
            return;
        }

        resultsCard.style.display = 'block';

        var scoreEl = document.getElementById('asfw-result-score');
        var totalEl = document.getElementById('asfw-result-total');
        var criticalEl = document.getElementById('asfw-result-critical');
        var seriousEl = document.getElementById('asfw-result-serious');
        var durationEl = document.getElementById('asfw-result-duration');

        if (scoreEl) scoreEl.textContent = data.score;
        if (totalEl) totalEl.textContent = data.total_issues;
        if (criticalEl) criticalEl.textContent = data.critical_count;
        if (seriousEl) seriousEl.textContent = data.serious_count;
        if (durationEl) durationEl.textContent = data.duration;

        // Color the score.
        if (scoreEl) {
            var color = '#dc2626';
            if (data.score >= 90) color = '#16a34a';
            else if (data.score >= 70) color = '#4285f4';
            else if (data.score >= 50) color = '#f59e0b';
            scoreEl.style.color = color;
        }

    }

    /**
     * Show scan error
     *
     * @param {string} message Error message
     */
    function showError(message) {
        var errorCard = document.getElementById('asfw-scan-error');
        var errorMessage = document.getElementById('asfw-scan-error-message');

        if (errorCard) {
            errorCard.style.display = 'block';
        }
        if (errorMessage) {
            errorMessage.textContent = asfwScanner.strings.error + ' ' + message;
        }
    }

    /**
     * Initialize quick scan buttons
     */
    function initQuickScanButtons() {
        var buttons = document.querySelectorAll('.asfw-quick-scan-btn');
        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var urlInput = document.getElementById('asfw-scan-url');
                if (urlInput) {
                    urlInput.value = btn.dataset.url;
                }
            });
        });
    }

    /**
     * Initialize Help dropdown
     */
    function initHelpDropdown() {
        var dropdown = document.querySelector('.asfw-help-dropdown');
        if (!dropdown) {
            return;
        }

        var toggle = dropdown.querySelector('.asfw-help-toggle');

        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            dropdown.classList.toggle('open');
        });

        document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                dropdown.classList.remove('open');
            }
        });
    }

    /**
     * Initialize statement generator button
     */
    function initStatementGenerator() {
        var btn = document.getElementById('asfw-generate-statement');
        if (!btn) {
            return;
        }

        btn.addEventListener('click', function () {
            btn.disabled = true;
            btn.textContent = 'Generating...';

            var formData = new FormData();
            formData.append('action', 'asfw_generate_statement');
            formData.append('nonce', asfwScanner.statementNonce);

            fetch(asfwScanner.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    var resultEl = document.getElementById('asfw-statement-result');
                    if (data.success && resultEl) {
                        resultEl.style.display = 'block';
                        resultEl.innerHTML = '<div class="notice notice-success inline"><p>Statement page created! <a href="' + data.data.edit_url + '">Edit page</a></p></div>';
                    } else if (resultEl) {
                        resultEl.style.display = 'block';
                        resultEl.innerHTML = '<div class="notice notice-error inline"><p>' + (data.data || 'Error') + '</p></div>';
                    }
                })
                .finally(function () {
                    btn.disabled = false;
                    btn.textContent = 'Generate Statement Page';
                });
        });
    }

    /**
     * Initialize issue Fix/Ignore buttons
     */
    function initIssueActions() {
        document.querySelectorAll('.asfw-fix-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                handleIssueAction(btn, 'asfw_fix_issue');
            });
        });

        document.querySelectorAll('.asfw-ignore-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                handleIssueAction(btn, 'asfw_dismiss_issue');
            });
        });
    }

    /**
     * Handle fix/ignore action for an issue
     *
     * @param {HTMLElement} btn    The clicked button
     * @param {string}      action AJAX action name
     */
    function handleIssueAction(btn, action) {
        var issueId = btn.dataset.issueId;
        var row = btn.closest('.asfw-issue-row');

        btn.disabled = true;

        var formData = new FormData();
        formData.append('action', action);
        formData.append('nonce', asfwScanner.fixNonce);
        formData.append('issue_id', issueId);

        fetch(asfwScanner.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.success && row) {
                    row.style.opacity = '0';
                    row.style.transition = 'opacity 0.3s';
                    setTimeout(function () {
                        row.remove();
                        // Update issue count in header.
                        var countEl = document.querySelector('.asfw-issues-count');
                        if (countEl) {
                            var remaining = document.querySelectorAll('.asfw-issue-row').length;
                            countEl.textContent = '(' + remaining + ')';
                        }
                    }, 300);
                }
            })
            .catch(function () {
                btn.disabled = false;
            });
    }

    /**
     * Initialize upgrade link (open in new tab)
     */
    function initUpgradeLink() {
        var link = document.querySelector('a[href*="page=asfw-upgrade-pro"]');
        if (link && typeof asfwScanner !== 'undefined') {
            link.href = asfwScanner.upgradeUrl;
            link.target = '_blank';
        }
    }
})();
