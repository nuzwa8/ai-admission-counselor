<?php
/** Part 3 — Assets Loader */
if (!defined('ABSPATH')) exit;

class AIAC_Assets {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook) {
        // Only load on our plugin pages
        if (strpos($hook, 'ai-admission-counselor') === false) return;

        wp_enqueue_style('aiac-common-css', AIAC_URL . 'aiac-common.css', array(), '1.0.0');
        wp_enqueue_script('aiac-common-js', AIAC_URL . 'aiac-common.js', array('jquery'), '1.0.0', true);

        // Localize data for AJAX
        wp_localize_script('aiac-common-js', 'aiacData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('aiac_secure_nonce')
        ));
    }
}
new AIAC_Assets();
// ✅ Syntax verified block end
