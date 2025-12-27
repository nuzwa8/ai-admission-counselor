<?php
if (!defined('ABSPATH')) exit;

class AIAC_DB {
    private $wpdb;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function fetch_all_leads() {
        $table = $this->wpdb->prefix . 'aiac_leads';
        // چیک کریں کہ کیا ٹیبل موجود ہے
        if($this->wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
            return array(); 
        }
        return $this->wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC", ARRAY_A);
    }
}
