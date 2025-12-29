<?php
/** * Part 1 — Database & Activation Logic
 * Handling Table Creation and Initial Setup
 */

if (!defined('ABSPATH')) exit;

class AIAC_Activator {

    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // 1. Table for Leads (Initial Inquiries)
        $table_leads = $wpdb->prefix . 'aiac_leads';
        $sql1 = "CREATE TABLE $table_leads (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            student_name varchar(100) NOT NULL,
            phone_number varchar(20) NOT NULL,
            course_id bigint(20) DEFAULT NULL,
            status varchar(50) DEFAULT 'new',
            language_detected varchar(20) DEFAULT 'en',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 2. Table for Admissions (Financial & Detailed Info)
        $table_admissions = $wpdb->prefix . 'aiac_admissions';
        $sql2 = "CREATE TABLE $table_admissions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            lead_id bigint(20) DEFAULT NULL,
            course_id bigint(20) DEFAULT NULL,
            total_fee decimal(10,2) NOT NULL,
            paid_amount decimal(10,2) DEFAULT 0,
            due_date date DEFAULT NULL,
            payment_screenshot varchar(255) DEFAULT NULL,
            admission_status varchar(50) DEFAULT 'pending',
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // 3. Table for Courses (Course Management)
        $table_courses = $wpdb->prefix . 'aiac_courses';
        $sql3 = "CREATE TABLE $table_courses (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            course_name varchar(150) NOT NULL,
            course_code varchar(50) DEFAULT NULL,
            duration varchar(50) DEFAULT NULL,
            fee decimal(10,2) DEFAULT 0,
            description text DEFAULT NULL,
            is_active tinyint(1) DEFAULT 1,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);
        dbDelta($sql2);
        dbDelta($sql3);

        // Insert default courses if table is empty
        $course_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_courses");
        if ($course_count == 0) {
            $default_courses = array(
                array('course_name' => 'Web Development', 'course_code' => 'WEB-101', 'duration' => '3 months', 'fee' => 15000),
                array('course_name' => 'Graphic Design', 'course_code' => 'GD-201', 'duration' => '2 months', 'fee' => 12000),
                array('course_name' => 'AI & Machine Learning', 'course_code' => 'AI-301', 'duration' => '6 months', 'fee' => 50000),
                array('course_name' => 'Mobile App Development', 'course_code' => 'APP-401', 'duration' => '4 months', 'fee' => 25000),
                array('course_name' => 'Digital Marketing', 'course_code' => 'DM-501', 'duration' => '2 months', 'fee' => 10000),
            );
            
            foreach ($default_courses as $course) {
                $wpdb->insert($table_courses, $course);
            }
        }

        // ✅ Add Custom Role: Admission Manager
        add_role('admission_manager', 'Admission Manager', [
            'read' => true,
            'edit_posts' => false,
            'manage_options' => false,
        ]);
    }
}
// ✅ Syntax verified block end
