/** * AI Admission Counselor - Common JS
 * Integrated Dashboard & Leads Management
 */
(function($) {
    'use strict';

    const AIAC_App = {
        // 1. Initialization
        init: function() {
            this.initDashboard();
            this.initLeadsPage();
            this.initEventListeners();
        },

        // 2. Event Listeners
        initEventListeners: function() {
            // Demo Data Import Trigger
            $(document).on('click', '#aiac-import-demo', this.handleDemoImport.bind(this));
        },

        // 3. Dashboard Logic
        initDashboard: function() {
            const $root = $('#aiac-dashboard-root');
            if (!$root.length) return;
            this.fetchDashboardStats();
        },

        fetchDashboardStats: function() {
            $.ajax({
                url: aiacData.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiac_get_dashboard_stats',
                    nonce: aiacData.nonce
                },
                success: (response) => {
                    if (response.success) {
                        const data = response.data;
                        $('#stat-total-leads').text(data.total_leads);
                        $('#stat-total-admissions').text(data.total_admissions);
                        $('#stat-total-revenue').text('$' + data.total_revenue);
                        $('#stat-pending-balance').text('$' + data.pending_balance);
                        
                        if(data.recent_admissions) {
                            this.renderRecentTable(data.recent_admissions);
                        }
                    }
                }
            });
        },

        renderRecentTable: function(admissions) {
            let html = '';
            if (admissions.length === 0) {
                html = '<tr><td colspan="5" style="text-align:center;">No recent admissions found.</td></tr>';
            } else {
                admissions.forEach(item => {
                    html += `<tr>
                        <td>${item.student_name}</td>
                        <td>${item.course_name}</td>
                        <td><span class="status-badge ${item.status}">${item.status}</span></td>
                        <td>$${item.balance}</td>
                        <td><button class="aiac-btn-sm">View</button></td>
                    </tr>`;
                });
            }
            $('#aiac-recent-admissions-list').html(html);
        },

        // 4. Leads Page Logic
        initLeadsPage: function() {
            const $leadsRoot = $('#aiac-leads-root');
            if (!$leadsRoot.length) return;
            this.fetchLeads();
        },

        fetchLeads: function() {
            $.ajax({
                url: aiacData.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiac_get_leads',
                    nonce: aiacData.nonce
                },
                success: (response) => {
                    if (response.success) {
                        this.renderLeadsTable(response.data);
                    }
                }
            });
        },

        renderLeadsTable: function(leads) {
            let html = '';
            if (leads.length === 0) {
                html = '<tr><td colspan="7" style="text-align:center;">No leads found.</td></tr>';
            } else {
                leads.forEach(lead => {
                    // Handling both dummy and DB data formats
                    const name = lead.student_name || lead.name;
                    const date = lead.created_at || lead.date;
                    const phone = lead.phone_number || lead.phone;
                    const course = lead.course_id || lead.course || 'AI Mastery';
                    const lang = lead.language_detected || lead.lang || 'Urdu';
                    const status = lead.status || 'New';

                    html += `<tr>
                        <td>${date}</td>
                        <td><strong>${name}</strong></td>
                        <td>${phone}</td>
                        <td>${course}</td>
                        <td>${lang}</td>
                        <td><span class="status-badge status-${status.toLowerCase()}">${status}</span></td>
                        <td>
                            <button class="aiac-btn-sm aiac-btn-primary">Connect</button>
                            <button class="aiac-btn-sm aiac-btn-secondary">Edit</button>
                        </td>
                    </tr>`;
                });
            }
            $('#aiac-leads-list').html(html);
        },

        // 5. Utility Functions
        handleDemoImport: function() {
            if (!confirm('Are you sure you want to import demo data?')) return;
            
            $.post(aiacData.ajax_url, {
                action: 'aiac_import_demo_data',
                nonce: aiacData.nonce
            }, function(res) {
                if (res.success) {
                    location.reload();
                }
            });
        }
    };

    // Run on Document Ready
    $(document).ready(() => AIAC_App.init());

})(jQuery);
// ✅ Syntax verified block end
