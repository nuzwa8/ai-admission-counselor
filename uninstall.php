<?php
/**
 * Fired when the plugin is uninstalled.
 * * This file cleans up all plugin-specific data from the database.
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

/**
 * 1. Delete Plugin Tables
 */
$table_leads      = $wpdb->prefix . 'aiac_leads';
$table_admissions = $wpdb->prefix . 'aiac_admissions';

// Delete both tables if they exist
$wpdb->query( "DROP TABLE IF EXISTS $table_leads" );
$wpdb->query( "DROP TABLE IF EXISTS $table_admissions" );

/**
 * 2. Delete Options/Settings (Optional)
 * اگر آپ نے کوئی سیٹنگز محفوظ کی ہیں تو انہیں یہاں سے ڈیلیٹ کریں
 */
delete_option( 'aiac_settings' );
delete_option( 'aiac_db_version' );

/**
 * 3. Clear Scheduled Cron Jobs (Optional)
 * اگر کوئی کرون جابز شیڈول کی تھیں تو انہیں صاف کریں
 */
wp_clear_scheduled_hook( 'aiac_daily_cleanup' );

// Final cleanup complete.
