<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class HeadingStructureTest extends CheckTestCase {

    private ASFW_Check_Heading_Structure $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Heading_Structure();
    }

    public function test_pass_correct_structure(): void {
        $html = '<html><body><h1>Title</h1><h2>Section</h2><h3>Sub</h3></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_h1_only(): void {
        $html = '<html><body><h1>Title</h1></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_decreasing_then_increasing(): void {
        // h1 -> h2 -> h3 -> h2 is fine (going back up is allowed).
        $html = '<html><body><h1>A</h1><h2>B</h2><h3>C</h3><h2>D</h2></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_missing_h1(): void {
        $html = '<html><body><h2>Section</h2><h3>Sub</h3></body></html>';
        $issues = $this->runCheck($this->check, $html);
        // Missing h1 + skipped level (h2 starts without h1 context, but the skip check
        // only triggers when current > previous + 1, so h2->h3 is fine).
        $this->assertGreaterThanOrEqual(1, count($issues));
        $this->assertSame('heading-structure', $issues[0]->check_id);
    }

    public function test_fail_skipped_level(): void {
        $html = '<html><body><h1>Title</h1><h3>Sub</h3></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertStringContainsString('h3', $issues[0]->message);
        $this->assertStringContainsString('h1', $issues[0]->message);
    }

    public function test_fail_multiple_skips(): void {
        $html = '<html><body><h1>Title</h1><h3>A</h3><h6>B</h6></body></html>';
        $issues = $this->runCheck($this->check, $html);
        // h1->h3 skip + h3->h6 skip = 2 issues.
        $this->assertCount(2, $issues);
    }

    public function test_no_headings_flags_missing_h1(): void {
        $html = '<html><body><p>No headings</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        // No headings at all still flags the missing h1.
        $this->assertCount(1, $issues);
        $this->assertStringContainsString('h1', $issues[0]->message);
    }
}
