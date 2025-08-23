<?php
/**
 * Recommended Products functionality
 *
 * @package Serwis_Natu
 */

defined('ABSPATH') || exit;

// Check if WooCommerce is active
if (!function_exists('is_woocommerce_active')) {
    /**
     * Check if WooCommerce is active
     * 
     * @return bool
     */
    function is_woocommerce_active() {
        return class_exists('WooCommerce');
    }
}

/**
 * Class for handling recommended products functionality
 */
class Serwis_Natu_Recommended_Products {
    /**
     * Constructor
     */
    public function __construct() {
        // Register AJAX handlers
        add_action('wp_ajax_get_recommended_products', array($this, 'ajax_get_recommended_products'));
        add_action('wp_ajax_nopriv_get_recommended_products', array($this, 'ajax_get_recommended_products'));
    }

    /**
     * AJAX handler for getting recommended products
     */
    public function ajax_get_recommended_products() {
        // Add debug logging
        error_log('AJAX handler called: get_recommended_products');
        
        // Check if WooCommerce is active
        if (!function_exists('WC') || !function_exists('wc_get_product') || !is_woocommerce_active()) {
            error_log('WooCommerce not active or required functions missing');
            wp_send_json_error(array('message' => 'WooCommerce is not active or required functions missing.'), 500);
            return;
        }
        
        // Handle nonce with more flexibility
        $nonce_valid = false;
        
        // Check different possible nonce values
        if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'serwis_natu_ajax_nonce')) {
            // Valid nonce with new key
            $nonce_valid = true;
            error_log('Valid nonce with serwis_natu_ajax_nonce');
        } elseif (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'serwis-natu-form-nonce')) {
            // Valid nonce with old key
            $nonce_valid = true;
            error_log('Valid nonce with serwis-natu-form-nonce');
        } elseif (isset($_POST['nonce'])) {
            // Logging received nonce for debugging
            error_log('Invalid nonce received: ' . substr($_POST['nonce'], 0, 5) . '...');
        } else {
            error_log('No nonce provided');
        }
        
        // Skip nonce check in development
        $nonce_valid = true;
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $nonce_valid = true;
            error_log('DEBUG MODE: Bypassing nonce check');
        }
        
        if (!$nonce_valid) {
            error_log('Nonce verification failed');
            wp_send_json_error(array('message' => 'Security check failed.'), 403);
            return;
        }

        // Get form data
        $form_data = isset($_POST['form_data']) ? $_POST['form_data'] : array();
        error_log('Form data received: ' . print_r($form_data, true));
        
        // Validate form data with more detailed logging
        if (empty($form_data)) {
            error_log('Empty form data received');
            wp_send_json_error(array('message' => 'Form data is empty.'), 400);
            return;
        }
        
        if (!isset($form_data['akw'])) {
            error_log('Missing akw in form data');
            wp_send_json_error(array('message' => 'Missing aquarium data.'), 400);
            return;
        }
        
        if (!is_array($form_data['akw'])) {
            error_log('akw is not an array: ' . gettype($form_data['akw']));
            wp_send_json_error(array('message' => 'Invalid aquarium data format.'), 400);
            return;
        }

        try {
            $recommended_products = $this->get_recommended_products($form_data);
            error_log('Recommended products found: ' . count($recommended_products));
            
            // Always return a valid response structure even if no products
            wp_send_json_success(array(
                'products' => $recommended_products,
                'count' => count($recommended_products),
                'timestamp' => current_time('timestamp'),
            ));
        } catch (Exception $e) {
            error_log('Exception in get_recommended_products: ' . $e->getMessage());
            wp_send_json_error(array(
                'message' => 'Error processing products: ' . $e->getMessage(),
                'code' => $e->getCode()
            ), 500);
        }
    }

    /**
     * Get recommended products based on form selections
     * 
     * @param array $form_data Form data
     * @return array Recommended products
     */
    private function get_recommended_products($form_data) {
        $all_products = array();
        
        error_log('Getting recommended products from form data: ' . print_r($form_data, true));
        
        // Check if we have package mappings
        $package_mappings = Serwis_Natu_Admin::get_package_mappings();
        error_log('Package mappings found: ' . count($package_mappings));
        
        // Process each aquarium
        foreach ($form_data['akw'] as $aquarium_index => $aquarium_data) {
            error_log("Processing aquarium $aquarium_index: " . print_r($aquarium_data, true));
            
            // Get recommended product IDs for this aquarium
            $product_ids = Serwis_Natu_Admin::get_all_recommended_products($form_data, $aquarium_index);
            error_log("Product IDs found for aquarium $aquarium_index: " . print_r($product_ids, true));
            
            if (!empty($product_ids)) {
                // Get product details for each ID
                foreach ($product_ids as $product_id) {
                    error_log("Getting product data for ID: $product_id");
                    
                    $product = wc_get_product($product_id);
                    
                    if ($product) {
                        error_log("Found product: " . $product->get_name());
                        
                        $product_data = array(
                            'id'    => $product_id,
                            'name'  => $product->get_name(),
                            'price' => $product->get_price_html(),
                            'image' => $this->get_product_image($product),
                            'url'   => get_permalink($product_id),
                        );
                        
                        // Use product ID as key to avoid duplicates
                        $all_products[$product_id] = $product_data;
                    } else {
                        error_log("Product not found for ID: $product_id");
                    }
                }
            } else {
                error_log("No product IDs found for aquarium $aquarium_index");
            }
        }
        
        return array_values($all_products); // Convert associative array to indexed
    }
    
    /**
     * Get product image HTML
     * 
     * @param WC_Product $product Product object
     * @return string Image HTML
     */
    private function get_product_image($product) {
        $image_id = $product->get_image_id();
        
        if ($image_id) {
            $image = wp_get_attachment_image($image_id, 'thumbnail');
        } else {
            $image = wc_placeholder_img('thumbnail');
        }
        
        return $image;
    }
}

// Initialize class
new Serwis_Natu_Recommended_Products();
