<?php
/*
Plugin Name: AI Admission Counselor
Description: A smart AI-based admission guide and finance manager.
Version: 1.0.1
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
                <div class="aiac-stat-value" id="stat-total-leads">...</div>
                <span class="aiac-stat-label">Initial Inquiries</span>
            </div>
            <div class="aiac-card">
                <h3>Admissions</h3>
                <div class="aiac-stat-value" id="stat-total-admissions">...</div>
                <span class="aiac-stat-label">Confirmed Students</span>
            </div>
            <div class="aiac-card">
                <h3>Total Revenue</h3>
                <div class="aiac-stat-value" id="stat-total-revenue">$...</div>
                <span class="aiac-stat-label">Collected Fees</span>
            </div>
            <div class="aiac-card">
                <h3>Pending Balance</h3>
                <div class="aiac-stat-value" id="stat-pending-balance" style="color: #e74c3c;">$...</div>
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
                        <tr><td colspan="5" style="text-align:center;">Initialising system...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php
}

/** 5. Leads Manager Page Template */
function aiac_leads_page() {
    ?>
    <div id="aiac-leads-root" class="aiac-wrap">
        <header class="aiac-header">
            <div class="aiac-header-title">
                <h1>Leads Manager</h1>
                <p>Track and manage student inquiries. <span id="leads-debug-note" style="color:green; font-weight:bold;">(Live Sync Active)</span></p>
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
                    <tr><td colspan="7" style="text-align:center;">Connecting to AI Data Stream...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}

function aiac_admissions_page() { echo '<div class="wrap"><h1>Admissions Manager</h1></div>'; }
function aiac_payments_page() { echo '<div class="wrap"><h1>Payments & Financials</h1></div>'; }
function aiac_settings_page() { echo '<div class="wrap"><h1>Plugin Settings</h1></div>'; }

/** 6. Forced Inline Data Loader (iPad Cache-Proof) */
add_action('admin_footer', 'aiac_force_load_script_fixed');
function aiac_force_load_script_fixed() {
    $screen = get_current_screen();
    // صرف ہمارے پلگ ان کے پیجز پر چلائیں
    if ( strpos($screen->id, 'ai-admission-counselor') === false && strpos($screen->id, 'aiac-') === false ) return;
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        const aiac_nonce = '<?php echo wp_create_nonce("aiac_secure_nonce"); ?>';
        
        // --- 1. Dashboard Fetch ---
        if ($('#aiac-dashboard-root').length > 0) {
            $.post(ajaxurl, {
                action: 'aiac_get_dashboard_stats',
                nonce: aiac_nonce
            }, function(res) {
                if (res.success) {
                    $('#stat-total-leads').text(res.data.total_leads);
                    $('#stat-total-admissions').text(res.data.total_admissions);
                    $('#stat-total-revenue').text('$' + res.data.total_revenue);
                    $('#stat-pending-balance').text('$' + res.data.pending_balance);
                    
                    let rows = '';
                    if(res.data.recent_admissions.length > 0) {
                        res.data.recent_admissions.forEach(item => {
                            rows += `<tr><td>${item.student_name}</td><td>${item.course_name}</td><td>${item.status}</td><td>$${item.balance}</td><td><button class="aiac-btn-sm">View</button></td></tr>`;
                        });
                    } else {
                        rows = '<tr><td colspan="5" style="text-align:center;">No recent admissions found.</td></tr>';
                    }
                    $('#aiac-recent-admissions-list').html(rows);
                }
            });
        }

        // --- 2. Leads Manager Fetch ---
        if ($('#aiac-leads-root').length > 0) {
            $.post(ajaxurl, {
                action: 'aiac_get_leads',
                nonce: aiac_nonce
            }, function(res) {
                if (res.success) {
                    let html = '';
                    if(res.data.length > 0) {
                        res.data.forEach(lead => {
                            let name = lead.student_name || lead.name;
                            let date = lead.created_at || lead.date;
                            let phone = lead.phone_number || lead.phone;
                            let course = lead.course_id || lead.course;
                            let lang = lead.language_detected || lead.lang;
                            
                            html += `<tr>
                                <td>${date}</td>
                                <td><strong>${name}</strong></td>
                                <td>${phone}</td>
                                <td>${course}</td>
                                <td>${lang}</td>
                                <td><span class="status-badge status-new">${lead.status}</span></td>
                                <td><button class="aiac-btn-sm aiac-btn-primary">Connect</button></td>
                            </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="7" style="text-align:center;">No leads found in database.</td></tr>';
                    }
                    $('#aiac-leads-list').html(html);
                }
            });
        }
    });
    </script>
    <?php
}
// ✅ Syntax verified block end
