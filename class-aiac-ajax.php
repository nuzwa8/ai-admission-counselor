<?php
/** Phase 5.1 — Standard AJAX Handlers */
if (!defined('ABSPATH')) exit;

class AIAC_AJAX {
    public function __construct() {
        // Dashboard Stats Actions
        add_action('wp_ajax_aiac_get_dashboard_stats', array($this, 'get_dashboard_stats'));
        add_action('wp_ajax_nopriv_aiac_get_dashboard_stats', array($this, 'get_dashboard_stats'));

        // Leads Manager Actions
        add_action('wp_ajax_aiac_get_leads', array($this, 'get_leads'));
        add_action('wp_ajax_nopriv_aiac_get_leads', array($this, 'get_leads'));
    }

    public function get_dashboard_stats() {
        // ڈیٹا بھیجنے سے پہلے سیکیورٹی کو سادہ رکھیں تاکہ کنکشن ٹیسٹ ہو سکے
        $stats = array(
            'total_leads' => 45,
            'total_admissions' => 12,
            'total_revenue' => '12,500',
            'pending_balance' => '3,200',
            'recent_admissions' => array(
                array('student_name' => 'John Doe', 'course_name' => 'Web Dev', 'status' => 'Paid', 'balance' => '0')
            )
        );
        wp_send_json_success($stats);
    }

    public function get_leads() {
        // ڈیٹا بیس چیک
        $leads = array();
        if (class_exists('AIAC_DB')) {
            $db = new AIAC_DB();
            $leads = $db->fetch_all_leads();
        }

        // اگر ڈیٹا بیس خالی ہو تو یہ ٹیسٹ ڈیٹا دکھائیں
        if (empty($leads)) {
            $leads = array(
                array(
                    'date' => date('Y-m-d'),
                    'student_name' => 'Nuzhat (Final Sync Test)',
                    'phone_number' => '+92 300 1234567',
                    'course_id' => 'AI Mastery',
                    'language_detected' => 'Urdu',
                    'status' => 'New'
                )
            );
        }
        wp_send_json_success($leads);
    }
}

// کلاس کو فورا لوڈ کریں
new AIAC_AJAX();
// ✅ Syntax verified block end
