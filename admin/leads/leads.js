/**
 * Leads Manager JavaScript
 * @package AI_Admission_Counselor
 */
(function($) {
    'use strict';

    var AIAC_Leads = {
        init: function() {
            this.bindEvents();
            this.loadLeads();
            console.log('AIAC Leads Manager Initialized');
        },

        bindEvents: function() {
            var self = this;

            // Add Lead Button
            $('#aiac-add-lead-btn').on('click', function() {
                self.openModal();
            });

            // Close Modal
            $('.aiac-modal-close').on('click', function() {
                self.closeModals();
            });

            // Close modal on outside click
            $('.aiac-modal').on('click', function(e) {
                if ($(e.target).hasClass('aiac-modal')) {
                    self.closeModals();
                }
            });

            // Save Lead Form
            $('#aiac-lead-form').on('submit', function(e) {
                e.preventDefault();
                self.saveLead();
            });

            // Edit Lead
            $(document).on('click', '.aiac-edit-lead', function() {
                var id = $(this).data('id');
                self.editLead(id);
            });

            // Delete Lead - show confirmation
            $(document).on('click', '.aiac-delete-lead', function() {
                var id = $(this).data('id');
                $('#aiac-delete-lead-id').val(id);
                $('#aiac-delete-modal').fadeIn(200);
            });

            // Confirm Delete
            $('#aiac-confirm-delete').on('click', function() {
                var id = $('#aiac-delete-lead-id').val();
                self.deleteLead(id);
            });

            // Filter
            $('#aiac-filter-btn').on('click', function() {
                self.loadLeads();
            });

            // Search on Enter
            $('#aiac-lead-search').on('keypress', function(e) {
                if (e.which === 13) self.loadLeads();
            });
        },

        loadLeads: function() {
            var self = this;
            var search = $('#aiac-lead-search').val();
            var status = $('#aiac-lead-status-filter').val();

            $('#aiac-leads-list').html('<tr><td colspan="7" class="aiac-loading">Loading...</td></tr>');

            $.post(aiacData.ajax_url, {
                action: 'aiac_get_leads',
                nonce: aiacData.nonce,
                search: search,
                status: status
            }, function(res) {
                if (res.success) {
                    self.renderLeads(res.data);
                }
            });
        },

        renderLeads: function(leads) {
            var self = this;
            var html = '';
            if (!leads || leads.length === 0) {
                html = '<tr><td colspan="7" class="aiac-empty">No leads found</td></tr>';
            } else {
                // First, load courses to map IDs to names
                $.ajax({
                    url: aiacData.ajax_url,
                    type: 'POST',
                    async: false,
                    data: {
                        action: 'aiac_get_courses',
                        nonce: aiacData.nonce
                    },
                    success: function(res) {
                        self.coursesMap = {};
                        if (res.success && res.data) {
                            res.data.forEach(function(course) {
                                self.coursesMap[course.id] = course.course_name;
                            });
                        }
                    }
                });

                leads.forEach(function(lead) {
                    var statusClass = 'status-' + (lead.status || 'new').toLowerCase();
                    var courseName = self.coursesMap[lead.course_id] || lead.course_id || '-';
                    html += '<tr data-id="' + lead.id + '">' +
                        '<td>' + (lead.created_at || lead.date || '-') + '</td>' +
                        '<td><strong>' + (lead.student_name || '-') + '</strong></td>' +
                        '<td>' + (lead.phone_number || '-') + '</td>' +
                        '<td>' + courseName + '</td>' +
                        '<td>' + (lead.language_detected || '-') + '</td>' +
                        '<td><span class="status-badge ' + statusClass + '">' + (lead.status || 'New') + '</span></td>' +
                        '<td class="aiac-actions">' +
                            '<button class="aiac-btn-sm aiac-btn-primary aiac-edit-lead" data-id="' + lead.id + '">Edit</button> ' +
                            '<button class="aiac-btn-sm aiac-btn-danger aiac-delete-lead" data-id="' + lead.id + '">Delete</button>' +
                        '</td>' +
                    '</tr>';
                });
            }
            $('#aiac-leads-list').html(html);
        },

        openModal: function(lead) {
            $('#aiac-modal-title').text(lead ? 'Edit Lead' : 'Add New Lead');
            $('#aiac-lead-form')[0].reset();
            $('#aiac-lead-id').val('');

            if (lead) {
                $('#aiac-lead-id').val(lead.id);
                $('#aiac-lead-name').val(lead.student_name);
                $('#aiac-lead-phone').val(lead.phone_number);
                $('#aiac-lead-course').val(lead.course_id);
                $('#aiac-lead-lang').val(lead.language_detected);
                $('#aiac-lead-status').val(lead.status);
            }

            $('#aiac-lead-modal').fadeIn(200);
        },

        closeModals: function() {
            $('.aiac-modal').fadeOut(200);
        },

        saveLead: function() {
            var self = this;
            var formData = {
                action: 'aiac_save_lead',
                nonce: aiacData.nonce,
                lead_id: $('#aiac-lead-id').val(),
                student_name: $('#aiac-lead-name').val(),
                phone_number: $('#aiac-lead-phone').val(),
                course_id: $('#aiac-lead-course').val(),
                language_detected: $('#aiac-lead-lang').val(),
                status: $('#aiac-lead-status').val()
            };

            $.post(aiacData.ajax_url, formData, function(res) {
                if (res.success) {
                    self.closeModals();
                    self.loadLeads();
                } else {
                    alert(res.data || 'Error saving lead');
                }
            });
        },

        editLead: function(id) {
            var self = this;
            $.post(aiacData.ajax_url, {
                action: 'aiac_get_lead',
                nonce: aiacData.nonce,
                lead_id: id
            }, function(res) {
                if (res.success) {
                    self.openModal(res.data);
                }
            });
        },

        deleteLead: function(id) {
            var self = this;
            $.post(aiacData.ajax_url, {
                action: 'aiac_delete_lead',
                nonce: aiacData.nonce,
                lead_id: id
            }, function(res) {
                if (res.success) {
                    self.closeModals();
                    self.loadLeads();
                }
            });
        }
    };

    $(document).ready(function() {
        if ($('#aiac-leads-root').length) {
            AIAC_Leads.init();
        }
    });

})(jQuery);
