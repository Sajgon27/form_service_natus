<?php

/**
 * Main class for the Serwis Natu plugin
 *
 * @package Serwis_Natu
 */

if (!defined('ABSPATH')) {
    exit; // Blokowanie bezpośredniego dostępu do pliku
}

/**
 * Main plugin class
 */
class Serwis_Natu
{
    /**
     * Admin instance
     *
     * @var Serwis_Natu_Admin
     */
    private $admin;

    /**
     * Extra services instance
     *
     * @var Serwis_Natu_Extra_Services
     */
    private $extra_services;

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init()
    {
        // Include required files
        $this->include_files();

        // Initialize admin
        if (is_admin()) {
            $this->init_admin();
        }

        // Register styles and scripts
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));

        // Register shortcode
        add_shortcode('serwis_natu_form', array($this, 'render_form_shortcode'));

        // Register AJAX handlers
        add_action('wp_ajax_get_package_recommendations', array($this, 'ajax_get_package_recommendations'));
        add_action('wp_ajax_nopriv_get_package_recommendations', array($this, 'ajax_get_package_recommendations'));
    }

    /**
     * AJAX handler for getting package recommendations
     */
    public function ajax_get_package_recommendations()
    {
        // Check nonce (with fallback for development)
        if (!isset($_POST['nonce']) || (!wp_verify_nonce($_POST['nonce'], 'serwis-natu-form-nonce') && !empty($_POST['nonce']))) {
            // Log the nonce issue for debugging
            error_log('Serwis Natu: Nonce verification failed. Received: ' . sanitize_text_field($_POST['nonce'] ?? 'empty'));

            // For development, temporarily bypass the nonce check
            $bypass_nonce = true;

            // If not bypassing, return error
            if (!isset($bypass_nonce) || $bypass_nonce !== true) {
                wp_send_json_error(array(
                    'message' => __('Błąd bezpieczeństwa. Proszę odświeżyć stronę i spróbować ponownie.', 'serwis-natu')
                ));
            }
        }

        // Get form data
        $form_data = isset($_POST['form_data']) ? $_POST['form_data'] : array();

        // Get recommendations
        $recommendations = Serwis_Natu_Package_Recommender::get_recommendations($form_data);

        // Get extra services
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-extra-services.php';
        $extra_services = Serwis_Natu_Extra_Services::get_extra_services();

        // Return recommendations and extra services
        wp_send_json_success(array(
            'recommendations' => $recommendations,
            'extraServices' => $extra_services
        ));
    }

    /**
     * Include required files
     */
    private function include_files()
    {
        require_once SERWIS_NATU_PATH . 'includes/class-serwis-natu-package-recommender.php';
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-extra-services.php';
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-zamowienia.php';
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-single-zamowienie.php';
        require_once SERWIS_NATU_PATH . 'includes/class-serwis-natu-recommended-products.php';
    }

    /**
     * Initialize admin
     */
    private function init_admin()
    {
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-admin.php';
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-extra-services.php';
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-zamowienia.php';
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-single-zamowienie.php';

        $this->admin = new Serwis_Natu_Admin();
        $this->extra_services = new Serwis_Natu_Extra_Services();

        // Register admin assets
        add_action('admin_enqueue_scripts', array($this, 'register_admin_assets'));
    }

    /**
     * Register admin assets
     */
    public function register_admin_assets($hook)
    {
        // Load on all admin pages
        wp_enqueue_style(
            'serwis-natu-admin-style',
            SERWIS_NATU_URL . 'admin/css/serwis-natu-admin.css',
            array(),
            SERWIS_NATU_VERSION
        );
    }

    /**
     * Register and enqueue styles and scripts
     *
     * @return void
     */
    public function register_assets()
    {
        // Register CSS
        wp_register_style(
            'serwis-natu-style',
            SERWIS_NATU_URL . 'assets/css/serwis-natu.css',
            array(),
            SERWIS_NATU_VERSION
        );



        // Register Summary CSS
        wp_register_style(
            'serwis-natu-summary',
            SERWIS_NATU_URL . 'assets/css/summary.css',
            array('serwis-natu-style'),
            SERWIS_NATU_VERSION
        );

        // Register JS
        wp_register_script(
            'serwis-natu-script',
            SERWIS_NATU_URL . 'assets/js/serwis-natu.js',
            array('jquery'),
            SERWIS_NATU_VERSION,
            true
        );
    }

    /**
     * Render the form shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string The rendered shortcode content
     */
    public function render_form_shortcode($atts)
    {
        // Enqueue necessary styles and scripts
        wp_enqueue_style('serwis-natu-style');
        wp_enqueue_style('serwis-natu-extra-services');
        wp_enqueue_style('serwis-natu-recommended-products');
        wp_enqueue_style('serwis-natu-summary');
        wp_enqueue_script('serwis-natu-script');

        // Initialize form data for JavaScript
        $form_data = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('serwis-natu-form-nonce'),
            'ajaxNonce' => wp_create_nonce('serwis_natu_ajax_nonce'),
            'tooltips' => $this->get_tooltips(),
            'version' => SERWIS_NATU_VERSION,
        );

        // Make sure we localize the script after enqueuing it
        wp_localize_script('serwis-natu-script', 'serwisNatuData', $form_data);

        // Start output buffering
        ob_start();

        // Include the form template
        include SERWIS_NATU_PATH . 'templates/form-container.php';

        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * Get tooltips for form fields
     *
     * @return array Array of tooltips
     */
    private function get_tooltips()
    {
        return array(
            'jednorazowa_usluga' => __('Jednorazowa usługa serwisowa bez zobowiązań', 'serwis-natu'),
            'pakiet_wielorazowy' => __('Pakiet z regularnymi wizytami serwisowymi', 'serwis-natu'),
            'uslugi_dodatkowe' => __('Dodatkowe usługi dla akwarium', 'serwis-natu'),
            'lowtech' => __('Zbiorniki bez CO2 i intensywnego oświetlenia', 'serwis-natu'),
            'hightech' => __('Zbiorniki z CO2 i intensywnym oświetleniem', 'serwis-natu'),
            'biotopowy' => __('Zbiorniki biotopowe odwzorowujące naturalne środowiska', 'serwis-natu'),
            // Dodaj więcej tooltipów według potrzeb
        );
    }
}




add_action('wp_ajax_submit_order', 'handle_submit_order');
add_action('wp_ajax_nopriv_submit_order', 'handle_submit_order');

function handle_submit_order()
{
    global $wpdb;
    
    // Include WordPress media functions
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $table_name = $wpdb->prefix . 'serwis_natu_orders'; // replace with your table

    if (!isset($_POST['form_data'])) {
        wp_send_json_error('No data received');
    }

    $data = json_decode(stripslashes($_POST['form_data']), true);

    if (!$data) {
        wp_send_json_error('Invalid JSON');
    }
    
    // Process file uploads
    $uploaded_files = [];
    foreach ($_FILES as $key => $file) {
        // Check if it's an aquarium photo (format: aquarium_photo_X)
        if (strpos($key, 'aquarium_photo_') !== false && !empty($file['name'])) {
            // Extract aquarium index from the key (e.g., "aquarium_photo_1" => "1")
            $aquarium_index = str_replace('aquarium_photo_', '', $key);
            
            // Upload the file to the WordPress media library
            $upload = wp_upload_bits($file['name'], null, file_get_contents($file['tmp_name']));
            
            if (!$upload['error']) {
                $wp_filetype = wp_check_filetype($upload['file'], null);
                
                // Prepare attachment data
                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => sanitize_file_name($file['name']),
                    'post_content' => '',
                    'post_status' => 'inherit'
                );
                
                // Insert the attachment
                $attachment_id = wp_insert_attachment($attachment, $upload['file']);
                
                // Generate metadata for the attachment
                $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
                wp_update_attachment_metadata($attachment_id, $attach_data);
                
                // Store file information for later use
                $uploaded_files[$aquarium_index] = array(
                    'url' => $upload['url'],
                    'attachment_id' => $attachment_id
                );
            }
        }
    }
    
    // Add image URLs to aquarium data
    foreach ($uploaded_files as $index => $file_info) {
        if (isset($data['akw'][$index])) {
            $data['akw'][$index]['photo_url'] = $file_info['url'];
            $data['akw'][$index]['photo_attachment_id'] = $file_info['attachment_id'];
        }
    }
    
    // Merge aquariums and extra_services already done in JS
    $aquariums_json = maybe_serialize($data['akw']); // store as LONGTEXT (JSON)

    $insert_result = $wpdb->insert(
        $table_name,
        array(
            'client_first_name' => sanitize_text_field($data['client_first_name']),
            'client_last_name'  => sanitize_text_field($data['client_last_name']),
            'client_email'      => sanitize_email($data['client_email']),
            'client_phone'      => sanitize_text_field($data['client_phone']),
            'aquarium_address'  => sanitize_text_field($data['aquarium_address']),
            'preferred_date'    => date('Y-m-d H:i:s', strtotime($data['preferred_date'])),
            'additional_notes'  => sanitize_textarea_field($data['additional_notes']),
            'aquariums'         => wp_json_encode($data['akw']),
            'cooperative_mode'  => isset($data['tryb_wspolpracy']) ? sanitize_text_field($data['tryb_wspolpracy']) : '',
            'total_price'       => isset($data['total_cost']) ? floatval($data['total_cost']) : 0
        ),
        array(
            '%s', // client_first_name
            '%s', // client_last_name
            '%s', // client_email
            '%s', // client_phone
            '%s', // aquarium_address
            '%s', // preferred_date
            '%s', // additional_notes
            '%s', // aquariums (JSON)
            '%s', // cooperative_mode
            '%f'  // total_price
        )
    );
    
    // Get the order ID (last inserted ID)
    $order_id = $wpdb->insert_id;
    
    // Send confirmation email to customer
    if ($insert_result && $order_id) {
        // Prepare data for the email template
        $email_data = array(
            'imie' => $data['client_first_name'],
            'nazwisko' => $data['client_last_name'],
            'email' => $data['client_email'],
            'telefon' => $data['client_phone'],
            'adres' => $data['aquarium_address'],
            'preferowany_termin' => $data['preferred_date'],
            'tryb_wspolpracy' => isset($data['tryb_wspolpracy']) ? $data['tryb_wspolpracy'] : '',
            'cena' => isset($data['total_cost']) ? floatval($data['total_cost']) : 0,
        );
        
        // Start output buffering to capture the email template output
        ob_start();
        
        // Make variables available to the template
        $dane = $email_data;
        $zamowienie_id = $order_id;
        $aquariums = $data['akw']; // Pass aquarium data to the template
        
        // Include the email template
        include_once(SERWIS_NATU_PATH . 'templates/emails/email-order-confirmation.php');
        
        // Get the email content from the buffer
        $email_content = ob_get_clean();
        
        // Email headers
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: Natuscape <sklep@natuscape.pl>'
        );
        
        // Send the email
        $subject = sprintf(__('Potwierdzenie zamówienia usługi serwisowej #%d', 'serwis-natu'), $order_id);
        $sent = wp_mail($data['client_email'], $subject, $email_content, $headers);
        
        // Log email sending status
        if (!$sent) {
            error_log('Failed to send confirmation email for order #' . $order_id);
        }
    }

    wp_send_json_success('Order saved successfully!');
}
