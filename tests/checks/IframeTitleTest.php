<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class IframeTitleTest extends CheckTestCase {

    private ASFW_Check_Iframe_Title $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Iframe_Title();
    }

    public function test_pass_with_title(): void {
        $html = '<html><body><iframe src="page.html" title="Embedded content"></iframe></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_missing_title(): void {
        $html = '<html><body><iframe src="page.html"></iframe></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('iframe-title', $issues[0]->check_id);
    }

    public function test_fail_empty_title(): void {
        $html = '<html><body><iframe src="page.html" title=""></iframe></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_skip_aria_hidden(): void {
        $html = '<html><body><iframe src="page.html" aria-hidden="true"></iframe></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_skip_hidden_attribute(): void {
        // DOMDocument parses `hidden="hidden"` as a proper boolean attribute.
        $html = '<html><body><iframe src="page.html" hidden="hidden"></iframe></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_no_iframes(): void {
        $html = '<html><body><p>No iframes</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_multiple_iframes(): void {
        $html = '<html><body><iframe src="a.html"></iframe><iframe src="b.html"></iframe></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(2, $issues);
    }
}
