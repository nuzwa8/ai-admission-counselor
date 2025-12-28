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
            total_fee decimal(10,2) NOT NULL,
            paid_amount decimal(10,2) DEFAULT 0,
            due_date date DEFAULT NULL,
            payment_screenshot varchar(255) DEFAULT NULL,
            admission_status varchar(50) DEFAULT 'pending',
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql1);
        dbDelta($sql2);

        // ✅ Add Custom Role: Admission Manager
        add_role('admission_manager', 'Admission Manager', [
            'read' => true,
            'edit_posts' => false,
            'manage_options' => false,
        ]);
    }
}
// ✅ Syntax verified block end
