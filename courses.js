/**
 * Courses Manager JavaScript
 * @package AI_Admission_Counselor
 */
(function($) {
    'use strict';

    var AIAC_Courses = {
        init: function() {
            this.bindEvents();
            this.loadCourses();
            console.log('AIAC Courses Manager Initialized');
        },

        bindEvents: function() {
            var self = this;

            // Add Course
            $('#aiac-add-course-btn').on('click', function() {
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

            // Save Course
            $('#aiac-course-form').on('submit', function(e) {
                e.preventDefault();
                self.saveCourse();
            });

            // Edit
            $(document).on('click', '.aiac-edit-course', function() {
                var id = $(this).data('id');
                self.editCourse(id);
            });

            // Delete
            $(document).on('click', '.aiac-delete-course', function() {
                var id = $(this).data('id');
                $('#aiac-delete-course-id').val(id);
                $('#aiac-delete-course-modal').fadeIn(200);
            });

            // Confirm Delete
            $('#aiac-confirm-delete-course').on('click', function() {
                var id = $('#aiac-delete-course-id').val();
                self.deleteCourse(id);
            });
        },

        loadCourses: function() {
            var self = this;
            $('#aiac-courses-list').html('<tr><td colspan="7" class="aiac-loading">Loading...</td></tr>');

            $.post(aiacData.ajax_url, {
                action: 'aiac_get_courses',
                nonce: aiacData.nonce
            }, function(res) {
                if (res.success) {
                    self.renderCourses(res.data);
                    self.updateStats(res.data);
                }
            });
        },

        updateStats: function(courses) {
            var total = courses ? courses.length : 0;
            var active = 0;
            if (courses) {
                courses.forEach(function(course) {
                    if (course.is_active == 1) active++;
                });
            }
            $('#stat-courses-count').text(total);
            $('#stat-active-courses').text(active);
        },

        renderCourses: function(courses) {
            var html = '';
            var currency = aiacData.currency || 'PKR';
            
            if (!courses || courses.length === 0) {
                html = '<tr><td colspan="7" class="aiac-empty">No courses found</td></tr>';
            } else {
                courses.forEach(function(course) {
                    var statusClass = course.is_active == 1 ? 'status-new' : 'status-lost';
                    var statusText = course.is_active == 1 ? 'Active' : 'Inactive';
                    
                    html += '<tr data-id="' + course.id + '">' +
                        '<td>#' + course.id + '</td>' +
                        '<td><strong>' + (course.course_name || '-') + '</strong></td>' +
                        '<td>' + (course.course_code || '-') + '</td>' +
                        '<td>' + (course.duration || '-') + '</td>' +
                        '<td>' + currency + ' ' + parseFloat(course.fee || 0).toLocaleString() + '</td>' +
                        '<td><span class="status-badge ' + statusClass + '">' + statusText + '</span></td>' +
                        '<td class="aiac-actions">' +
                            '<button class="aiac-btn-sm aiac-btn-primary aiac-edit-course" data-id="' + course.id + '">Edit</button> ' +
                            '<button class="aiac-btn-sm aiac-btn-danger aiac-delete-course" data-id="' + course.id + '">Deactivate</button>' +
                        '</td>' +
                    '</tr>';
                });
            }
            $('#aiac-courses-list').html(html);
        },

        openModal: function(course) {
            $('#aiac-course-modal-title').text(course ? 'Edit Course' : 'Add New Course');
            $('#aiac-course-form')[0].reset();
            $('#aiac-course-id').val('');

            if (course) {
                $('#aiac-course-id').val(course.id);
                $('#aiac-course-name').val(course.course_name);
                $('#aiac-course-code').val(course.course_code);
                $('#aiac-course-duration').val(course.duration);
                $('#aiac-course-fee').val(course.fee);
                $('#aiac-course-description').val(course.description);
            }

            $('#aiac-course-modal').fadeIn(200);
        },

        closeModals: function() {
            $('.aiac-modal').fadeOut(200);
        },

        saveCourse: function() {
            var self = this;
            var formData = {
                action: 'aiac_save_course',
                nonce: aiacData.nonce,
                course_id: $('#aiac-course-id').val(),
                course_name: $('#aiac-course-name').val(),
                course_code: $('#aiac-course-code').val(),
                duration: $('#aiac-course-duration').val(),
                fee: $('#aiac-course-fee').val(),
                description: $('#aiac-course-description').val()
            };

            $.post(aiacData.ajax_url, formData, function(res) {
                if (res.success) {
                    self.closeModals();
                    self.loadCourses();
                } else {
                    alert(res.data || 'Error saving course');
                }
            });
        },

        editCourse: function(id) {
            var self = this;
            var courses = $('#aiac-courses-list tr');
            courses.each(function() {
                if ($(this).data('id') == id) {
                    var course = {
                        id: id,
                        course_name: $(this).find('td:eq(1)').text(),
                        course_code: $(this).find('td:eq(2)').text(),
                        duration: $(this).find('td:eq(3)').text(),
                        fee: $(this).find('td:eq(4)').text().replace(/[^0-9.]/g, ''),
                        description: ''
                    };
                    self.openModal(course);
                    return false;
                }
            });
        },

        deleteCourse: function(id) {
            var self = this;
            $.post(aiacData.ajax_url, {
                action: 'aiac_delete_course',
                nonce: aiacData.nonce,
                course_id: id
            }, function(res) {
                if (res.success) {
                    self.closeModals();
                    self.loadCourses();
                }
            });
        }
    };

    $(document).ready(function() {
        if ($('#aiac-courses-root').length) {
            AIAC_Courses.init();
        }
    });

})(jQuery);
