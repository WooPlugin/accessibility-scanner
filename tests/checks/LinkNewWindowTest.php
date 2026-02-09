<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class LinkNewWindowTest extends CheckTestCase {

    private ASFW_Check_Link_New_Window $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Link_New_Window();
    }

    public function test_pass_no_target_blank(): void {
        $html = '<html><body><a href="/about">About</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_target_blank_no_warning(): void {
        $html = '<html><body><a href="https://example.com" target="_blank">Example</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('link-new-window', $issues[0]->check_id);
    }

    public function test_pass_target_blank_with_text_warning(): void {
        $html = '<html><body><a href="https://example.com" target="_blank">Example (opens in new tab)</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_target_blank_with_aria_label(): void {
        $html = '<html><body><a href="https://example.com" target="_blank" aria-label="Example (opens in new window)">Example</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_target_blank_with_sr_only(): void {
        $html = '<html><body><a href="https://example.com" target="_blank">Example <span class="sr-only">(opens in new tab)</span></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_target_blank_with_screen_reader_text(): void {
        $html = '<html><body><a href="https://example.com" target="_blank">Example <span class="screen-reader-text">opens in new window</span></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_external_keyword(): void {
        $html = '<html><body><a href="https://example.com" target="_blank">External link</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_multiple(): void {
        $html = '<html><body><a href="/a" target="_blank">A</a><a href="/b" target="_blank">B</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(2, $issues);
    }
}
