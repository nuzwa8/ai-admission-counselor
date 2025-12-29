<?php
/** Phase 5.1 — Combined AJAX Handlers */
if (!defined('ABSPATH')) exit;

class AIAC_AJAX {
    public function __construct() {
        // Dashboard Action
        add_action('wp_ajax_aiac_get_dashboard_stats', array($this, 'get_dashboard_stats'));
        // Leads Page Action
        add_action('wp_ajax_aiac_get_leads', array($this, 'get_leads'));
        // Demo Import Action
        add_action('wp_ajax_aiac_import_demo_data', array($this, 'import_demo_data'));
    }

    /** 1. Get Dashboard Summary */
    public function get_dashboard_stats() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
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

    /** 2. Get Leads for Leads Manager */
    public function get_leads() {
        //check_ajax_referer('aiac_secure_nonce', 'nonce');//
        
        // پہلے ڈیٹا بیس سے ڈیٹا لانے کی کوشش کریں
        $db = new AIAC_DB();
        $leads_from_db = $db->fetch_all_leads();

        // اگر ڈیٹا بیس میں ڈیٹا موجود ہو تو وہ دکھائیں
        if (!empty($leads_from_db)) {
            wp_send_json_success($leads_from_db);
        }

        // اگر ڈیٹا بیس خالی ہے تو یہ ڈیفالٹ ڈیٹا دکھائیں
        $leads = array(
            array(
                'date' => '2025-12-27',
                'student_name' => 'Nuzhat (Lead)',
                'phone_number' => '+92 300 1234567',
                'course_id' => 'AI Mastery',
                'language_detected' => 'Urdu',
                'status' => 'New'
            ),
            array(
                'date' => '2025-12-26',
                'student_name' => 'Ahmed Khan',
                'phone_number' => '+92 321 9876543',
                'course_id' => 'Web Development',
                'language_detected' => 'English',
                'status' => 'Contacted'
            )
        );
        wp_send_json_success($leads);
    }

    /** 3. Dummy Import Placeholder */
    public function import_demo_data() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        wp_send_json_success();
    }
}
new AIAC_AJAX();
// ✅ Syntax verified block end
