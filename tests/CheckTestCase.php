<?php
/**
 * Base test case for accessibility checks.
 */

use PHPUnit\Framework\TestCase;

class CheckTestCase extends TestCase {

    /**
     * Run a check against an HTML string and return the issues found.
     *
     * @param ASFW_Check_Interface $check The check instance.
     * @param string               $html  The HTML to scan.
     * @return ASFW_Issue[]
     */
    protected function runCheck(ASFW_Check_Interface $check, string $html): array {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $xpath  = new DOMXPath($dom);
        $issues = [];
        $check->run($dom, $xpath, $issues);
        return $issues;
    }
}
