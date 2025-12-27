/** Part 1 — Dashboard Data Fetching & UI Logic */
(function($) {
    'use strict';

    const AIAC_App = {
        init: function() {
            this.fetchDashboardStats();
            this.initEventListeners();
        },

        initEventListeners: function() {
            // Demo Data Import Trigger
            $(document).on('click', '#aiac-import-demo', this.handleDemoImport.bind(this));
        },

        fetchDashboardStats: function() {
            const $root = $('#aiac-dashboard-root');
            if (!$root.length) return;

            $.ajax({
                url: aiacData.ajax_url,
                type: 'POST',
                data: {
                    action: 'aiac_get_dashboard_stats',
                    nonce: aiacData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#stat-total-leads').text(data.total_leads);
                        $('#stat-total-admissions').text(data.total_admissions);
                        $('#stat-total-revenue').text('$' + data.total_revenue);
                        $('#stat-pending-balance').text('$' + data.pending_balance);
                        
                        // Render table if data exists
                        if(data.recent_admissions) {
                            AIAC_App.renderRecentTable(data.recent_admissions);
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

    $(document).ready(() => AIAC_App.init());

})(jQuery);
// ✅ Syntax verified block end
