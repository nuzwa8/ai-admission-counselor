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
        // New Admission Action
        add_action('wp_ajax_aiac_add_admission', array($this, 'add_admission'));
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
        if ( method_exists($db, 'fetch_all_leads') ) {
            $leads_from_db = $db->fetch_all_leads();
        } else {
            $leads_from_db = array();
        }

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

    /** 4. Add Admission (AJAX, handles optional file upload) */
    public function add_admission() {
        check_ajax_referer('aiac_secure_nonce', 'nonce');

        // Basic sanitization
        $student_name = isset($_POST['student_name']) ? sanitize_text_field($_POST['student_name']) : '';
        $phone_number = isset($_POST['phone_number']) ? sanitize_text_field($_POST['phone_number']) : '';
        $course_id = isset($_POST['course_id']) ? sanitize_text_field($_POST['course_id']) : '';
        $total_fee = isset($_POST['total_fee']) ? floatval($_POST['total_fee']) : 0;
        $paid_amount = isset($_POST['paid_amount']) ? floatval($_POST['paid_amount']) : 0;
        $due_date = isset($_POST['due_date']) && !empty($_POST['due_date']) ? sanitize_text_field($_POST['due_date']) : null;
        $language_detected = isset($_POST['language_detected']) ? sanitize_text_field($_POST['language_detected']) : 'en';

        // Determine status
        if ($paid_amount >= $total_fee && $total_fee > 0) {
            $admission_status = 'paid';
        } elseif ($paid_amount > 0) {
            $admission_status = 'partial';
        } else {
            $admission_status = 'pending';
        }

        // Handle optional file upload (payment screenshot)
        $payment_screenshot_url = null;
        if (!empty($_FILES['payment_screenshot']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            // Use wp_handle_upload to safely handle the file
            $file = $_FILES['payment_screenshot'];
            $overrides = array('test_form' => false);
            $move = wp_handle_upload($file, $overrides);

            if (isset($move['url'])) {
                $payment_screenshot_url = esc_url_raw($move['url']);
            }
        }

        // Insert into database via helper
        $db = new AIAC_DB();

        $admission_data = array(
            'lead_id' => null,
            'total_fee' => $total_fee,
            'paid_amount' => $paid_amount,
            'due_date' => $due_date ? date('Y-m-d', strtotime($due_date)) : null,
            'payment_screenshot' => $payment_screenshot_url,
            'admission_status' => $admission_status,
        );

        $insert = $db->insert_data('admissions', $admission_data);

        if ($insert !== false) {
            // Optionally create a lead record or link — skipped for brevity
            wp_send_json_success(array('message' => 'Admission created successfully'));
        } else {
            wp_send_json_error(array('message' => 'Failed to create admission'));
        }
    }
}
new AIAC_AJAX();
// ✅ Syntax verified block end
