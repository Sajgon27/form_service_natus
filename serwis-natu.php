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
define('SERWIS_NATU_VERSION', '1.0.1');
define('SERWIS_NATU_FILE', __FILE__);

// Include required files
require_once SERWIS_NATU_PATH . 'includes/class-serwis-natu.php';
require_once SERWIS_NATU_PATH . 'includes/helpers/polish-date-helper.php';

// Initialize the plugin
function serwis_natu_init()
{
    $plugin = new Serwis_Natu();
    $plugin->init();
}
add_action('plugins_loaded', 'serwis_natu_init');


register_activation_hook(__FILE__, 'serwis_natu_create_orders_table');
register_activation_hook(__FILE__, 'serwis_natu_activation');
register_activation_hook(__FILE__, 'serwis_natu_initialize_package_settings');

function serwis_natu_activation()
{
    // Clear any previous settings
    delete_option('serwis_natu_endpoint_fixed');

    // Set the flag to flush rewrite rules
    update_option('serwis_natu_needs_rewrite_flush', true);

    // Immediately add the endpoint
    add_rewrite_endpoint('historia-serwisowa', EP_ROOT | EP_PAGES);
}

/**
 * Initialize package settings on plugin activation
 */
function serwis_natu_initialize_package_settings() {
    // Initialize one-time packages if they don't exist
    if (false === get_option('serwis_natu_one_time_packages')) {
        $empty_package = array(
            'package_1' => array(
                'name' => '',
                'price' => 0
            )
        );
        update_option('serwis_natu_one_time_packages', $empty_package);
    }
    
    // Initialize subscription packages if they don't exist
    if (false === get_option('serwis_natu_subscription_packages')) {
        $empty_package = array(
            'package_1' => array(
                'name' => '',
                'price' => 0
            )
        );
        update_option('serwis_natu_subscription_packages', $empty_package);
    }
    
    // Initialize additional services if they don't exist
    if (false === get_option('serwis_natu_additional_services')) {
        $empty_service = array(
            'service_1' => array(
                'name' => '',
                'price' => 0
            )
        );
        update_option('serwis_natu_additional_services', $empty_service);
    }
}

// Table to store service orders
function serwis_natu_create_orders_table()
{
    global $wpdb;

    $table_name = $wpdb->prefix . "serwis_natu_orders"; 
    $charset_collate = $wpdb->get_charset_collate();

    // SQL to create the table
    $sql = "CREATE TABLE $table_name (
    id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT(20) UNSIGNED NULL,
    client_first_name VARCHAR(100) NOT NULL,
    client_last_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(150) NOT NULL,
    client_phone VARCHAR(50) NOT NULL,
    aquarium_address VARCHAR(255) NOT NULL,
    preferred_date DATETIME NOT NULL,
    additional_notes TEXT NULL,
    aquariums LONGTEXT NULL,  -- JSON {1: {details, extra_services: []}, 2: {...}}
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

// Runs on plugin activation
add_action('init', 'serwis_natu_register_endpoint', 10);
add_action('wp_loaded', 'serwis_natu_flush_rules_if_needed', 20);

/**
 * Register the custom endpoint for My Account page
 */
function serwis_natu_register_endpoint()
{
    add_rewrite_endpoint('historia-serwisowa', EP_ROOT | EP_PAGES);
}

/**
 * Flush rewrite rules if needed
 */
function serwis_natu_flush_rules_if_needed()
{
    if (get_option('serwis_natu_needs_rewrite_flush', false)) {
        flush_rewrite_rules();
        update_option('serwis_natu_needs_rewrite_flush', false);
        update_option('serwis_natu_endpoint_fixed', true);
    }
}

/**
 * Register AJAX handlers
 */
add_action('wp_ajax_send_custom_email_to_client', 'serwis_natu_send_custom_email_to_client');

/**
 * AJAX handler for sending custom emails to clients
 */
function serwis_natu_send_custom_email_to_client()
{
    // Verify security nonce
    check_ajax_referer('send_email_to_client_nonce', 'security');

    // Check permissions
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Nie masz uprawnień do wykonania tej operacji.');
        return;
    }

    // Get data from request
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $message = isset($_POST['message']) ? wp_kses_post($_POST['message']) : '';

    if ($order_id <= 0) {
        wp_send_json_error('Nieprawidłowy identyfikator zamówienia.');
        return;
    }

    // Get order data from database
    global $wpdb;
    $table_name = $wpdb->prefix . 'serwis_natu_orders';
    $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $order_id), ARRAY_A);

    if (!$order) {
        wp_send_json_error('Zamówienie nie zostało znalezione.');
        return;
    }

    // Parse aquariums data
    $aquariums = $order['aquariums'];
    if (is_string($aquariums)) {
        $aquariums = json_decode($aquariums, true);
    }

    // Prepare data for the email template
    $email_data = array(
        'client_first_name' => $order['client_first_name'],
        'client_last_name' => $order['client_last_name'],
        'client_email' => $order['client_email'],
        'client_phone' => $order['client_phone'],
        'aquarium_address' => $order['aquarium_address'],
        'preferred_date' => $order['preferred_date'],
        'tryb_wspolpracy' => $order['cooperative_mode'],
        'cena' => $order['total_price'],
        'created_at' => $order['created_at']
    );

    // Start output buffering to capture the email template output
    ob_start();

    // Make variables available to the template
    $dane = $email_data;
    $zamowienie_id = $order_id;
    $custom_message = $message;

    // Include the custom email template
    include_once(SERWIS_NATU_PATH . 'templates/emails/email-admin-to-client.php');

    // Get the email content from the buffer
    $email_content = ob_get_clean();

    // Email headers
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: Natuscape <sklep@natuscape.pl>'
    );

    // Send the email
    $subject = sprintf(__('Wiadomość dt. usługi serwisowej #%d', 'serwis-natu'), $order_id);
    $sent = wp_mail($order['client_email'], $subject, $email_content, $headers);

    if ($sent) {
        wp_send_json_success(array(
            'message' => 'Wiadomość została pomyślnie wysłana.'
        ));
    } else {
        wp_send_json_error('Nie udało się wysłać wiadomości. Proszę spróbować ponownie.');
    }
}
