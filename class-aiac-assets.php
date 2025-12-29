<?php
/** 
 * Assets Loader - Loads Common + Page-Specific CSS/JS
 * @package AI_Admission_Counselor
 */
if (!defined('ABSPATH')) exit;

class AIAC_Assets {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_admin_assets($hook) {
        // صرف ہمارے پلگ ان کے پیجز پر لوڈ کریں
        if (strpos($hook, 'ai-admission-counselor') === false && strpos($hook, 'aiac-') === false) return;

        // 1. Common CSS/JS (ہر پیج پر لوڈ)
        wp_enqueue_style('aiac-common-css', AIAC_URL . 'aiac-common.css', array(), AIAC_VERSION);
        wp_enqueue_script('aiac-common-js', AIAC_URL . 'aiac-common.js', array('jquery'), AIAC_VERSION, true);

        // ڈیٹا پاس کریں (AJAX کے لیے)
        wp_localize_script('aiac-common-js', 'aiacData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('aiac_secure_nonce'),
            'currency' => get_option('aiac_currency', 'PKR')
        ));

        // 2. Page-Specific Assets
        $this->load_page_assets($hook);
    }

    private function load_page_assets($hook) {
        $page_map = array(
            'aiac-settings'    => 'settings',
            'aiac-courses'     => 'courses',
            'aiac-leads'       => 'leads',
            'aiac-admissions'  => 'admissions',
            'aiac-payments'    => 'payments',
            'ai-admission-counselor' => 'dashboard'
        );

        foreach ($page_map as $page_slug => $folder) {
            if (strpos($hook, $page_slug) !== false) {
                $base_path = AIAC_URL . 'admin/' . $folder . '/' . $folder;
                $file_path = AIAC_PATH . 'admin/' . $folder . '/';
                
                // CSS - ہمیشہ لوڈ کریں
                wp_enqueue_style('aiac-' . $folder . '-css', $base_path . '.css', array('aiac-common-css'), time());
                
                // JS - ہمیشہ لوڈ کریں
                wp_enqueue_script('aiac-' . $folder . '-js', $base_path . '.js', array('jquery', 'aiac-common-js'), time(), true);
                
                // اس JS کو بھی aiacData دیں
                wp_localize_script('aiac-' . $folder . '-js', 'aiacData', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce'    => wp_create_nonce('aiac_secure_nonce'),
                    'currency' => get_option('aiac_currency', 'PKR')
                ));
                break;
            }
        }
    }
}
new AIAC_Assets();
