<?php
/**
 * Plugin Name: Serwis Natu
 * Plugin URI: https://natuscape.pl
 * Description: Zaawansowany wieloetapowy formularz do usługi serwisu akwarystycznego z obsługą WPML
 * Version: 1.0.0
 * Author: Natuscape
 * Text Domain: serwis-natu
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Blokowanie bezpośredniego dostępu do pliku
}

// Define plugin constants
define('SERWIS_NATU_PATH', plugin_dir_path(__FILE__));
define('SERWIS_NATU_URL', plugin_dir_url(__FILE__));
define('SERWIS_NATU_VERSION', '1.0.0');

// Include required files
require_once SERWIS_NATU_PATH . 'includes/class-serwis-natu.php';

// Initialize the plugin
function serwis_natu_init() {
    $plugin = new Serwis_Natu();
    $plugin->init();
}
add_action('plugins_loaded', 'serwis_natu_init');
