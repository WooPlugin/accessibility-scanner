<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class ImgAltEmptyTest extends CheckTestCase {

    private ASFW_Check_Img_Alt_Empty $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Img_Alt_Empty();
    }

    public function test_pass_decorative_image(): void {
        $html = '<html><body><img src="divider.png" alt=""></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_informative_keyword_in_src(): void {
        $html = '<html><body><img src="team-photo.jpg" alt=""></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('img-alt-empty', $issues[0]->check_id);
    }

    public function test_fail_logo_keyword(): void {
        $html = '<html><body><img src="company-logo.svg" alt=""></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_product_keyword(): void {
        $html = '<html><body><img src="product-image.jpg" alt=""></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_inside_link(): void {
        $html = '<html><body><a href="/about"><img src="decorative.png" alt=""></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_inside_figure(): void {
        $html = '<html><body><figure><img src="decorative.png" alt=""></figure></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_inside_article(): void {
        $html = '<html><body><article><img src="decorative.png" alt=""></article></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_no_images(): void {
        $html = '<html><body><p>No images</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_image_with_alt_text(): void {
        $html = '<html><body><img src="logo.png" alt="Company Logo"></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
