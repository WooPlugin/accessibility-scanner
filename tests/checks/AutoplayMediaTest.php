<?php

require_once dirname(__DIR__) . '/CheckTestCase.php';

class AutoplayMediaTest extends CheckTestCase {

    private ASFW_Check_Autoplay_Media $check;

    protected function setUp(): void {
        $this->check = new ASFW_Check_Autoplay_Media();
    }

    public function test_pass_video_without_autoplay(): void {
        $html = '<html><body><video src="clip.mp4" controls></video></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }

    public function test_fail_video_with_autoplay(): void {
        $html = '<html><body><video src="clip.mp4" autoplay></video></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
        $this->assertSame('autoplay-media', $issues[0]->check_id);
    }

    public function test_fail_audio_with_autoplay(): void {
        $html = '<html><body><audio src="song.mp3" autoplay></audio></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(1, $issues);
    }

    public function test_fail_multiple_autoplay(): void {
        $html = '<html><body><video autoplay src="a.mp4"></video><audio autoplay src="b.mp3"></audio></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(2, $issues);
    }

    public function test_pass_no_media(): void {
        $html = '<html><body><p>No media</p></body></html>';
        $issues = $this->runCheck($this->check, $html);
        $this->assertCount(0, $issues);
    }
}
