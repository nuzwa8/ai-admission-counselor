<?php
/*
Plugin Name: AI Admission Counselor
Description: A smart AI-based admission guide and finance manager.
Version: 1.0.0
Author: Architect Mode
Text Domain: ai-admission-counselor
*/

if (!defined('ABSPATH')) exit;

// Define Constants
define('AIAC_PATH', plugin_dir_path(__FILE__));
define('AIAC_URL', plugin_dir_url(__FILE__));

// Activation Hook
require_once AIAC_PATH . 'class-aiac-activator.php';
register_activation_hook(__FILE__, array('AIAC_Activator', 'activate'));

// Load Core Files
require_once AIAC_PATH . 'class-aiac-assets.php';
require_once AIAC_PATH . 'class-aiac-ajax.php';
require_once AIAC_PATH . 'class-aiac-db.php';

// ✅ Syntax verified block end
