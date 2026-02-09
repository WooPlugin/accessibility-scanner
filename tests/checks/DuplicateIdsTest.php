<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class DuplicateIdsTest extends CheckTestCase {

    private ASFW_Check_Duplicate_IDs $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Duplicate_IDs();
    }

    public function test_pass_unique_ids(): void {
        $html = '<html><body><div id="a"></div><div id="b"></div><div id="c"></div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_no_ids(): void {
        $html = '<html><body><div></div><p></p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_duplicate_id(): void {
        $html = '<html><body><div id="dup"></div><span id="dup"></span></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('duplicate-ids', $issues[0]->check_id);
        $this->assertStringContainsString('dup', $issues[0]->message);
    }

    public function test_fail_multiple_duplicate_ids(): void {
        $html = '<html><body><div id="x"></div><div id="x"></div><div id="y"></div><div id="y"></div><div id="y"></div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(2, $issues);
    }

    public function test_fail_triple_duplicate(): void {
        $html = '<html><body><div id="same"></div><p id="same"></p><span id="same"></span></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertStringContainsString('3', $issues[0]->message);
    }
}
