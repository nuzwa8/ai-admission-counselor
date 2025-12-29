<?php
/**
 * Settings Page Template
 * @package AI_Admission_Counselor
 */
if (!defined('ABSPATH')) exit;

function aiac_settings_page() {
    // Save settings if form submitted
    if (isset($_POST['aiac_save_settings']) && check_admin_referer('aiac_settings_nonce', 'aiac_nonce_field')) {
        update_option('aiac_institution_name', sanitize_text_field($_POST['aiac_institution_name']));
        update_option('aiac_currency', sanitize_text_field($_POST['aiac_currency']));
        update_option('aiac_phone', sanitize_text_field($_POST['aiac_phone']));
        update_option('aiac_email', sanitize_email($_POST['aiac_email']));
        update_option('aiac_late_fee_percent', floatval($_POST['aiac_late_fee_percent']));
        update_option('aiac_enable_notifications', isset($_POST['aiac_enable_notifications']) ? 1 : 0);
        echo '<div class="notice notice-success"><p>Settings saved successfully!</p></div>';
    }

    // Get saved values
    $institution = get_option('aiac_institution_name', '');
    $currency = get_option('aiac_currency', 'PKR');
    $phone = get_option('aiac_phone', '');
    $email = get_option('aiac_email', '');
    $late_fee = get_option('aiac_late_fee_percent', 5);
    $notifications = get_option('aiac_enable_notifications', 1);
    ?>
    <div id="aiac-settings-root" class="aiac-wrap">
        <header class="aiac-header">
            <div class="aiac-header-title">
                <h1>Plugin Settings</h1>
                <p>Configure your admission system preferences</p>
            </div>
        </header>

        <form method="post" class="aiac-settings-form">
            <?php wp_nonce_field('aiac_settings_nonce', 'aiac_nonce_field'); ?>
            
            <div class="aiac-card">
                <h2>Institution Details</h2>
                <div class="aiac-form-group">
                    <label for="aiac_institution_name">Institution Name</label>
                    <input type="text" id="aiac_institution_name" name="aiac_institution_name" 
                           value="<?php echo esc_attr($institution); ?>" placeholder="Your Institute Name">
                </div>
                <div class="aiac-form-row">
                    <div class="aiac-form-group">
                        <label for="aiac_phone">Phone Number</label>
                        <input type="text" id="aiac_phone" name="aiac_phone" 
                               value="<?php echo esc_attr($phone); ?>" placeholder="+92 300 1234567">
                    </div>
                    <div class="aiac-form-group">
                        <label for="aiac_email">Email Address</label>
                        <input type="email" id="aiac_email" name="aiac_email" 
                               value="<?php echo esc_attr($email); ?>" placeholder="info@institute.com">
                    </div>
                </div>
            </div>

            <div class="aiac-card">
                <h2>Financial Settings</h2>
                <div class="aiac-form-row">
                    <div class="aiac-form-group">
                        <label for="aiac_currency">Currency</label>
                        <select id="aiac_currency" name="aiac_currency">
                            <option value="PKR" <?php selected($currency, 'PKR'); ?>>PKR (Pakistani Rupee)</option>
                            <option value="USD" <?php selected($currency, 'USD'); ?>>USD (US Dollar)</option>
                            <option value="EUR" <?php selected($currency, 'EUR'); ?>>EUR (Euro)</option>
                            <option value="GBP" <?php selected($currency, 'GBP'); ?>>GBP (British Pound)</option>
                            <option value="SAR" <?php selected($currency, 'SAR'); ?>>SAR (Saudi Riyal)</option>
                            <option value="AED" <?php selected($currency, 'AED'); ?>>AED (UAE Dirham)</option>
                        </select>
                    </div>
                    <div class="aiac-form-group">
                        <label for="aiac_late_fee_percent">Late Fee (%)</label>
                        <input type="number" id="aiac_late_fee_percent" name="aiac_late_fee_percent" 
                               value="<?php echo esc_attr($late_fee); ?>" min="0" max="100" step="0.5">
                    </div>
                </div>
            </div>

            <div class="aiac-card">
                <h2>Notifications</h2>
                <div class="aiac-form-group aiac-checkbox-group">
                    <label>
                        <input type="checkbox" name="aiac_enable_notifications" value="1" 
                               <?php checked($notifications, 1); ?>>
                        Enable Email Notifications for new leads
                    </label>
                </div>
            </div>

            <div class="aiac-form-actions">
                <button type="submit" name="aiac_save_settings" class="aiac-btn aiac-btn-primary">
                    Save Settings
                </button>
                <button type="button" class="aiac-btn aiac-btn-secondary" id="aiac-reset-settings">
                    Reset to Default
                </button>
            </div>
        </form>
    </div>
    <?php
}
