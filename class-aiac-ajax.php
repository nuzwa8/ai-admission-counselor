<?php
/** Part 2 — Dashboard AJAX Handlers */
if (!defined('ABSPATH')) exit;

class AIAC_AJAX {
    public function __construct() {
        add_action('wp_ajax_aiac_get_dashboard_stats', array($this, 'get_dashboard_stats'));
        add_action('wp_ajax_aiac_import_demo_data', array($this, 'import_demo_data'));
    }

    public function get_dashboard_stats() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');

        // Initial Dummy Data for UI Testing (Will be replaced with SQL later)
        $stats = array(
            'total_leads' => 45,
            'total_admissions' => 12,
            'total_revenue' => '12,500',
            'pending_balance' => '3,200',
            'recent_admissions' => array(
                array('student_name' => 'John Doe', 'course_name' => 'Web Dev', 'status' => 'Paid', 'balance' => '0'),
                array('student_name' => 'Sara Ali', 'course_name' => 'Graphic Design', 'status' => 'Partial', 'balance' => '500')
            )
        );

        wp_send_json_success($stats);
    }

    public function import_demo_data() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        // Placeholder for demo import logic
        wp_send_json_success();
    }
}
new AIAC_AJAX();
// ✅ Syntax verified block end
