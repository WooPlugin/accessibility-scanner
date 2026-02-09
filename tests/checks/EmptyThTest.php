<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class EmptyThTest extends CheckTestCase {

    private ASFW_Check_Empty_Th $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Empty_Th();
    }

    public function test_pass_th_with_text(): void {
        $html = '<html><body><table><tr><th>Name</th></tr><tr><td>Alice</td></tr></table></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_empty_th(): void {
        $html = '<html><body><table><tr><th></th><th>Name</th></tr><tr><td>1</td><td>Alice</td></tr></table></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('empty-th', $issues[0]->check_id);
    }

    public function test_fail_whitespace_only_th(): void {
        $html = '<html><body><table><tr><th>   </th></tr></table></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_no_tables(): void {
        $html = '<html><body><p>No tables</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_multiple_empty(): void {
        $html = '<html><body><table><tr><th></th><th></th><th>OK</th></tr></table></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(2, $issues);
    }
}
