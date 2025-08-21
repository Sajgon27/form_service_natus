<?php
/**
 * WooCommerce My Account integration for Serwis Natu
 *
 * @package Serwis_Natu
 */

if (!defined('ABSPATH')) {
    exit; // Block direct access to the file
}

/**
 * Class for handling My Account integration
 */
class Serwis_Natu_My_Account {

    /**
     * The endpoint name for our custom My Account tab
     *
     * @var string
     */
    private $endpoint = 'historia-serwisowa';

    /**
     * Constructor
     */
    public function __construct() {
        // Register the new endpoint
        add_action('init', array($this, 'add_endpoints'));
        
        // Add the endpoint to WooCommerce navigation
        add_filter('woocommerce_account_menu_items', array($this, 'add_menu_item'));
        
        // Add content to the new endpoint
        add_action('woocommerce_account_' . $this->endpoint . '_endpoint', array($this, 'endpoint_content'));
        
        // Register query vars
        add_filter('query_vars', array($this, 'add_query_vars'), 0);
        
        // Flush rewrite rules if needed
        add_action('wp_loaded', array($this, 'flush_rewrite_rules_maybe'));
        
        // Plugin activation hook should be registered directly in the main plugin file, not here
        // The constructor doesn't have access to register_activation_hook properly
    }

    /**
     * Register the endpoint
     */
    public function add_endpoints() {
        add_rewrite_endpoint($this->endpoint, EP_ROOT | EP_PAGES);
    }

    /**
     * Add new query vars
     *
     * @param array $vars
     * @return array
     */
    public function add_query_vars($vars) {
        $vars[] = $this->endpoint;
        return $vars;
    }

    /**
     * Flush rewrite rules
     */
    public function flush_rewrite_rules() {
        add_rewrite_endpoint($this->endpoint, EP_ROOT | EP_PAGES);
        flush_rewrite_rules();
    }
    
    /**
     * Flush rewrite rules if needed
     */
    public function flush_rewrite_rules_maybe() {
        // Check both options to ensure we flush when needed
        $needs_flush = get_option('serwis_natu_needs_rewrite_flush', true);
        $endpoint_fixed = get_option('serwis_natu_endpoint_fixed', false);
        
        if ($needs_flush || !$endpoint_fixed) {
            $this->flush_rewrite_rules();
            update_option('serwis_natu_needs_rewrite_flush', false);
            update_option('serwis_natu_endpoint_fixed', true);
        }
    }

    /**
     * Add menu item to My Account navigation
     *
     * @param array $items
     * @return array
     */
    public function add_menu_item($items) {
        // Add our item after the Orders item
        $new_items = array();
        
        foreach ($items as $key => $value) {
            $new_items[$key] = $value;
            if ($key === 'orders') {
                $new_items[$this->endpoint] = __('Historia usług serwisowych', 'serwis-natu');
            }
        }
        
        return $new_items;
    }

    /**
     * Content for the service history endpoint
     */
    public function endpoint_content() {
        // Get current user ID
        $user_id = get_current_user_id();
        
        // If user is not logged in, show message
        if (!$user_id) {
            echo '<p>' . esc_html__('Proszę się zalogować, aby zobaczyć historię usług serwisowych.', 'serwis-natu') . '</p>';
            return;
        }
        
        // Query the database for orders linked to this user
        global $wpdb;
        $table_name = $wpdb->prefix . 'serwis_natu_orders';
        
        // Get orders for this user
        $orders = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table_name} WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            )
        );
        
        // Output the orders
        if ($orders && count($orders) > 0) {
            echo '<h2>' . esc_html__('Twoje usługi serwisowe', 'serwis-natu') . '</h2>';
            echo '<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>' . esc_html__('ID', 'serwis-natu') . '</th>';
            echo '<th>' . esc_html__('Data zamówienia', 'serwis-natu') . '</th>';
            echo '<th>' . esc_html__('Preferowany termin', 'serwis-natu') . '</th>';
            echo '<th>' . esc_html__('Adres', 'serwis-natu') . '</th>';
            echo '<th>' . esc_html__('Cena', 'serwis-natu') . '</th>';
            echo '<th>' . esc_html__('Akcje', 'serwis-natu') . '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            
            foreach ($orders as $order) {
                echo '<tr>';
                echo '<td>' . esc_html($order->id) . '</td>';
                echo '<td>' . esc_html(date_i18n(get_option('date_format'), strtotime($order->created_at))) . '</td>';
                echo '<td>' . esc_html(date_i18n(get_option('date_format'), strtotime($order->preferred_date))) . '</td>';
                echo '<td>' . esc_html($order->aquarium_address) . '</td>';
                echo '<td>' . number_format((float)$order->total_price, 2, ',', ' ') . ' zł</td>';
                echo '<td>';
                echo '<a href="#" class="button view" data-order-id="' . esc_attr($order->id) . '">' . esc_html__('Szczegóły', 'serwis-natu') . '</a>';
                echo '</td>';
                echo '</tr>';
                
                // Hidden details container that will be shown/hidden via JavaScript
                echo '<tr class="order-details-row" id="order-details-' . esc_attr($order->id) . '" style="display: none;">';
                echo '<td colspan="6">';
                echo '<div class="order-details-container">';
                
                // Customer details
                echo '<div class="customer-details">';
                echo '<h4>' . esc_html__('Dane klienta', 'serwis-natu') . '</h4>';
                echo '<p><strong>' . esc_html__('Imię:', 'serwis-natu') . '</strong> ' . esc_html($order->client_first_name) . '</p>';
                echo '<p><strong>' . esc_html__('Nazwisko:', 'serwis-natu') . '</strong> ' . esc_html($order->client_last_name) . '</p>';
                echo '<p><strong>' . esc_html__('Email:', 'serwis-natu') . '</strong> ' . esc_html($order->client_email) . '</p>';
                echo '<p><strong>' . esc_html__('Telefon:', 'serwis-natu') . '</strong> ' . esc_html($order->client_phone) . '</p>';
                echo '</div>';
                
                // Aquarium details
                $aquariums = json_decode($order->aquariums, true);
                if ($aquariums && is_array($aquariums)) {
                    echo '<div class="aquariums-details">';
                    echo '<h4>' . esc_html__('Akwaria', 'serwis-natu') . '</h4>';
                    
                    foreach ($aquariums as $akw_id => $aquarium) {
                        echo '<div class="aquarium-item">';
                        echo '<h5>' . esc_html__('Akwarium #', 'serwis-natu') . esc_html($akw_id) . '</h5>';
                        
                        // Display aquarium photo if available
                        if (isset($aquarium['photo_url'])) {
                            echo '<div class="aquarium-photo">';
                            echo '<img src="' . esc_url($aquarium['photo_url']) . '" alt="' . esc_attr__('Zdjęcie akwarium', 'serwis-natu') . '" />';
                            echo '</div>';
                        }
                        
                        echo '<div class="aquarium-properties">';
                        if (isset($aquarium['typ'])) {
                            echo '<p><strong>' . esc_html__('Typ:', 'serwis-natu') . '</strong> ' . esc_html($aquarium['typ']) . '</p>';
                        }
                        if (isset($aquarium['wielkosc'])) {
                            echo '<p><strong>' . esc_html__('Wielkość:', 'serwis-natu') . '</strong> ' . esc_html($aquarium['wielkosc']) . '</p>';
                        }
                        echo '</div>';
                        
                        // Display additional services
                        if (!empty($aquarium['uslugi']) && is_array($aquarium['uslugi'])) {
                            echo '<div class="aquarium-services">';
                            echo '<h6>' . esc_html__('Wybrane usługi', 'serwis-natu') . '</h6>';
                            echo '<ul>';
                            foreach ($aquarium['uslugi'] as $service) {
                                echo '<li>' . esc_html($service) . '</li>';
                            }
                            echo '</ul>';
                            echo '</div>';
                        }
                        
                        echo '</div>'; // End of aquarium-item
                    }
                    
                    echo '</div>'; // End of aquariums-details
                }
                
                // Additional notes
                if (!empty($order->additional_notes)) {
                    echo '<div class="additional-notes">';
                    echo '<h4>' . esc_html__('Uwagi dodatkowe', 'serwis-natu') . '</h4>';
                    echo '<p>' . nl2br(esc_html($order->additional_notes)) . '</p>';
                    echo '</div>';
                }
                
                echo '</div>'; // End of order-details-container
                echo '</td>';
                echo '</tr>';
            }
            
            echo '</tbody>';
            echo '</table>';
            
            // Add JavaScript to toggle details
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('.view').on('click', function(e) {
                    e.preventDefault();
                    var orderId = $(this).data('order-id');
                    $('#order-details-' + orderId).toggle();
                });
            });
            </script>
            <style>
            .order-details-container {
                padding: 15px;
                background-color: #f8f8f8;
                border: 1px solid #ddd;
                margin: 10px 0;
            }
            .aquarium-item {
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px dashed #ddd;
            }
            .aquarium-item:last-child {
                border-bottom: none;
            }
            .aquarium-photo img {
                max-width: 200px;
                height: auto;
                margin: 10px 0;
                border: 1px solid #ddd;
            }
            </style>
            <?php
        } else {
            echo '<p>' . esc_html__('Nie masz jeszcze żadnych zamówionych usług serwisowych.', 'serwis-natu') . '</p>';
            
            // Add a link to the form if you have a page with the form shortcode
            $form_page_id = get_option('serwis_natu_form_page');
            if ($form_page_id) {
                echo '<p><a href="' . esc_url(get_permalink($form_page_id)) . '" class="button">' . esc_html__('Zamów usługę serwisową', 'serwis-natu') . '</a></p>';
            }
        }
    }
}
