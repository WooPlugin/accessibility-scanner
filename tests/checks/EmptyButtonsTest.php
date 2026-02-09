<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class EmptyButtonsTest extends CheckTestCase {

    private ASFW_Check_Empty_Buttons $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Empty_Buttons();
    }

    public function test_pass_with_text(): void {
        $html = '<html><body><button>Submit</button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_aria_label(): void {
        $html = '<html><body><button aria-label="Close"></button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_aria_labelledby(): void {
        $html = '<html><body><span id="btn">Save</span><button aria-labelledby="btn"></button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_title(): void {
        $html = '<html><body><button title="Close dialog"></button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_img_alt(): void {
        $html = '<html><body><button><img src="icon.png" alt="Save"></button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_svg_title(): void {
        $html = '<html><body><button><svg><title>Close</title></svg></button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_role_button_with_text(): void {
        $html = '<html><body><div role="button">Click me</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_empty_button(): void {
        $html = '<html><body><button></button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('empty-buttons', $issues[0]->check_id);
    }

    public function test_fail_role_button_empty(): void {
        $html = '<html><body><div role="button"></div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_button_with_empty_img_alt(): void {
        $html = '<html><body><button><img src="icon.png" alt=""></button></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_no_buttons(): void {
        $html = '<html><body><p>No buttons</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
