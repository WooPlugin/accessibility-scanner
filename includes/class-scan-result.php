<?php
/**
 * Scan result with scoring
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Scan_Result
 */
class ASFW_Scan_Result {

    /** @var string */
    public string $url;

    /** @var ASFW_Issue[] */
    public array $issues;

    /** @var int */
    public int $score;

    /** @var int */
    public int $critical_count = 0;

    /** @var int */
    public int $serious_count = 0;

    /** @var int */
    public int $moderate_count = 0;

    /** @var int */
    public int $minor_count = 0;

    /** @var float */
    public float $duration = 0.0;

    /**
     * Constructor
     *
     * @param string       $url    The scanned URL.
     * @param ASFW_Issue[] $issues Array of issues found.
     * @param float        $duration Scan duration in seconds.
     */
    public function __construct(string $url, array $issues, float $duration = 0.0) {
        $this->url      = $url;
        $this->issues   = $issues;
        $this->duration = $duration;

        $this->count_by_severity();
        $this->score = $this->calculate_score();
    }

    /**
     * Count issues by severity
     */
    private function count_by_severity(): void {
        foreach ($this->issues as $issue) {
            switch ($issue->impact) {
                case 'critical':
                    ++$this->critical_count;
                    break;
                case 'serious':
                    ++$this->serious_count;
                    break;
                case 'moderate':
                    ++$this->moderate_count;
                    break;
                case 'minor':
                    ++$this->minor_count;
                    break;
            }
        }
    }

    /**
     * Calculate accessibility score using weighted severity
     *
     * @return int Score 0-100
     */
    private function calculate_score(): int {
        $checks = ASFW_Check_Registry::get_checks();
        if (empty($checks)) {
            return 100;
        }

        $severity_weights = [
            'critical' => 3.0,
            'serious'  => 2.0,
            'moderate' => 1.0,
            'minor'    => 0.5,
        ];

        $total_weight  = 0.0;
        $passed_weight = 0.0;

        // Build set of check IDs that have issues.
        $failed_checks = [];
        foreach ($this->issues as $issue) {
            $failed_checks[$issue->check_id] = true;
        }

        foreach ($checks as $check) {
            $weight       = $severity_weights[$check->get_severity()] ?? 1.0;
            $total_weight += $weight;

            if (!isset($failed_checks[$check->get_id()])) {
                $passed_weight += $weight;
            }
        }

        if ($total_weight === 0.0) {
            return 100;
        }

        return (int) round(($passed_weight / $total_weight) * 100);
    }

    /**
     * Get total issue count
     *
     * @return int
     */
    public function get_total_issues(): int {
        return count($this->issues);
    }
}
