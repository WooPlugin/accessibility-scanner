<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class TableHeadersTest extends CheckTestCase {

    private ASFW_Check_Table_Headers $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Table_Headers();
    }

    public function test_pass_table_with_th(): void {
        $html = '<html><body><table><tr><th>Name</th><th>Age</th></tr><tr><td>Alice</td><td>30</td></tr></table></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_table_without_th(): void {
        $html = '<html><body><table><tr><td>Alice</td><td>30</td></tr></table></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('table-headers', $issues[0]->check_id);
    }

    public function test_pass_empty_table(): void {
        // A table with no td cells is not a data table.
        $html = '<html><body><table></table></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_no_tables(): void {
        $html = '<html><body><p>No tables</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_multiple_tables(): void {
        $html = '<html><body><table><tr><td>A</td></tr></table><table><tr><td>B</td></tr></table></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(2, $issues);
    }
}
