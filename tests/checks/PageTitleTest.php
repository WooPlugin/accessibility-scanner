<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class PageTitleTest extends CheckTestCase {

    private ASFW_Check_Page_Title $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Page_Title();
    }

    public function test_pass_with_title(): void {
        $html = '<html><head><title>My Page</title></head><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_missing_title(): void {
        $html = '<html><head></head><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('page-title', $issues[0]->check_id);
    }

    public function test_fail_empty_title(): void {
        $html = '<html><head><title></title></head><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_whitespace_title(): void {
        $html = '<html><head><title>   </title></head><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_no_head(): void {
        $html = '<html><body><p>Hello</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }
}
