<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class FormLabelsTest extends CheckTestCase {

    private ASFW_Check_Form_Labels $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Form_Labels();
    }

    public function test_pass_with_label_for(): void {
        $html = '<html><body><label for="name">Name</label><input type="text" id="name"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_wrapping_label(): void {
        $html = '<html><body><label>Name <input type="text"></label></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_aria_label(): void {
        $html = '<html><body><input type="text" aria-label="Search"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_aria_labelledby(): void {
        $html = '<html><body><span id="lbl">Name</span><input type="text" aria-labelledby="lbl"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_title(): void {
        $html = '<html><body><input type="text" title="Enter name"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_skip_hidden_input(): void {
        $html = '<html><body><input type="hidden" name="token" value="abc"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_skip_submit_button(): void {
        $html = '<html><body><input type="submit" value="Go"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_skip_button_input(): void {
        $html = '<html><body><input type="button" value="Click"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_missing_label(): void {
        $html = '<html><body><input type="text"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('form-labels', $issues[0]->check_id);
    }

    public function test_fail_select_without_label(): void {
        $html = '<html><body><select><option>A</option></select></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_textarea_without_label(): void {
        $html = '<html><body><textarea></textarea></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_multiple_unlabeled(): void {
        $html = '<html><body><input type="text"><input type="email"><select><option>X</option></select></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(3, $issues);
    }
}
