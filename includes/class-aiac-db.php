<?php
/** Part 2 — Database Helper Class */
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

    // Insert Lead or Admission
    public function insert_data($table, $data) {
        return $this->wpdb->insert($this->get_table($table), $data);
    }
}
// ✅ Syntax verified block end
