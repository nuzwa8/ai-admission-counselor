<?php
/**
 * Database Helper Class
 * @package AI_Admission_Counselor
 */
if (!defined('ABSPATH')) exit;

class AIAC_DB {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    // Get table name with prefix
    public function get_table($name) {
        return $this->wpdb->prefix . 'aiac_' . $name;
    }

    // Insert data
    public function insert_data($table, $data) {
        $result = $this->wpdb->insert($this->get_table($table), $data);
        return $result ? $this->wpdb->insert_id : false;
    }

    // Update data
    public function update_data($table, $data, $where) {
        return $this->wpdb->update($this->get_table($table), $data, $where);
    }

    // Delete data
    public function delete_data($table, $where) {
        return $this->wpdb->delete($this->get_table($table), $where);
    }

    // Fetch all leads
    public function fetch_all_leads($limit = 100) {
        $table = $this->get_table('leads');
        return $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT * FROM $table ORDER BY created_at DESC LIMIT %d", $limit),
            ARRAY_A
        );
    }

    // Fetch single lead
    public function get_lead($id) {
        $table = $this->get_table('leads');
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id),
            ARRAY_A
        );
    }

    // Fetch all admissions
    public function fetch_all_admissions($limit = 100) {
        $table = $this->get_table('admissions');
        $leads_table = $this->get_table('leads');
        $courses_table = $this->get_table('courses');
        return $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT a.*, l.student_name, l.phone_number, c.course_name 
                 FROM $table a 
                 LEFT JOIN $leads_table l ON a.lead_id = l.id 
                 LEFT JOIN $courses_table c ON a.course_id = c.id 
                 ORDER BY a.id DESC LIMIT %d", 
                $limit
            ),
            ARRAY_A
        );
    }

    // Get dashboard stats
    public function get_dashboard_stats() {
        $leads_table = $this->get_table('leads');
        $admissions_table = $this->get_table('admissions');

        return array(
            'total_leads' => (int) $this->wpdb->get_var("SELECT COUNT(*) FROM $leads_table"),
            'total_admissions' => (int) $this->wpdb->get_var("SELECT COUNT(*) FROM $admissions_table"),
            'total_revenue' => (float) $this->wpdb->get_var("SELECT COALESCE(SUM(paid_amount), 0) FROM $admissions_table"),
            'pending_balance' => (float) $this->wpdb->get_var("SELECT COALESCE(SUM(total_fee - paid_amount), 0) FROM $admissions_table")
        );
    }

    // Count leads by status
    public function count_leads_by_status($status) {
        $table = $this->get_table('leads');
        return (int) $this->wpdb->get_var(
            $this->wpdb->prepare("SELECT COUNT(*) FROM $table WHERE status = %s", $status)
        );
    }

    // Get single admission
    public function get_admission($id) {
        $table = $this->get_table('admissions');
        $leads_table = $this->get_table('leads');
        $courses_table = $this->get_table('courses');
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT a.*, l.student_name, l.phone_number, c.course_name 
                 FROM $table a 
                 LEFT JOIN $leads_table l ON a.lead_id = l.id 
                 LEFT JOIN $courses_table c ON a.course_id = c.id 
                 WHERE a.id = %d", 
                $id
            ),
            ARRAY_A
        );
    }

    // Get admission stats
    public function get_admission_stats() {
        $table = $this->get_table('admissions');
        return array(
            'count'     => (int) $this->wpdb->get_var("SELECT COUNT(*) FROM $table"),
            'total_fee' => number_format((float) $this->wpdb->get_var("SELECT COALESCE(SUM(total_fee), 0) FROM $table")),
            'collected' => number_format((float) $this->wpdb->get_var("SELECT COALESCE(SUM(paid_amount), 0) FROM $table")),
            'pending'   => number_format((float) $this->wpdb->get_var("SELECT COALESCE(SUM(total_fee - paid_amount), 0) FROM $table"))
        );
    }

    // ========== COURSES MANAGEMENT ==========

    // Fetch all active courses
    public function fetch_all_courses($active_only = true) {
        $table = $this->get_table('courses');
        $sql = "SELECT * FROM $table";
        if ($active_only) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY course_name ASC";
        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    // Get single course
    public function get_course($id) {
        $table = $this->get_table('courses');
        return $this->wpdb->get_row(
            $this->wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id),
            ARRAY_A
        );
    }

    // Save course (Add/Edit)
    public function save_course($data, $course_id = 0) {
        $table = $this->get_table('courses');
        
        if ($course_id > 0) {
            return $this->update_data('courses', $data, array('id' => $course_id));
        } else {
            return $this->insert_data('courses', $data);
        }
    }

    // Delete course (soft delete - set is_active = 0)
    public function delete_course($id) {
        return $this->update_data('courses', array('is_active' => 0), array('id' => $id));
    }

    // Get course by ID (for display)
    public function get_course_name($course_id) {
        if (empty($course_id)) return '-';
        $course = $this->get_course($course_id);
        return $course ? $course['course_name'] : '-';
    }
}
