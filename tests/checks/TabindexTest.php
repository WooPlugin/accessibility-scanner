<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class TabindexTest extends CheckTestCase {

    private ASFW_Check_Tabindex $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Tabindex();
    }

    public function test_pass_tabindex_zero(): void {
        $html = '<html><body><div tabindex="0">Focusable</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_tabindex_negative(): void {
        $html = '<html><body><div tabindex="-1">Programmatic focus</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_positive_tabindex(): void {
        $html = '<html><body><input tabindex="5" type="text"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('tabindex', $issues[0]->check_id);
    }

    public function test_fail_multiple_positive(): void {
        $html = '<html><body><input tabindex="1"><input tabindex="2"><input tabindex="0"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(2, $issues);
    }

    public function test_pass_no_tabindex(): void {
        $html = '<html><body><button>Click</button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
