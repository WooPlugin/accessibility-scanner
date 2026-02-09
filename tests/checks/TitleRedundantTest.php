<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class TitleRedundantTest extends CheckTestCase {

    private ASFW_Check_Title_Redundant $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Title_Redundant();
    }

    public function test_pass_title_differs_from_text(): void {
        $html = '<html><body><a href="/" title="Go to homepage">Home</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_title_matches_text(): void {
        $html = '<html><body><a href="/" title="Home">Home</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('title-redundant', $issues[0]->check_id);
    }

    public function test_fail_title_matches_text_case_insensitive(): void {
        $html = '<html><body><a href="/" title="HOME">Home</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_title_matches_aria_label(): void {
        $html = '<html><body><button title="Close" aria-label="Close">X</button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_empty_title(): void {
        $html = '<html><body><a href="/" title="">Home</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_no_title_attr(): void {
        $html = '<html><body><a href="/">Home</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
