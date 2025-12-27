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

// 1. Activation Hook
require_once AIAC_PATH . 'class-aiac-activator.php';
register_activation_hook(__FILE__, array('AIAC_Activator', 'activate'));

// 2. Load Core Files
require_once AIAC_PATH . 'class-aiac-assets.php';
require_once AIAC_PATH . 'class-aiac-ajax.php';
require_once AIAC_PATH . 'class-aiac-db.php';

// 3. Admin Menu Setup
add_action('admin_menu', 'aiac_create_admin_menu');

function aiac_create_admin_menu() {
    add_menu_page(
        'AI Admission Counselor',
        'AI Counselor',
        'manage_options',
        'ai-admission-counselor',
        'aiac_dashboard_page',
        'dashicons-welcome-learn-more',
        25
    );

    add_submenu_page('ai-admission-counselor', 'Dashboard', 'Dashboard', 'manage_options', 'ai-admission-counselor', 'aiac_dashboard_page');
    add_submenu_page('ai-admission-counselor', 'Leads Manager', 'Leads', 'manage_options', 'aiac-leads', 'aiac_leads_page');
    add_submenu_page('ai-admission-counselor', 'Admissions', 'Admissions', 'manage_options', 'aiac-admissions', 'aiac_admissions_page');
    add_submenu_page('ai-admission-counselor', 'Payments', 'Payments', 'manage_options', 'aiac-payments', 'aiac_payments_page');
    add_submenu_page('ai-admission-counselor', 'Settings', 'Settings', 'manage_options', 'aiac-settings', 'aiac_settings_page');
}

/** 4. Dashboard Template */
function aiac_dashboard_page() {
    ?>
    <div id="aiac-dashboard-root" class="aiac-wrap">
        <header class="aiac-header">
            <div class="aiac-header-title">
                <h1>AI Admission Dashboard</h1>
                <p>Overview of leads, admissions, and financial performance.</p>
            </div>
            <div class="aiac-header-actions">
                <button class="aiac-btn aiac-btn-secondary" id="aiac-import-demo">Import Demo Data</button>
                <button class="aiac-btn aiac-btn-primary" id="aiac-export-excel">Export to Excel</button>
            </div>
        </header>

        <div class="aiac-stats-grid">
            <div class="aiac-card">
                <h3>Total Leads</h3>
                <div class="aiac-stat-value" id="stat-total-leads">0</div>
                <span class="aiac-stat-label">Initial Inquiries</span>
            </div>
            <div class="aiac-card">
                <h3>Admissions</h3>
                <div class="aiac-stat-value" id="stat-total-admissions">0</div>
                <span class="aiac-stat-label">Confirmed Students</span>
            </div>
            <div class="aiac-card">
                <h3>Total Revenue</h3>
                <div class="aiac-stat-value" id="stat-total-revenue">$0</div>
                <span class="aiac-stat-label">Collected Fees</span>
            </div>
            <div class="aiac-card">
                <h3>Pending Balance</h3>
                <div class="aiac-stat-value" id="stat-pending-balance" style="color: #e74c3c;">$0</div>
                <span class="aiac-stat-label">Next Due: <strong id="next-due-date">N/A</strong></span>
            </div>
        </div>

        <div class="aiac-content-section">
            <div class="aiac-card aiac-table-card">
                <div class="card-header">
                    <h2>Recent Admissions</h2>
                    <button class="aiac-btn-text" onclick="window.print()">Print Report</button>
                </div>
                <table class="aiac-table">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="aiac-recent-admissions-list">
                        <tr><td colspan="5" style="text-align:center;">Loading dynamic data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

/** 5. Empty Temp Functions for Other Pages */
/** Part 7 — Leads Manager Page Template */
function aiac_leads_page() {
    ?>
    <div id="aiac-leads-root" class="aiac-wrap">
        <header class="aiac-header">
            <div class="aiac-header-title">
                <h1>Leads Manager</h1>
                <p>Track and manage initial student inquiries via AI Counselor.</p>
            </div>
            <div class="aiac-header-actions">
                <button class="aiac-btn aiac-btn-primary" id="aiac-add-lead-btn">+ Add New Lead</button>
                <button class="aiac-btn aiac-btn-secondary" onclick="window.print()">Print List</button>
            </div>
        </header>

        <div class="aiac-card aiac-table-card">
            <table class="aiac-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student Name</th>
                        <th>Phone Number</th>
                        <th>Interested Course</th>
                        <th>Language</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="aiac-leads-list">
                    <tr><td colspan="7" style="text-align:center;">Fetching leads from database...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}
// ✅ Syntax verified block end
function aiac_admissions_page() { echo '<div class="wrap"><h1>Admissions</h1></div>'; }
function aiac_payments_page() { echo '<div class="wrap"><h1>Payments</h1></div>'; }
function aiac_settings_page() { echo '<div class="wrap"><h1>Settings</h1></div>'; }

// ✅ Syntax verified block end
/** Part 8 — Inline Script for Data Loading (iPad Fix) */
add_action('admin_footer', 'aiac_force_load_script');
function aiac_force_load_script() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        console.log("AIAC Script Triggered");

        // 1. Dashboard Logic
        if ($('#aiac-dashboard-root').length > 0) {
            $.post(ajaxurl, {
                action: 'aiac_get_dashboard_stats',
                nonce: '<?php echo wp_create_nonce("aiac_secure_nonce"); ?>'
            }, function(res) {
                if (res.success) {
                    $('#stat-total-leads').text(res.data.total_leads);
                    $('#stat-total-admissions').text(res.data.total_admissions);
                    $('#stat-total-revenue').text('$' + res.data.total_revenue);
                    $('#stat-pending-balance').text('$' + res.data.pending_balance);
                    
                    let rows = '';
                    res.data.recent_admissions.forEach(item => {
                        rows += `<tr><td>${item.student_name}</td><td>${item.course_name}</td><td>${item.status}</td><td>$${item.balance}</td><td><button class="aiac-btn-sm">View</button></td></tr>`;
                    });
                    $('#aiac-recent-admissions-list').html(rows);
                }
            });
        }

        // 2. Leads Page Logic
        if ($('#aiac-leads-root').length > 0) {
            $.post(ajaxurl, {
                action: 'aiac_get_leads',
                nonce: '<?php echo wp_create_nonce("aiac_secure_nonce"); ?>'
            }, function(res) {
                if (res.success) {
                    let html = '';
                    res.data.forEach(lead => {
                        html += `<tr>
                            <td>${lead.date || lead.created_at}</td>
                            <td><strong>${lead.name || lead.student_name}</strong></td>
                            <td>${lead.phone || lead.phone_number}</td>
                            <td>${lead.course || lead.course_id}</td>
                            <td>${lead.lang || lead.language_detected}</td>
                            <td><span class="status-badge status-new">${lead.status}</span></td>
                            <td><button class="aiac-btn-sm aiac-btn-primary">View</button></td>
                        </tr>`;
                    });
                    $('#aiac-leads-list').html(html);
                }
            });
        }
    });
    </script>
    <?php
}
