<?php
/**
 * Issue data transfer object
 *
 * @package Accessibility_Scanner_For_WordPress
 */

defined('ABSPATH') || exit;

/**
 * Class ASFW_Issue
 */
class ASFW_Issue {

    /** @var string */
    public string $check_id;

    /** @var string */
    public string $element;

    /** @var string */
    public string $selector;

    /** @var string */
    public string $message;

    /** @var string */
    public string $impact;

    /** @var string */
    public string $wcag;

    /** @var string */
    public string $fix_hint;

    /** @var string */
    public string $context;

    /**
     * Constructor
     *
     * @param array $data Associative array of issue data.
     */
    public function __construct(array $data) {
        $this->check_id = $data['check_id'] ?? '';
        $this->element  = $data['element'] ?? '';
        $this->selector = $data['selector'] ?? '';
        $this->message  = $data['message'] ?? '';
        $this->impact   = $data['impact'] ?? 'moderate';
        $this->wcag     = $data['wcag'] ?? '';
        $this->fix_hint = $data['fix_hint'] ?? '';
        $this->context  = $data['context'] ?? '';
    }

    /**
     * Convert to array for database storage
     *
     * @return array
     */
    public function to_array(): array {
        return [
            'check_id'     => $this->check_id,
            'element_html' => $this->element,
            'selector'     => $this->selector,
            'message'      => $this->message,
            'impact'       => $this->impact,
            'wcag_criterion' => $this->wcag,
            'fix_hint'     => $this->fix_hint,
            'context'      => $this->context,
        ];
    }
}
