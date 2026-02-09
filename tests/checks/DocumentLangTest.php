<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class DocumentLangTest extends CheckTestCase {

    private ASFW_Check_Document_Lang $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Document_Lang();
    }

    public function test_pass_with_lang(): void {
        $html = '<html lang="en"><head><title>Test</title></head><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_pass_with_non_english_lang(): void {
        $html = '<html lang="fr"><head><title>Test</title></head><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_missing_lang(): void {
        $html = '<html><head><title>Test</title></head><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('document-lang', $issues[0]->check_id);
    }

    public function test_fail_empty_lang(): void {
        $html = '<html lang=""><head><title>Test</title></head><body></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }
}
