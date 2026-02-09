<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class LandmarksTest extends CheckTestCase {

    private ASFW_Check_Landmarks $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Landmarks();
    }

    public function test_pass_with_main_element(): void {
        $html = '<html><body><main><p>Content</p></main></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_role_main(): void {
        $html = '<html><body><div role="main"><p>Content</p></div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_no_main(): void {
        $html = '<html><body><div><p>Content</p></div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('landmarks', $issues[0]->check_id);
    }

    public function test_fail_empty_body(): void {
        $html = '<html><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }
}
