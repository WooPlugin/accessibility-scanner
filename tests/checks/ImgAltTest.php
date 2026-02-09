<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class ImgAltTest extends CheckTestCase {

    private ASFW_Check_Img_Alt $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Img_Alt();
    }

    public function test_pass_with_alt(): void {
        $html = '<html><body><img src="photo.jpg" alt="A photo"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_empty_alt(): void {
        // Empty alt is valid (decorative image).
        $html = '<html><body><img src="decoration.png" alt=""></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_missing_alt(): void {
        $html = '<html><body><img src="photo.jpg"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('img-alt', $issues[0]->check_id);
    }

    public function test_fail_multiple_images(): void {
        $html = '<html><body><img src="a.jpg"><img src="b.jpg"><img src="c.jpg" alt="ok"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(2, $issues);
    }

    public function test_skip_tracking_pixel_width(): void {
        $html = '<html><body><img src="pixel.gif" width="1" height="1"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_no_skip_tracking_pixel_zero(): void {
        // PHP treats "0" as falsy, so the width/height check short-circuits.
        // A 0x0 image without alt is still flagged.
        $html = '<html><body><img src="pixel.gif" width="0" height="0"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_does_not_skip_normal_sized_image(): void {
        $html = '<html><body><img src="photo.jpg" width="200" height="150"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_no_images(): void {
        $html = '<html><body><p>No images here</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
