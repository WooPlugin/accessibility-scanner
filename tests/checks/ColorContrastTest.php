<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class ColorContrastTest extends CheckTestCase {

    private ASFW_Check_Color_Contrast $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Color_Contrast();
    }

    public function test_pass_good_contrast(): void {
        // Black on white = 21:1.
        $html = '<html><body><p style="color: #000000; background-color: #ffffff;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_low_contrast(): void {
        // Light gray on white = ~1.5:1.
        $html = '<html><body><p style="color: #cccccc; background-color: #ffffff;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('color-contrast', $issues[0]->check_id);
    }

    public function test_pass_only_fg_color(): void {
        // Only foreground specified - not enough to evaluate.
        $html = '<html><body><p style="color: #cccccc;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_only_bg_color(): void {
        // Only background specified.
        $html = '<html><body><p style="background-color: #ffffff;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_rgb_format(): void {
        // Black on white via rgb().
        $html = '<html><body><p style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255);">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_rgb_low_contrast(): void {
        $html = '<html><body><p style="color: rgb(200, 200, 200); background-color: rgb(255, 255, 255);">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_shorthand_hex(): void {
        // #000 on #fff = 21:1.
        $html = '<html><body><p style="color: #000; background-color: #fff;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_no_inline_styles(): void {
        $html = '<html><body><p>No inline styles</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_background_shorthand(): void {
        $html = '<html><body><p style="color: #000; background: #fff;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
