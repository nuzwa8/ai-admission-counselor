/** * AI Admission Counselor - Common JS 
 * Version: 1.2 (Form submit for New Admission)
 */
(function($) {
    'use strict';

    // مین ایپلیکیشن آبجیکٹ
    var AIAC_App = {
        init: function() {
            console.log('AIAC App Initialized');
            this.loadDashboard();
            this.loadLeads();
            this.setupEvents();
        },

        setupEvents: function() {
            var self = this;
            $(document).on('click', '#aiac-import-demo', function() {
                if (confirm('Import demo data?')) {
                    self.ajaxCall('aiac_import_demo_data', {}, function() {
                        location.reload();
                    });
                }
            });

            // New admission form submit (uses FormData for file upload)
            $(document).on('submit', '#aiac-new-admission-form', function(e) {
                e.preventDefault();
                var form = this;
                var fd = new FormData(form);
                // Add action and nonce if not included
                fd.set('action', 'aiac_add_admission');
                if (!fd.get('nonce') && window.aiacData && aiacData.nonce) {
                    fd.set('nonce', aiacData.nonce);
                }

                $.ajax({
                    url: aiacData.ajax_url,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        if (res && res.success) {
                            alert(res.data && res.data.message ? res.data.message : 'Admission created');
                            // reload to reflect new data
                            location.reload();
                        } else {
                            var msg = (res && res.data && res.data.message) ? res.data.message : 'Error creating admission';
                            alert(msg);
                        }
                    },
                    error: function(xhr, status, err) {
                        console.error('AIAC form submit error', status, err);
                        alert('Request failed: ' + status);
                    }
                });
            });
        },

        // ڈیش بورڈ لوڈ کرنے کا لاجک
        loadDashboard: function() {
            if (!$('#aiac-dashboard-root').length) return;
            
            this.ajaxCall('aiac_get_dashboard_stats', {}, function(data) {
                $('#stat-total-leads').text(data.total_leads);
                $('#stat-total-admissions').text(data.total_admissions);
                $('#stat-total-revenue').text('$' + data.total_revenue);
                $('#stat-pending-balance').text('$' + data.pending_balance);
                
                var html = '';
                if (data.recent_admissions) {
                    data.recent_admissions.forEach(function(item) {
                        html += '<tr><td>'+item.student_name+'</td><td>'+item.course_name+'</td><td>'+item.status+'</td><td>$'+item.balance+'</td><td><button class="aiac-btn-sm">View</button></td></tr>';
                    });
                    $('#aiac-recent-admissions-list').html(html);
                }
            });
        },

        // لیڈز لوڈ کرنے کا لاجک
        loadLeads: function() {
            if (!$('#aiac-leads-root').length) return;

            this.ajaxCall('aiac_get_leads', {}, function(leads) {
                var html = '';
                if (!leads || leads.length === 0) {
                    html = '<tr><td colspan="7" style="text-align:center;">No leads found.</td></tr>';
                } else {
                    leads.forEach(function(lead) {
                        var name = lead.student_name || lead.name || 'N/A';
                        var date = lead.created_at || lead.date || '-';
                        var phone = lead.phone_number || lead.phone || '-';
                        var course = lead.course_id || lead.course || 'AI Mastery';
                        var lang = lead.language_detected || lead.lang || 'Urdu';
                        var status = lead.status || 'New';

                        html += '<tr>' +
                            '<td>' + date + '</td>' +
                            '<td><strong>' + name + '</strong></td>' +
                            '<td>' + phone + '</td>' +
                            '<td>' + course + '</td>' +
                            '<td>' + lang + '</td>' +
                            '<td><span class="status-badge status-new">' + status + '</span></td>' +
                            '<td><button class="aiac-btn-sm aiac-btn-primary">Connect</button></td>' +
                        '</tr>';
                    });
                }
                $('#aiac-leads-list').html(html);
            });
        },

        // کامن ایجیکس فنکشن (تاکہ کوڈ کم ہو)
        ajaxCall: function(action, extraData, callback) {
            var data = {
                action: action,
                nonce: aiacData.nonce
            };
            $.extend(data, extraData);

            $.post(aiacData.ajax_url, data, function(response) {
                if (response.success && callback) {
                    callback(response.data);
                } else if (!response.success) {
                    console.error('AIAC Error:', response);
                }
            });
        }
    };

    // ڈاکومنٹ ریڈی پر رن کریں
    $(document).ready(function() {
        AIAC_App.init();
    });

})(jQuery);
// ✅ Syntax verified block end