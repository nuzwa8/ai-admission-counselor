/**
 * Admissions Manager JavaScript
 * @package AI_Admission_Counselor
 */
(function($) {
    'use strict';

    var AIAC_Admissions = {
        init: function() {
            this.bindEvents();
            this.loadAdmissions();
            this.loadStats();
            console.log('AIAC Admissions Manager Initialized');
        },

        bindEvents: function() {
            var self = this;

            // Add Admission
            $('#aiac-add-admission-btn').on('click', function() {
                self.openModal();
            });

            // Close Modals
            $('.aiac-modal-close').on('click', function() {
                self.closeModals();
            });

            $('.aiac-modal').on('click', function(e) {
                if ($(e.target).hasClass('aiac-modal')) {
                    self.closeModals();
                }
            });

            // Save Admission
            $('#aiac-admission-form').on('submit', function(e) {
                e.preventDefault();
                self.saveAdmission();
            });

            // Edit
            $(document).on('click', '.aiac-edit-admission', function() {
                var id = $(this).data('id');
                self.editAdmission(id);
            });

            // Delete
            $(document).on('click', '.aiac-delete-admission', function() {
                var id = $(this).data('id');
                $('#aiac-delete-admission-id').val(id);
                $('#aiac-delete-admission-modal').fadeIn(200);
            });

            // Confirm Delete
            $('#aiac-confirm-delete-admission').on('click', function() {
                var id = $('#aiac-delete-admission-id').val();
                self.deleteAdmission(id);
            });

            // Filter
            $('#aiac-admission-filter-btn').on('click', function() {
                self.loadAdmissions();
            });

            $('#aiac-admission-search').on('keypress', function(e) {
                if (e.which === 13) self.loadAdmissions();
            });

            // Auto-update status based on payment
            $('#aiac-admission-paid, #aiac-admission-fee').on('change', function() {
                self.autoUpdateStatus();
            });
        },

        loadStats: function() {
            $.post(aiacData.ajax_url, {
                action: 'aiac_get_admission_stats',
                nonce: aiacData.nonce
            }, function(res) {
                if (res.success) {
                    var d = res.data;
                    var currency = aiacData.currency || 'PKR';
                    $('#stat-admissions-count').text(d.count);
                    $('#stat-total-fee').text(currency + ' ' + d.total_fee);
                    $('#stat-collected').text(currency + ' ' + d.collected);
                    $('#stat-pending').text(currency + ' ' + d.pending);
                }
            });
        },

        loadAdmissions: function() {
            var self = this;
            var search = $('#aiac-admission-search').val();
            var status = $('#aiac-admission-status-filter').val();

            $('#aiac-admissions-list').html('<tr><td colspan="10" class="aiac-loading">Loading...</td></tr>');

            $.post(aiacData.ajax_url, {
                action: 'aiac_get_admissions',
                nonce: aiacData.nonce,
                search: search,
                status: status
            }, function(res) {
                if (res.success) {
                    self.renderAdmissions(res.data);
                }
            });
        },

        renderAdmissions: function(admissions) {
            var self = this;
            var html = '';
            var currency = aiacData.currency || 'PKR';
            
            if (!admissions || admissions.length === 0) {
                html = '<tr><td colspan="10" class="aiac-empty">No admissions found</td></tr>';
            } else {
                admissions.forEach(function(adm) {
                    var balance = parseFloat(adm.total_fee) - parseFloat(adm.paid_amount);
                    var statusClass = 'status-' + (adm.admission_status || 'pending');
                    var courseName = adm.course_name || '-';
                    
                    html += '<tr data-id="' + adm.id + '">' +
                        '<td>#' + adm.id + '</td>' +
                        '<td><strong>' + (adm.student_name || 'N/A') + '</strong></td>' +
                        '<td>' + (adm.phone_number || '-') + '</td>' +
                        '<td>' + courseName + '</td>' +
                        '<td>' + currency + ' ' + parseFloat(adm.total_fee).toLocaleString() + '</td>' +
                        '<td>' + currency + ' ' + parseFloat(adm.paid_amount).toLocaleString() + '</td>' +
                        '<td class="' + (balance > 0 ? 'aiac-danger' : 'aiac-success') + '">' + currency + ' ' + balance.toLocaleString() + '</td>' +
                        '<td>' + (adm.due_date || '-') + '</td>' +
                        '<td><span class="status-badge ' + statusClass + '">' + (adm.admission_status || 'Pending') + '</span></td>' +
                        '<td class="aiac-actions">' +
                            '<button class="aiac-btn-sm aiac-btn-primary aiac-edit-admission" data-id="' + adm.id + '">Edit</button> ' +
                            '<button class="aiac-btn-sm aiac-btn-danger aiac-delete-admission" data-id="' + adm.id + '">Del</button>' +
                        '</td>' +
                    '</tr>';
                });
            }
            $('#aiac-admissions-list').html(html);
        },

        openModal: function(admission) {
            var self = this;
            $('#aiac-admission-modal-title').text(admission ? 'Edit Admission' : 'New Admission');
            $('#aiac-admission-form')[0].reset();
            $('#aiac-admission-id').val('');

            // Load leads dropdown
            $.post(aiacData.ajax_url, {
                action: 'aiac_get_leads',
                nonce: aiacData.nonce
            }, function(res) {
                if (res.success) {
                    var opts = '<option value="">-- Select Lead --</option>';
                    res.data.forEach(function(lead) {
                        var selected = admission && admission.lead_id == lead.id ? 'selected' : '';
                        opts += '<option value="' + lead.id + '" ' + selected + '>' + (lead.student_name || lead.name) + ' (' + (lead.phone_number || lead.phone) + ')</option>';
                    });
                    $('#aiac-admission-lead').html(opts);
                }
            });

            // Load courses dropdown
            $.post(aiacData.ajax_url, {
                action: 'aiac_get_courses',
                nonce: aiacData.nonce
            }, function(res) {
                if (res.success) {
                    var opts = '<option value="">-- Select Course --</option>';
                    var currency = aiacData.currency || 'PKR';
                    res.data.forEach(function(course) {
                        var selected = admission && admission.course_id == course.id ? 'selected' : '';
                        opts += '<option value="' + course.id + '" ' + selected + '>' + course.course_name + ' - ' + currency + ' ' + parseFloat(course.fee).toLocaleString() + '</option>';
                    });
                    $('#aiac-admission-course').html(opts);
                }
            });

            if (admission) {
                $('#aiac-admission-id').val(admission.id);
                $('#aiac-admission-course').val(admission.course_id);
                $('#aiac-admission-fee').val(admission.total_fee);
                $('#aiac-admission-paid').val(admission.paid_amount);
                $('#aiac-admission-due').val(admission.due_date);
                $('#aiac-admission-status').val(admission.admission_status);
            }

            $('#aiac-admission-modal').fadeIn(200);
        },

        closeModals: function() {
            $('.aiac-modal').fadeOut(200);
        },

        autoUpdateStatus: function() {
            var fee = parseFloat($('#aiac-admission-fee').val()) || 0;
            var paid = parseFloat($('#aiac-admission-paid').val()) || 0;
            
            if (paid >= fee && fee > 0) {
                $('#aiac-admission-status').val('paid');
            } else if (paid > 0) {
                $('#aiac-admission-status').val('partial');
            } else {
                $('#aiac-admission-status').val('pending');
            }
        },

        saveAdmission: function() {
            var self = this;
            var formData = {
                action: 'aiac_save_admission',
                nonce: aiacData.nonce,
                admission_id: $('#aiac-admission-id').val(),
                lead_id: $('#aiac-admission-lead').val(),
                course_id: $('#aiac-admission-course').val(),
                total_fee: $('#aiac-admission-fee').val(),
                paid_amount: $('#aiac-admission-paid').val(),
                due_date: $('#aiac-admission-due').val(),
                admission_status: $('#aiac-admission-status').val()
            };

            $.post(aiacData.ajax_url, formData, function(res) {
                if (res.success) {
                    self.closeModals();
                    self.loadAdmissions();
                    self.loadStats();
                } else {
                    alert(res.data || 'Error saving admission');
                }
            });
        },

        editAdmission: function(id) {
            var self = this;
            $.post(aiacData.ajax_url, {
                action: 'aiac_get_admission',
                nonce: aiacData.nonce,
                admission_id: id
            }, function(res) {
                if (res.success) {
                    self.openModal(res.data);
                }
            });
        },

        deleteAdmission: function(id) {
            var self = this;
            $.post(aiacData.ajax_url, {
                action: 'aiac_delete_admission',
                nonce: aiacData.nonce,
                admission_id: id
            }, function(res) {
                if (res.success) {
                    self.closeModals();
                    self.loadAdmissions();
                    self.loadStats();
                }
            });
        }
    };

    $(document).ready(function() {
        if ($('#aiac-admissions-root').length) {
            AIAC_Admissions.init();
        }
    });

})(jQuery);
