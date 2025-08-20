<?php
/**
 * Admin settings for the Serwis Natu plugin
 *
 * @package Serwis_Natu
 */

if (!defined('ABSPATH')) {
    exit; // Blokowanie bezpośredniego dostępu do pliku
}

/**
 * Admin settings class
 */
class Serwis_Natu_Admin {
    /**
     * Admin page slug
     *
     * @var string
     */
    private $page_slug = 'serwis-natu-settings';
    
    /**
     * Option group name
     *
     * @var string
     */
    private $option_group = 'serwis_natu_options';
    
    /**
     * Option name for package mappings
     *
     * @var string
     */
    private $option_name = 'serwis_natu_package_mappings';
    
    /**
     * Available one-time packages
     *
     * @var array
     */
    private $one_time_packages = array();
    
    /**
     * Available subscription packages
     *
     * @var array
     */
    private $subscription_packages = array();

    /**
     * Constructor
     */
    public function __construct() {
        // Define available packages
        $this->one_time_packages = array(
            'basic' => array(
                'name' => __('Podstawowy serwis akwarium', 'serwis-natu'),
                'price' => 150
            ),
            'extended' => array(
                'name' => __('Rozszerzony serwis akwarium', 'serwis-natu'),
                'price' => 250
            ),
            'complete' => array(
                'name' => __('Serwis akwarium od A do Z', 'serwis-natu'),
                'price' => 350
            ),
            'consultation' => array(
                'name' => __('Konsultacja akwarystyczna', 'serwis-natu'),
                'price' => 100
            )
        );
        
        $this->subscription_packages = array(
            'monthly_basic' => array(
                'name' => __('Podstawowy pakiet miesięczny', 'serwis-natu'),
                'price' => 300
            ),
            'monthly_extended' => array(
                'name' => __('Rozszerzony pakiet miesięczny', 'serwis-natu'),
                'price' => 500
            ),
            'monthly_complete' => array(
                'name' => __('Kompleksowy pakiet miesięczny', 'serwis-natu'),
                'price' => 800
            )
        );
        
        // Register hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Ustawienia Serwis Natu', 'serwis-natu'),
            __('Serwis Natu', 'serwis-natu'),
            'manage_options',
            'serwis-natu-settings',
            array($this, 'render_settings_page'),
            'dashicons-admin-settings',
            100
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            $this->option_group,
            $this->option_name,
            array($this, 'sanitize_package_mappings')
        );

        // Add settings section
        add_settings_section(
            'package_mappings_section',
            __('Mapowanie opcji na pakiety', 'serwis-natu'),
            array($this, 'render_section_description'),
            $this->page_slug
        );
        
        // Add settings fields for checkbox to package mappings
        $this->add_checkbox_fields();
        
      
    }
    
  

    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields($this->option_group);
                do_settings_sections($this->page_slug);
                submit_button(__('Zapisz ustawienia', 'serwis-natu'));
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Render section description
     */
    public function render_section_description() {
        ?>
        <p><?php _e('Skonfiguruj, które opcje formularza mają odpowiadać którym pakietom oraz jakie produkty mają być polecane dla poszczególnych opcji. Dla każdej opcji, wybierz odpowiedni pakiet jednorazowy, miesięczny oraz do dwóch produktów WooCommerce, które chcesz polecić.', 'serwis-natu'); ?></p>
        <?php
    }

    /**
     * Add checkbox fields for package mapping
     */
   private function add_checkbox_fields() {
    // Get all checkbox options from the form
    $checkboxes = $this->get_form_checkboxes();
    
    // Get current saved mappings
    $current_mappings = get_option($this->option_name, array());
    
    // Group checkboxes by category for better UI organization
    $categories = array(
        'typ'    => __('Typ akwarium', 'serwis-natu'),
        'cel'    => __('Cel zgłoszenia', 'serwis-natu'),
        'zakres' => __('Zakres oczekiwanych działań', 'serwis-natu'),
        'inne'   => __('Inne potrzeby', 'serwis-natu')
    );
    
    foreach ($categories as $category_key => $category_name) {
        // Get checkboxes for this category (regex instead of hardcoded akw[1])
        $category_checkboxes = array_filter($checkboxes, function($checkbox) use ($category_key) {
            return preg_match('/akw\[\d+\]\[' . preg_quote($category_key, '/') . '\]\[\]/', $checkbox['name']);
        });
        
        if (!empty($category_checkboxes)) {
            // Add a heading for the category
            add_settings_field(
                "category_heading_{$category_key}",
                "<h3>{$category_name}</h3>",
                function() {},
                $this->page_slug,
                'package_mappings_section'
            );
            
            // Add fields for each checkbox in this category
            foreach ($category_checkboxes as $checkbox) {
                // Strip "akw[1][...]" from the name, just keep the value as ID
                $field_id = $checkbox['value'];
                
                add_settings_field(
                    "package_mapping_{$field_id}",
                    $checkbox['label'],
                    array($this, 'render_package_mapping_field'),
                    $this->page_slug,
                    'package_mappings_section',
                    array(
                        'id' => $field_id,
                        'name' => $field_id,
                        'current_mappings' => $current_mappings,
                    )
                );
            }
        }
    }
}

    /**
     * Render package mapping field
     *
     * @param array $args Field arguments
     */
    public function render_package_mapping_field($args) {
        $id = $args['id'];
        $name = $args['name'];
        $current_mappings = $args['current_mappings'];
        
        $one_time_value = isset($current_mappings[$name]['one_time']) ? $current_mappings[$name]['one_time'] : '';
        $subscription_value = isset($current_mappings[$name]['subscription']) ? $current_mappings[$name]['subscription'] : '';
        
        // Get product mappings
        $product_1_id = isset($current_mappings[$name]['product_1']) ? $current_mappings[$name]['product_1'] : '';
        $product_2_id = isset($current_mappings[$name]['product_2']) ? $current_mappings[$name]['product_2'] : '';
        
        // Get all WooCommerce products
        $products = array();
        if (function_exists('wc_get_products')) {
            $args = array(
                'status' => 'publish',
                'limit' => -1,
            );
            $wc_products = wc_get_products($args);
            
            foreach ($wc_products as $product) {
                $products[$product->get_id()] = $product->get_name() . ' (' . wc_price($product->get_price()) . ')';
            }
        }
        ?>
        <div class="package-mapping-row">
            <div class="package-mapping-column">
                <label><?php _e('Pakiet jednorazowy:', 'serwis-natu'); ?></label>
                <select name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($name); ?>][one_time]">
                    <option value=""><?php _e('-- Wybierz pakiet --', 'serwis-natu'); ?></option>
                    <?php foreach ($this->one_time_packages as $package_key => $package) : ?>
                        <option value="<?php echo esc_attr($package_key); ?>" <?php selected($one_time_value, $package_key); ?>>
                            <?php echo esc_html($package['name']); ?> (<?php echo esc_html($package['price']); ?> zł)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="package-mapping-column">
                <label><?php _e('Pakiet miesięczny:', 'serwis-natu'); ?></label>
                <select name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($name); ?>][subscription]">
                    <option value=""><?php _e('-- Wybierz pakiet --', 'serwis-natu'); ?></option>
                    <?php foreach ($this->subscription_packages as $package_key => $package) : ?>
                        <option value="<?php echo esc_attr($package_key); ?>" <?php selected($subscription_value, $package_key); ?>>
                            <?php echo esc_html($package['name']); ?> (<?php echo esc_html($package['price']); ?> zł)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="product-mapping-row">
            <div class="product-mapping-column">
                <label><?php _e('Polecany produkt 1:', 'serwis-natu'); ?></label>
                <select name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($name); ?>][product_1]">
                    <option value=""><?php _e('-- Wybierz produkt --', 'serwis-natu'); ?></option>
                    <?php foreach ($products as $product_id => $product_name) : ?>
                        <option value="<?php echo esc_attr($product_id); ?>" <?php selected($product_1_id, $product_id); ?>>
                            <?php echo wp_kses_post($product_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="product-mapping-column">
                <label><?php _e('Polecany produkt 2:', 'serwis-natu'); ?></label>
                <select name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($name); ?>][product_2]">
                    <option value=""><?php _e('-- Wybierz produkt --', 'serwis-natu'); ?></option>
                    <?php foreach ($products as $product_id => $product_name) : ?>
                        <option value="<?php echo esc_attr($product_id); ?>" <?php selected($product_2_id, $product_id); ?>>
                            <?php echo wp_kses_post($product_name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php
    }

    /**
     * Get all checkbox options from the form
     *
     * @return array Array of checkbox options
     */
    private function get_form_checkboxes() {
        // Hardcoded form structure since we're not parsing the HTML directly
        $checkboxes = array(
            // Typ akwarium
            array(
                'name' => 'akw[1][typ][]',
                'value' => 'lowtech',
                'label' => __('Lowtech', 'serwis-natu'),
                'products' => array() // Array to store selected product IDs
            ),
            array(
                'name' => 'akw[1][typ][]',
                'value' => 'hightech',
                'label' => __('Hightech', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][typ][]',
                'value' => 'biotopowy',
                'label' => __('Biotopowy', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][typ][]',
                'value' => 'roslinny',
                'label' => __('Roślinny', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][typ][]',
                'value' => 'dekoracyjny',
                'label' => __('Dekoracyjny / Ekspozycyjny', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][typ][]',
                'value' => 'firmowe',
                'label' => __('Akwarium firmowe / usługowe', 'serwis-natu'),
                'products' => array()
            ),
            
            // Cel zgłoszenia
            array(
                'name' => 'akw[1][cel][]',
                'value' => 'regularna',
                'label' => __('Regularna pielęgnacja akwarium', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][cel][]',
                'value' => 'czyszczenie',
                'label' => __('Gruntowne czyszczenie i porządki', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][cel][]',
                'value' => 'restart',
                'label' => __('Restart zbiornika', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][cel][]',
                'value' => 'dorazna',
                'label' => __('Pomoc doraźna', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][cel][]',
                'value' => 'aranzacja',
                'label' => __('Zmiana aranżacji', 'serwis-natu'),
                'products' => array()
            ),
            
            // Zakres oczekiwanych działań
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'wymiana',
                'label' => __('Podmiana i/lub dolanie wody', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'odmulanie',
                'label' => __('Odmulanie dna', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'czyszczenieSzyb',
                'label' => __('Czyszczenie szyb i dekoracji', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'przycinanie',
                'label' => __('Przycinanie/obsadzanie roślin', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'filtr',
                'label' => __('Czyszczenie/konserwacja filtra', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'nawozenie',
                'label' => __('Sprawdzenie i korekta nawożenia', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'badanie',
                'label' => __('Badanie parametrów wody', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'glony',
                'label' => __('Likwidacja glonów', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'przeglad',
                'label' => __('Przegląd techniczny sprzętu', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'montazSprzetu',
                'label' => __('Montaż nowego sprzętu', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'ocenaObsady',
                'label' => __('Ocena stanu obsady', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'szkolenie',
                'label' => __('Szkolenie / instruktaż', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'aranzacjaDekoracji',
                'label' => __('Zmiana aranżacji/dekoracji', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][zakres][]',
                'value' => 'aplikacja',
                'label' => __('Aplikacja preparatów', 'serwis-natu'),
                'products' => array()
            ),
            
            // Inne potrzeby
            array(
                'name' => 'akw[1][inne][]',
                'value' => 'jednorazowa',
                'label' => __('Potrzebuję tylko jednorazowej pomocy', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][inne][]',
                'value' => 'stala',
                'label' => __('Jestem zainteresowany stałą opieką', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][inne][]',
                'value' => 'kompleksowo',
                'label' => __('Chcę, by ktoś kompleksowo zajął się moim zbiornikiem', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][inne][]',
                'value' => 'okazja',
                'label' => __('Chcę przygotować akwarium na konkretną okazję', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][inne][]',
                'value' => 'wakacje',
                'label' => __('Wyjeżdżam na wakacje', 'serwis-natu'),
                'products' => array()
            ),
            array(
                'name' => 'akw[1][inne][]',
                'value' => 'diagnoza',
                'label' => __('Mam problem - potrzebuję diagnozy', 'serwis-natu'),
                'products' => array()
            )
        );
        
        return $checkboxes;
    }

    /**
     * Sanitize package mappings
     *
     * @param array $input The input to sanitize
     * @return array Sanitized input
     */
    public function sanitize_package_mappings($input) {
        $sanitized = array();
        
        error_log('Sanitizing package mappings: ' . print_r($input, true));
        
        if (is_array($input)) {
            foreach ($input as $key => $values) {
                // Sanitize the key
                $key = sanitize_text_field($key);
                
                // Package mappings
                if (isset($values['one_time'])) {
                    $sanitized[$key]['one_time'] = sanitize_text_field($values['one_time']);
                }
                
                if (isset($values['subscription'])) {
                    $sanitized[$key]['subscription'] = sanitize_text_field($values['subscription']);
                }
                
                // Product mappings
                if (isset($values['product_1'])) {
                    $sanitized[$key]['product_1'] = absint($values['product_1']);
                }
                
                if (isset($values['product_2'])) {
                    $sanitized[$key]['product_2'] = absint($values['product_2']);
                }
            }
        }
        
        error_log('Sanitized package mappings: ' . print_r($sanitized, true));
        
        return $sanitized;
    }

    /**
     * Get all available packages
     *
     * @return array All packages
     */
    public function get_packages() {
        return array(
            'one_time' => $this->one_time_packages,
            'subscription' => $this->subscription_packages
        );
    }

    /**
     * Get package mapping settings
     *
     * @return array Package mappings
     */
    public static function get_package_mappings() {
        return get_option('serwis_natu_package_mappings', array());
    }
    
    /**
     * Get recommended products for a checkbox
     *
     * @param string $checkbox_id The checkbox ID (value)
     * @return array Array of product IDs
     */
    public static function get_recommended_products($checkbox_id) {
        $package_mappings = self::get_package_mappings();
        $products = array();
        
        if (isset($package_mappings[$checkbox_id])) {
            // Product 1
            if (!empty($package_mappings[$checkbox_id]['product_1'])) {
                $products[] = $package_mappings[$checkbox_id]['product_1'];
            }
            
            // Product 2
            if (!empty($package_mappings[$checkbox_id]['product_2'])) {
                $products[] = $package_mappings[$checkbox_id]['product_2'];
            }
        }
        
        return $products;
    }

    /**
     * Get recommended package based on form selections
     *
     * @param array $form_data Form data
     * @param string $service_type Service type ('jednorazowa' or 'pakiet')
     * @param int $aquarium_index Aquarium index
     * @return array|bool Package data or false if no mapping found
     */
    public static function get_recommended_package($form_data, $service_type, $aquarium_index) {
        $package_mappings = self::get_package_mappings();
        $available_packages = self::get_all_packages();
        
        // For debugging
        error_log('Package mappings: ' . print_r($package_mappings, true));
        
        // Determine package type based on service mode
        $package_type = ($service_type === 'jednorazowa') ? 'one_time' : 'subscription';
        
        // Get the aquarium's selections
        $aquarium_data = isset($form_data['akw'][$aquarium_index]) ? $form_data['akw'][$aquarium_index] : array();
        
        // If we don't have data for this aquarium, return default basic package
        if (empty($aquarium_data)) {
            $default_key = ($package_type === 'one_time') ? 'basic' : 'monthly_basic';
            return $available_packages[$package_type][$default_key];
        }
        
        // Find the highest level package that matches selections
        $highest_package_level = 0;
        $highest_package_key = '';
        
        // Package priorities (lowest to highest)
        $package_priorities = array(
            'one_time' => array(
                'consultation' => 1,
                'basic' => 2,
                'extended' => 3,
                'complete' => 4
            ),
            'subscription' => array(
                'monthly_basic' => 1,
                'monthly_extended' => 2,
                'monthly_complete' => 3
            )
        );
        
        // Combine all selections from this aquarium
        $all_selections = array();
        foreach ($aquarium_data as $category => $selections) {
            if (is_array($selections)) {
                foreach ($selections as $selection) {
                    $all_selections[] = $selection;
                }
            }
        }
        
        // Find matching packages for each selection
        foreach ($all_selections as $selection) {
            // Skip if no mapping exists for this selection
            if (!isset($package_mappings[$selection][$package_type])) {
                continue;
            }
            
            $mapped_package = $package_mappings[$selection][$package_type];
            
            // Skip empty mappings
            if (empty($mapped_package)) {
                continue;
            }
            
            // Check if this mapping has a higher priority than current highest
            if ($package_priorities[$package_type][$mapped_package] > $highest_package_level) {
                $highest_package_level = $package_priorities[$package_type][$mapped_package];
                $highest_package_key = $mapped_package;
            }
        }
        
        // If we found a matching package, return its data
        if (!empty($highest_package_key) && isset($available_packages[$package_type][$highest_package_key])) {
            return array(
                'key' => $highest_package_key,
                'name' => $available_packages[$package_type][$highest_package_key]['name'],
                'price' => $available_packages[$package_type][$highest_package_key]['price'],
            );
        }
        
        // Default to basic package if no mapping found
        $default_key = ($package_type === 'one_time') ? 'basic' : 'monthly_basic';
        return array(
            'key' => $default_key,
            'name' => $available_packages[$package_type][$default_key]['name'],
            'price' => $available_packages[$package_type][$default_key]['price'],
        );
    }
    
    /**
     * Get all recommended products based on selected checkboxes
     *
     * @param array $form_data Form data
     * @param int $aquarium_index Aquarium index
     * @return array Array of product IDs
     */
    public static function get_all_recommended_products($form_data, $aquarium_index) {
        $products = array();
        
        // Get the aquarium's selections
        $aquarium_data = isset($form_data['akw'][$aquarium_index]) ? $form_data['akw'][$aquarium_index] : array();
        
        // If we don't have data for this aquarium, return empty array
        if (empty($aquarium_data)) {
            return $products;
        }
        
        // Combine all selections from this aquarium
        $all_selections = array();
        foreach ($aquarium_data as $category => $selections) {
            if (is_array($selections)) {
                foreach ($selections as $selection) {
                    // Get recommended products for this checkbox
                    $checkbox_products = self::get_recommended_products($selection);
                    
                    if (!empty($checkbox_products)) {
                        $products = array_merge($products, $checkbox_products);
                    }
                }
            }
        }
        
        // Return unique products
        return array_unique($products);
    }
    
    /**
     * Get all available packages (static method)
     *
     * @return array All packages
     */
    public static function get_all_packages() {
        return array(
            'one_time' => array(
                'basic' => array(
                    'name' => __('Podstawowy serwis akwarium', 'serwis-natu'),
                    'price' => 150
                ),
                'extended' => array(
                    'name' => __('Rozszerzony serwis akwarium', 'serwis-natu'),
                    'price' => 250
                ),
                'complete' => array(
                    'name' => __('Serwis akwarium od A do Z', 'serwis-natu'),
                    'price' => 350
                ),
                'consultation' => array(
                    'name' => __('Konsultacja akwarystyczna', 'serwis-natu'),
                    'price' => 100
                )
            ),
            'subscription' => array(
                'monthly_basic' => array(
                    'name' => __('Podstawowy pakiet miesięczny', 'serwis-natu'),
                    'price' => 300
                ),
                'monthly_extended' => array(
                    'name' => __('Rozszerzony pakiet miesięczny', 'serwis-natu'),
                    'price' => 500
                ),
                'monthly_complete' => array(
                    'name' => __('Kompleksowy pakiet miesięczny', 'serwis-natu'),
                    'price' => 800
                )
            )
        );
    }
}
