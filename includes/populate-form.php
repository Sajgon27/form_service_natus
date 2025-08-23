<?php

/**
 * Form Populate class for Serwis Natu
 *
 * @package Serwis_Natu
 */

if (!defined('ABSPATH')) {
    exit; // Block direct access to the file
}

/**
 * Class for handling form pre-population from existing orders
 */
class Serwis_Natu_Form_Populate
{

    /**
     * Constructor
     */
    public function __construct()
    {
        // Add JavaScript to handle form pre-population
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add AJAX handler for retrieving order data
        add_action('wp_ajax_get_order_data_for_form', array($this, 'get_order_data_for_form'));
        add_action('wp_ajax_nopriv_get_order_data_for_form', array($this, 'get_order_data_for_form'));
    }

    /**
     * Enqueue required scripts
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script(
            'serwis-natu-form-populate',
            SERWIS_NATU_URL . 'assets/js/populate-form.js',
            array('jquery'),
            SERWIS_NATU_VERSION,
            true
        );

        // Pass data to JavaScript
        wp_localize_script(
            'serwis-natu-form-populate',
            'serwisNatuFormPopulate',
            array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('serwis_natu_form_populate_nonce'),
            )
        );
    }

    /**
     * AJAX handler for retrieving order data
     */
    public function get_order_data_for_form()
    {
        // Check nonce for security
        check_ajax_referer('serwis_natu_form_populate_nonce', 'nonce');

        // Get order ID from request
        $order_id = isset($_REQUEST['order_id']) ? intval($_REQUEST['order_id']) : 0;

        if ($order_id <= 0) {
            wp_send_json_error(array('message' => 'Invalid order ID'));
            return;
        }

        // Get order data from database
        global $wpdb;
        $table_name = $wpdb->prefix . 'serwis_natu_orders';

        $order = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $order_id),
            ARRAY_A
        );

        if (!$order) {
            wp_send_json_error(array('message' => 'Order not found'));
            return;
        }

        // Parse aquariums JSON
        $aquariums = json_decode($order['aquariums'], true);

        // Prepare response data with only the fields we want to populate
        $response_data = array(
            'client_info' => array(
                'client_first_name' => $order['client_first_name'],
                'client_last_name' => $order['client_last_name'],
                'client_email' => $order['client_email'],
                'client_phone' => $order['client_phone'],
                'aquarium_address' => $order['aquarium_address'],
            ),
            'form_settings' => array(
                'cooperative_mode' => $order['cooperative_mode'],
                'aquarium_count' => is_array($aquariums) ? count($aquariums) : 1,
            ),
            'aquariums' => $aquariums,
        );

        wp_send_json_success($response_data);
    }
}
