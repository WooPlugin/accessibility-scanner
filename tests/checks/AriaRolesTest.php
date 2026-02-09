<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class AriaRolesTest extends CheckTestCase {

    private ASFW_Check_Aria_Roles $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Aria_Roles();
    }

    public function test_pass_valid_role(): void {
        $html = '<html><body><div role="navigation">Nav</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_button_role(): void {
        $html = '<html><body><span role="button">Click</span></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_invalid_role(): void {
        $html = '<html><body><div role="foobar">Content</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('aria-roles', $issues[0]->check_id);
    }

    public function test_fail_typo_role(): void {
        $html = '<html><body><nav role="naviagtion">Nav</nav></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_presentation_role(): void {
        $html = '<html><body><img role="presentation" src="dec.png"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_empty_role(): void {
        $html = '<html><body><div role="">Content</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_no_roles(): void {
        $html = '<html><body><div>Content</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
