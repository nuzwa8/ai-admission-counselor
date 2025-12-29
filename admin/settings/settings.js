/**
 * Settings Page JavaScript
 * @package AI_Admission_Counselor
 */
(function($) {
    'use strict';

    var AIAC_Settings = {
        init: function() {
            this.bindEvents();
            console.log('AIAC Settings Initialized');
        },

        bindEvents: function() {
            var self = this;

            // Reset button
            $('#aiac-reset-settings').on('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to reset all settings to default?')) {
                    self.resetToDefault();
                }
            });

            // Form validation
            $('.aiac-settings-form').on('submit', function(e) {
                if (!self.validateForm()) {
                    e.preventDefault();
                    alert('Please fill in all required fields correctly.');
                }
            });
        },

        validateForm: function() {
            var isValid = true;
            var email = $('#aiac_email').val();
            
            // Email validation
            if (email && !this.isValidEmail(email)) {
                $('#aiac_email').addClass('aiac-error');
                isValid = false;
            } else {
                $('#aiac_email').removeClass('aiac-error');
            }

            return isValid;
        },

        isValidEmail: function(email) {
            var regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        resetToDefault: function() {
            $('#aiac_institution_name').val('');
            $('#aiac_currency').val('PKR');
            $('#aiac_phone').val('');
            $('#aiac_email').val('');
            $('#aiac_late_fee_percent').val('5');
            $('input[name="aiac_enable_notifications"]').prop('checked', true);
        }
    };

    $(document).ready(function() {
        if ($('#aiac-settings-root').length) {
            AIAC_Settings.init();
        }
    });

})(jQuery);
