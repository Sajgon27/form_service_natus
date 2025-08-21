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
define('SERWIS_NATU_FILE', __FILE__);

// Include required files
require_once SERWIS_NATU_PATH . 'includes/class-serwis-natu.php';

// Initialize the plugin
function serwis_natu_init()
{
    $plugin = new Serwis_Natu();
    $plugin->init();
}
add_action('plugins_loaded', 'serwis_natu_init');


register_activation_hook(__FILE__, 'serwis_natu_create_orders_table');
register_activation_hook(__FILE__, 'serwis_natu_activation');

function serwis_natu_activation() {
    // Clear any previous settings
    delete_option('serwis_natu_endpoint_fixed');
    
    // Set the flag to flush rewrite rules
    update_option('serwis_natu_needs_rewrite_flush', true);
    
    // Immediately add the endpoint
    add_rewrite_endpoint('historia-serwisowa', EP_ROOT | EP_PAGES);
}

function serwis_natu_create_orders_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . "serwis_natu_orders"; // wp_serwis_natu_orders
    $charset_collate = $wpdb->get_charset_collate();

    // SQL to create the table
$sql = "CREATE TABLE $table_name (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT(20) UNSIGNED NULL, -- Optional user ID
    client_first_name VARCHAR(100) NOT NULL,
    client_last_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(150) NOT NULL,
    client_phone VARCHAR(50) NOT NULL,
    aquarium_address VARCHAR(255) NOT NULL,
    preferred_date DATETIME NOT NULL,
    additional_notes TEXT NULL,
    aquariums LONGTEXT NULL,         -- JSON {1: {details, extra_services: []}, 2: {...}}
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0,
    cooperative_mode VARCHAR(100) NOT NULL,
    status ENUM('Potwierdzone','Anulowane','Oczekujące') NOT NULL DEFAULT 'Oczekujące',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY user_id (user_id) 
) $charset_collate;";


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// This runs on plugin activation
add_action('init', 'serwis_natu_register_endpoint', 10);
add_action('wp_loaded', 'serwis_natu_flush_rules_if_needed', 20);

/**
 * Register the custom endpoint for My Account page
 */
function serwis_natu_register_endpoint() {
    add_rewrite_endpoint('historia-serwisowa', EP_ROOT | EP_PAGES);
}

/**
 * Flush rewrite rules if needed
 */
function serwis_natu_flush_rules_if_needed() {
    if (get_option('serwis_natu_needs_rewrite_flush', false)) {
        flush_rewrite_rules();
        update_option('serwis_natu_needs_rewrite_flush', false);
        update_option('serwis_natu_endpoint_fixed', true);
    }
}

/**
 * Manual function to fix rewrite rules - can be called directly if needed
 * Simply add this code anywhere in your theme: <?php if(function_exists('serwis_natu_fix_endpoints')) serwis_natu_fix_endpoints(); ?>
 */
function serwis_natu_fix_endpoints() {
    // Add the endpoint manually
    add_rewrite_endpoint('historia-serwisowa', EP_ROOT | EP_PAGES);
    
    // Flush the rules
    flush_rewrite_rules();
    
    // Mark as fixed
    update_option('serwis_natu_endpoint_fixed', true);
    update_option('serwis_natu_needs_rewrite_flush', false);
    
    return true;
}
