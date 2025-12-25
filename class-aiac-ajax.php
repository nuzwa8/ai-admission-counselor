<?php
/** Part 4 — AJAX Handler */
if (!defined('ABSPATH')) exit;

class AIAC_AJAX {
    public function __construct() {
        add_action('wp_ajax_aiac_save_lead', array($this, 'handle_save_lead'));
    }

    public function handle_save_lead() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        
        // Basic AJAX Response for testing
        wp_send_json_success(array('message' => 'Connected successfully!'));
    }
}
new AIAC_AJAX();
// ✅ Syntax verified block end
