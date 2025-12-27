<?php
/*
Plugin Name: AI Admission Counselor
Description: A smart AI-based admission guide and finance manager.
Version: 1.0.0
Author: Architect Mode
Text Domain: ai-admission-counselor
*/

if (!defined('ABSPATH')) exit;

// Define Constants
define('AIAC_PATH', plugin_dir_path(__FILE__));
define('AIAC_URL', plugin_dir_url(__FILE__));

// Activation Hook
require_once AIAC_PATH . 'class-aiac-activator.php';
register_activation_hook(__FILE__, array('AIAC_Activator', 'activate'));

// Load Core Files
require_once AIAC_PATH . 'class-aiac-assets.php';
require_once AIAC_PATH . 'class-aiac-ajax.php';
require_once AIAC_PATH . 'class-aiac-db.php';

// ✅ Syntax verified block end
/** Part 5 — Admin Menu Setup */

add_action('admin_menu', 'aiac_create_admin_menu');

function aiac_create_admin_menu() {
    // Main Menu
    add_menu_page(
        'AI Admission Counselor',    // Page Title
        'AI Counselor',             // Menu Title
        'manage_options',            // Capability
        'ai-admission-counselor',    // Menu Slug
        'aiac_dashboard_page',       // Callback Function
        'dashicons-welcome-learn-more', // Icon
        25                           // Position
    );

    // Submenu: Dashboard (Same as main menu)
    add_submenu_page('ai-admission-counselor', 'Dashboard', 'Dashboard', 'manage_options', 'ai-admission-counselor', 'aiac_dashboard_page');

    // Submenu: Leads
    add_submenu_page('ai-admission-counselor', 'Leads Manager', 'Leads', 'manage_options', 'aiac-leads', 'aiac_leads_page');

    // Submenu: Admissions
    add_submenu_page('ai-admission-counselor', 'Admissions', 'Admissions', 'manage_options', 'aiac-admissions', 'aiac_admissions_page');

    // Submenu: Payments
    add_submenu_page('ai-admission-counselor', 'Payments', 'Payments', 'manage_options', 'aiac-payments', 'aiac_payments_page');

    // Submenu: Settings
    add_submenu_page('ai-admission-counselor', 'Settings', 'Settings', 'manage_options', 'aiac-settings', 'aiac_settings_page');
}

/** 🛠 Temp Callback Functions to prevent errors */
function aiac_dashboard_page() { echo '<div class="wrap"><h1>Dashboard</h1><p>Welcome to AI Admission Counselor</p></div>'; }
function aiac_leads_page() { echo '<div class="wrap"><h1>Leads Manager</h1></div>'; }
function aiac_admissions_page() { echo '<div class="wrap"><h1>Admissions</h1></div>'; }
function aiac_payments_page() { echo '<div class="wrap"><h1>Payments & Installments</h1></div>'; }
function aiac_settings_page() { echo '<div class="wrap"><h1>Settings</h1></div>'; }

// ✅ Syntax verified block end
