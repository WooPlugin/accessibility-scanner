<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class SkipNavTest extends CheckTestCase {

    private ASFW_Check_Skip_Nav $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Skip_Nav();
    }

    public function test_pass_with_skip_link(): void {
        $html = '<html><body><a href="#main-content">Skip to content</a><nav>Nav</nav><main id="main-content">Content</main></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_skip_to_main(): void {
        $html = '<html><body><a href="#main">Skip to main</a><div id="main">Content</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_aria_label(): void {
        $html = '<html><body><a href="#content" aria-label="Skip to main content"></a><div id="content">Content</div></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_no_skip_link(): void {
        $html = '<html><body><nav><a href="/">Home</a></nav><main>Content</main></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('skip-nav', $issues[0]->check_id);
    }

    public function test_fail_link_without_hash(): void {
        $html = '<html><body><a href="/skip">Skip to content</a><main>Content</main></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_pass_no_body(): void {
        $html = '<html><head><title>Test</title></head></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
