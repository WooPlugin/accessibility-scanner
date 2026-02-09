<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class AriaReferencesTest extends CheckTestCase {

    private ASFW_Check_Aria_References $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Aria_References();
    }

    public function test_pass_valid_labelledby(): void {
        $html = '<html><body><span id="label1">Name</span><input aria-labelledby="label1"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_missing_labelledby_target(): void {
        $html = '<html><body><input aria-labelledby="nonexistent"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('aria-references', $issues[0]->check_id);
    }

    public function test_fail_missing_describedby_target(): void {
        $html = '<html><body><input aria-describedby="missing-id"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_valid_describedby(): void {
        $html = '<html><body><p id="desc">Help text</p><input aria-describedby="desc"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_partial_missing(): void {
        // One ID exists, one doesn't.
        $html = '<html><body><span id="a">A</span><input aria-labelledby="a b"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_multiple_valid_ids(): void {
        $html = '<html><body><span id="a">A</span><span id="b">B</span><input aria-labelledby="a b"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_no_aria_refs(): void {
        $html = '<html><body><input type="text"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
