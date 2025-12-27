<?php
/** Part 1 — Database & Activation Logic (Fixed) */
if (!defined('ABSPATH')) exit;

class AIAC_Activator {
    public static function activate() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Leads Table
        $table_leads = $wpdb->prefix . 'aiac_leads';
        $sql1 = "CREATE TABLE $table_leads (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            student_name varchar(100) NOT NULL,
            phone_number varchar(20) NOT NULL,
            course_id varchar(100) DEFAULT NULL,
            status varchar(50) DEFAULT 'new',
            language_detected varchar(20) DEFAULT 'en',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql1);

        // Admissions Table
        $table_admissions = $wpdb->prefix . 'aiac_admissions';
        $sql2 = "CREATE TABLE $table_admissions (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            total_fee decimal(10,2) NOT NULL,
            paid_amount decimal(10,2) DEFAULT 0,
            due_date date DEFAULT NULL,
            admission_status varchar(50) DEFAULT 'pending',
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql2);
    }
}
