<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class EmptyLinksTest extends CheckTestCase {

    private ASFW_Check_Empty_Links $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Empty_Links();
    }

    public function test_pass_with_text(): void {
        $html = '<html><body><a href="/page">Click here</a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_aria_label(): void {
        $html = '<html><body><a href="/page" aria-label="Go to page"></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_aria_labelledby(): void {
        $html = '<html><body><span id="lnk">Page</span><a href="/page" aria-labelledby="lnk"></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_title(): void {
        $html = '<html><body><a href="/page" title="Go to page"></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_img_alt(): void {
        $html = '<html><body><a href="/page"><img src="icon.png" alt="Icon"></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_svg_title(): void {
        $html = '<html><body><a href="/page"><svg><title>Icon</title></svg></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_empty_link(): void {
        $html = '<html><body><a href="/page"></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('empty-links', $issues[0]->check_id);
    }

    public function test_fail_link_with_empty_img_alt(): void {
        $html = '<html><body><a href="/page"><img src="icon.png" alt=""></a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_whitespace_only(): void {
        $html = '<html><body><a href="/page">   </a></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_no_links(): void {
        $html = '<html><body><p>No links</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
