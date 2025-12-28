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
        // Lead CRUD Actions
        add_action('wp_ajax_aiac_save_lead', array($this, 'save_lead'));
        add_action('wp_ajax_aiac_get_lead', array($this, 'get_single_lead'));
        add_action('wp_ajax_aiac_delete_lead', array($this, 'delete_lead'));
        // Admission CRUD Actions
        add_action('wp_ajax_aiac_get_admissions', array($this, 'get_admissions'));
        add_action('wp_ajax_aiac_get_admission', array($this, 'get_single_admission'));
        add_action('wp_ajax_aiac_save_admission', array($this, 'save_admission'));
        add_action('wp_ajax_aiac_delete_admission', array($this, 'delete_admission'));
        add_action('wp_ajax_aiac_get_admission_stats', array($this, 'get_admission_stats'));
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
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        
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

    /** 4. Save Lead (Add/Edit) */
    public function save_lead() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        
        $lead_id = isset($_POST['lead_id']) ? intval($_POST['lead_id']) : 0;
        $data = array(
            'student_name'      => sanitize_text_field($_POST['student_name']),
            'phone_number'      => sanitize_text_field($_POST['phone_number']),
            'course_id'         => sanitize_text_field($_POST['course_id']),
            'language_detected' => sanitize_text_field($_POST['language_detected']),
            'status'            => sanitize_text_field($_POST['status'])
        );

        if (empty($data['student_name']) || empty($data['phone_number'])) {
            wp_send_json_error('Name and phone are required');
        }

        $db = new AIAC_DB();
        
        if ($lead_id > 0) {
            $result = $db->update_data('leads', $data, array('id' => $lead_id));
        } else {
            $result = $db->insert_data('leads', $data);
        }

        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Database error');
        }
    }

    /** 5. Get Single Lead */
    public function get_single_lead() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        
        $lead_id = intval($_POST['lead_id']);
        $db = new AIAC_DB();
        $lead = $db->get_lead($lead_id);

        if ($lead) {
            wp_send_json_success($lead);
        } else {
            wp_send_json_error('Lead not found');
        }
    }

    /** 6. Delete Lead */
    public function delete_lead() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        
        $lead_id = intval($_POST['lead_id']);
        $db = new AIAC_DB();
        $result = $db->delete_data('leads', array('id' => $lead_id));

        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Could not delete');
        }
    }

    /** 7. Get All Admissions */
    public function get_admissions() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        $db = new AIAC_DB();
        $admissions = $db->fetch_all_admissions();
        wp_send_json_success($admissions ?: array());
    }

    /** 8. Get Single Admission */
    public function get_single_admission() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        $id = intval($_POST['admission_id']);
        $db = new AIAC_DB();
        $admission = $db->get_admission($id);
        if ($admission) {
            wp_send_json_success($admission);
        } else {
            wp_send_json_error('Not found');
        }
    }

    /** 9. Save Admission */
    public function save_admission() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        
        $admission_id = isset($_POST['admission_id']) ? intval($_POST['admission_id']) : 0;
        $data = array(
            'lead_id'          => intval($_POST['lead_id']),
            'total_fee'        => floatval($_POST['total_fee']),
            'paid_amount'      => floatval($_POST['paid_amount']),
            'due_date'         => sanitize_text_field($_POST['due_date']),
            'admission_status' => sanitize_text_field($_POST['admission_status'])
        );

        if (empty($data['lead_id']) || $data['total_fee'] <= 0) {
            wp_send_json_error('Lead and fee are required');
        }

        $db = new AIAC_DB();
        
        if ($admission_id > 0) {
            $result = $db->update_data('admissions', $data, array('id' => $admission_id));
        } else {
            $result = $db->insert_data('admissions', $data);
        }

        if ($result !== false) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Database error');
        }
    }

    /** 10. Delete Admission */
    public function delete_admission() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        $id = intval($_POST['admission_id']);
        $db = new AIAC_DB();
        $result = $db->delete_data('admissions', array('id' => $id));
        if ($result) {
            wp_send_json_success();
        } else {
            wp_send_json_error('Could not delete');
        }
    }

    /** 11. Get Admission Stats */
    public function get_admission_stats() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');
        $db = new AIAC_DB();
        $stats = $db->get_admission_stats();
        wp_send_json_success($stats);
    }
}
new AIAC_AJAX();
// ✅ Syntax verified block end
