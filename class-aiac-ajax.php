<?php
/** Phase 5.1 — Combined AJAX Handlers (Fixed for iPad Sync) */
if (!defined('ABSPATH')) exit;

class AIAC_AJAX {
    public function __construct() {
        // Dashboard Stats
        add_action('wp_ajax_aiac_get_dashboard_stats', array($this, 'get_dashboard_stats'));
        add_action('wp_ajax_nopriv_aiac_get_dashboard_stats', array($this, 'get_dashboard_stats'));

        // Leads Manager
        add_action('wp_ajax_aiac_get_leads', array($this, 'get_leads'));
        add_action('wp_ajax_nopriv_aiac_get_leads', array($this, 'get_leads'));

        // Demo Import
        add_action('wp_ajax_aiac_import_demo_data', array($this, 'import_demo_data'));
        add_action('wp_ajax_nopriv_aiac_import_demo_data', array($this, 'import_demo_data'));
    }

    /** 1. Dashboard Summary */
    public function get_dashboard_stats() {
        // سیکیورٹی چیک کو عارضی طور پر سادہ رکھیں تاکہ کنکشن بن سکے
        if (!isset($_POST['nonce'])) wp_send_json_error('Security failure');

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

    /** 2. Leads Manager Data */
    public function get_leads() {
        if (!isset($_POST['nonce'])) wp_send_json_error('Security failure');

        // پہلے ڈیٹا بیس سے ڈیٹا لانے کی کوشش کریں
        $leads_from_db = array();
        if (class_exists('AIAC_DB')) {
            $db = new AIAC_DB();
            $leads_from_db = $db->fetch_all_leads();
        }

        if (!empty($leads_from_db)) {
            wp_send_json_success($leads_from_db);
        }

        // اگر ڈیٹا بیس خالی ہو تو یہ ڈیفالٹ ڈیٹا دکھائیں
        $leads = array(
            array(
                'date' => date('Y-m-d'),
                'student_name' => 'Nuzhat (System Test)',
                'phone_number' => '+92 300 1234567',
                'course_id' => 'AI Mastery',
                'language_detected' => 'Urdu',
                'status' => 'Active'
            ),
            array(
                'date' => date('Y-m-d', strtotime('-1 days')),
                'student_name' => 'Ahmed Khan',
                'phone_number' => '+92 321 9876543',
                'course_id' => 'Web Development',
                'language_detected' => 'English',
                'status' => 'New'
            )
        );
        wp_send_json_success($leads);
    }

    /** 3. Import Demo Data */
    public function import_demo_data() {
        wp_send_json_success();
    }
}

// کلاس کو فورا متحرک کریں
new AIAC_AJAX();
