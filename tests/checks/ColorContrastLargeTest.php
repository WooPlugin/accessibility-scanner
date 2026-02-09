<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class ColorContrastLargeTest extends CheckTestCase {

    private ASFW_Check_Color_Contrast_Large $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Color_Contrast_Large();
    }

    public function test_pass_good_contrast_large_text(): void {
        // Black on white, 24px = large text, 21:1 ratio.
        $html = '<html><body><p style="color: #000; background-color: #fff; font-size: 24px;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_low_contrast_large_text(): void {
        // Gray on white, 24px, ratio ~2:1.
        $html = '<html><body><p style="color: #999; background-color: #fff; font-size: 24px;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('color-contrast-large', $issues[0]->check_id);
    }

    public function test_pass_no_font_size(): void {
        // Without font-size, can't determine if large text - skip.
        $html = '<html><body><p style="color: #999; background-color: #fff;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_small_text_ignored(): void {
        // 12px is not large text - this check only applies to large text.
        $html = '<html><body><p style="color: #999; background-color: #fff; font-size: 12px;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_bold_large_text(): void {
        // 19px bold = large text (>= 18.66px bold). Black on white passes.
        $html = '<html><body><p style="color: #000; background-color: #fff; font-size: 19px; font-weight: bold;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_bold_large_text_low_contrast(): void {
        // 19px bold = large text. Gray on white fails at 3:1.
        $html = '<html><body><p style="color: #999; background-color: #fff; font-size: 19px; font-weight: 700;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_pt_units(): void {
        // 18pt = 24px = large text. Black on white passes.
        $html = '<html><body><p style="color: #000; background-color: #fff; font-size: 18pt;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_em_units(): void {
        // 1.5em = 24px (assuming 16px base) = large text.
        $html = '<html><body><p style="color: #000; background-color: #fff; font-size: 1.5em;">Text</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
