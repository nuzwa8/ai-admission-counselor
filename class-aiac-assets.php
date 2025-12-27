<?php
/** Part 3 — Assets Loader (Improved) */
if (!defined('ABSPATH')) exit;

class AIAC_Assets {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook) {
        // صرف ہمارے پلگ ان کے پیجز پر لوڈ کریں
        if (strpos($hook, 'ai-admission-counselor') === false && strpos($hook, 'aiac-') === false) return;

        // CSS لوڈ کریں
        wp_enqueue_style('aiac-common-css', AIAC_URL . 'aiac-common.css', array(), time());

        // JS لوڈ کریں
        wp_enqueue_script('aiac-common-js', AIAC_URL . 'aiac-common.js', array('jquery'), time(), true);

        // ڈیٹا پاس کریں (AJAX کے لیے)
        wp_localize_script('aiac-common-js', 'aiacData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('aiac_secure_nonce')
        ));
    }
}
new AIAC_Assets();
// ✅ Syntax verified block end
