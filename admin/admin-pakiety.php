<?php
/**
 * Admin settings for the Serwis Natu packages
 *
 * @package Serwis_Natu
 */

if (!defined('ABSPATH')) {
    exit; // Block direct access to the file
}

/**
 * Class for managing Serwis Natu packages
 */
class Serwis_Natu_Admin_Packages {

    /**
     * Option name for one-time packages
     */
    const OPTION_ONE_TIME_PACKAGES = 'serwis_natu_one_time_packages';

    /**
     * Option name for subscription packages
     */
    const OPTION_SUBSCRIPTION_PACKAGES = 'serwis_natu_subscription_packages';

    /**
     * Option name for additional services
     */
    const OPTION_ADDITIONAL_SERVICES = 'serwis_natu_additional_services';

    /**
     * Constructor
     */
    public function __construct() {
        // Register hooks
        add_action('admin_menu', array($this, 'add_admin_menu'), 20);
        add_action('admin_init', array($this, 'register_settings'));
        
        // Initialize default packages if none exist
        $this->maybe_initialize_packages();
        
        // Show admin notice if packages are not configured
        add_action('admin_notices', array($this, 'show_package_notice'));

        // Add custom styles for the admin page
        add_action('admin_head', array($this, 'admin_styles'));
    }
    
    /**
     * Show admin notice if packages are not configured
     */
    public function show_package_notice() {
        // Get all packages
        $one_time_packages = $this->get_one_time_packages();
        $subscription_packages = $this->get_subscription_packages();
        
        // Check if any package has a name (is configured)
        $has_configured_packages = false;
        
        foreach ($one_time_packages as $package) {
            if (!empty($package['name'])) {
                $has_configured_packages = true;
                break;
            }
        }
        
        if (!$has_configured_packages) {
            foreach ($subscription_packages as $package) {
                if (!empty($package['name'])) {
                    $has_configured_packages = true;
                    break;
                }
            }
        }
        
        // Show notice if no packages are configured
        if (!$has_configured_packages) {
            echo '<div class="notice notice-warning"><p>';
            printf(
                __('Pakiety usług serwisu akwarystycznego nie są skonfigurowane. <a href="%s">Kliknij tutaj</a> aby je skonfigurować.', 'serwis-natu'),
                admin_url('admin.php?page=serwis-natu-packages')
            );
            echo '</p></div>';
        }
    }
    
    /**
     * Initialize default empty packages if they don't exist
     */
    private function maybe_initialize_packages() {
        // Initialize one-time packages
        if (false === get_option(self::OPTION_ONE_TIME_PACKAGES)) {
            $empty_package = array(
                'package_1' => array(
                    'name' => '',
                    'price' => 0
                )
            );
            update_option(self::OPTION_ONE_TIME_PACKAGES, $empty_package);
        }
        
        // Initialize subscription packages
        if (false === get_option(self::OPTION_SUBSCRIPTION_PACKAGES)) {
            $empty_package = array(
                'package_1' => array(
                    'name' => '',
                    'price' => 0
                )
            );
            update_option(self::OPTION_SUBSCRIPTION_PACKAGES, $empty_package);
        }
        
        // Initialize additional services
        if (false === get_option(self::OPTION_ADDITIONAL_SERVICES)) {
            $empty_service = array(
                'service_1' => array(
                    'name' => '',
                    'price' => 0
                )
            );
            update_option(self::OPTION_ADDITIONAL_SERVICES, $empty_service);
        }
    }

    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        add_submenu_page(
            'serwis-natu-settings',
            __('Konfiguracja pakietów', 'serwis-natu'),
            __('Pakiety usług', 'serwis-natu'),
            'manage_options',
            'serwis-natu-packages',
            array($this, 'render_packages_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        // Register settings for one-time packages
        register_setting(
            'serwis_natu_packages',
            self::OPTION_ONE_TIME_PACKAGES,
            array($this, 'sanitize_packages')
        );

        // Register settings for subscription packages
        register_setting(
            'serwis_natu_packages',
            self::OPTION_SUBSCRIPTION_PACKAGES,
            array($this, 'sanitize_packages')
        );

        // Register settings for additional services
        register_setting(
            'serwis_natu_packages',
            self::OPTION_ADDITIONAL_SERVICES,
            array($this, 'sanitize_packages')
        );
    }

    /**
     * Sanitize package settings
     * 
     * @param array $input The input to sanitize
     * @return array Sanitized input
     */
    public function sanitize_packages($input) {
        $sanitized = array();
        
        if (is_array($input)) {
            foreach ($input as $key => $package) {
                $sanitized_key = sanitize_key($key);
                $sanitized[$sanitized_key] = array();
                
                if (isset($package['name'])) {
                    $sanitized[$sanitized_key]['name'] = sanitize_text_field($package['name']);
                }
                
                if (isset($package['price'])) {
                    $sanitized[$sanitized_key]['price'] = floatval($package['price']);
                }
            }
        }
        
        return $sanitized;
    }

    /**
     * Render the packages admin page
     */
    public function render_packages_page() {
        // Get current package settings or default values
        $one_time_packages = $this->get_one_time_packages();
        $subscription_packages = $this->get_subscription_packages();
        $additional_services = $this->get_additional_services();
        ?>
        <div class="wrap serwis-natu-packages-admin">
            <h1><?php _e('Konfiguracja pakietów Serwis Natu', 'serwis-natu'); ?></h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('serwis_natu_packages'); ?>
                
                <div class="packages-section">
                    <h2><?php _e('Pakiety jednorazowe', 'serwis-natu'); ?></h2>
                    <p class="description"><?php _e('Skonfiguruj nazwy i ceny pakietów jednorazowych.', 'serwis-natu'); ?></p>
                    
                    <table class="form-table package-table">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'serwis-natu'); ?></th>
                                <th><?php _e('Nazwa pakietu', 'serwis-natu'); ?></th>
                                <th><?php _e('Cena (zł)', 'serwis-natu'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($one_time_packages as $id => $package) : ?>
                                <tr>
                                    <td><code><?php echo esc_html($id); ?></code></td>
                                    <td>
                                        <input type="text" name="<?php echo esc_attr(self::OPTION_ONE_TIME_PACKAGES); ?>[<?php echo esc_attr($id); ?>][name]" 
                                            value="<?php echo esc_attr($package['name']); ?>" class="regular-text" />
                                    </td>
                                    <td>
                                        <input type="number" name="<?php echo esc_attr(self::OPTION_ONE_TIME_PACKAGES); ?>[<?php echo esc_attr($id); ?>][price]" 
                                            value="<?php echo esc_attr($package['price']); ?>" class="small-text" min="0" step="1" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="packages-section">
                    <h2><?php _e('Pakiety miesięczne', 'serwis-natu'); ?></h2>
                    <p class="description"><?php _e('Skonfiguruj nazwy i ceny pakietów miesięcznych (abonamentowych).', 'serwis-natu'); ?></p>
                    
                    <table class="form-table package-table">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'serwis-natu'); ?></th>
                                <th><?php _e('Nazwa pakietu', 'serwis-natu'); ?></th>
                                <th><?php _e('Cena (zł)', 'serwis-natu'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($subscription_packages as $id => $package) : ?>
                                <tr>
                                    <td><code><?php echo esc_html($id); ?></code></td>
                                    <td>
                                        <input type="text" name="<?php echo esc_attr(self::OPTION_SUBSCRIPTION_PACKAGES); ?>[<?php echo esc_attr($id); ?>][name]" 
                                            value="<?php echo esc_attr($package['name']); ?>" class="regular-text" />
                                    </td>
                                    <td>
                                        <input type="number" name="<?php echo esc_attr(self::OPTION_SUBSCRIPTION_PACKAGES); ?>[<?php echo esc_attr($id); ?>][price]" 
                                            value="<?php echo esc_attr($package['price']); ?>" class="small-text" min="0" step="1" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="packages-section">
                    <h2><?php _e('Dodatkowe usługi', 'serwis-natu'); ?></h2>
                    <p class="description"><?php _e('Skonfiguruj nazwy i ceny dodatkowych usług.', 'serwis-natu'); ?></p>
                    
                    <table class="form-table package-table">
                        <thead>
                            <tr>
                                <th><?php _e('ID', 'serwis-natu'); ?></th>
                                <th><?php _e('Nazwa usługi', 'serwis-natu'); ?></th>
                                <th><?php _e('Cena (zł)', 'serwis-natu'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($additional_services as $id => $service) : ?>
                                <tr>
                                    <td><code><?php echo esc_html($id); ?></code></td>
                                    <td>
                                        <input type="text" name="<?php echo esc_attr(self::OPTION_ADDITIONAL_SERVICES); ?>[<?php echo esc_attr($id); ?>][name]" 
                                            value="<?php echo esc_attr($service['name']); ?>" class="regular-text" />
                                    </td>
                                    <td>
                                        <input type="number" name="<?php echo esc_attr(self::OPTION_ADDITIONAL_SERVICES); ?>[<?php echo esc_attr($id); ?>][price]" 
                                            value="<?php echo esc_attr($service['price']); ?>" class="small-text" min="0" step="1" />
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php submit_button(__('Zapisz ustawienia', 'serwis-natu')); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Add custom CSS for the admin page
     */
    public function admin_styles() {
        echo '<style>
            .serwis-natu-packages-admin .packages-section {
                background: #fff;
                padding: 15px 20px;
                margin-bottom: 20px;
                border: 1px solid #ccd0d4;
                box-shadow: 0 1px 1px rgba(0,0,0,.04);
            }
            .serwis-natu-packages-admin .package-table {
                border-collapse: collapse;
                margin-top: 10px;
            }
            .serwis-natu-packages-admin .package-table th,
            .serwis-natu-packages-admin .package-table td {
                padding: 12px;
                border-bottom: 1px solid #f0f0f0;
            }
            .serwis-natu-packages-admin .package-table thead th {
                text-align: left;
                border-bottom: 2px solid #e0e0e0;
            }
        </style>';
    }

    /**
     * Get one-time packages
     * 
     * @return array One-time packages
     */
    public function get_one_time_packages() {
        // If no saved packages, return a single empty template
        $saved = get_option(self::OPTION_ONE_TIME_PACKAGES, array());
        if (empty($saved)) {
            return array(
                'package_1' => array(
                    'name' => '',
                    'price' => 0
                )
            );
        }
        return $saved;
    }
    
    /**
     * Get subscription packages
     * 
     * @return array Subscription packages
     */
    public function get_subscription_packages() {
        // If no saved packages, return a single empty template
        $saved = get_option(self::OPTION_SUBSCRIPTION_PACKAGES, array());
        if (empty($saved)) {
            return array(
                'package_1' => array(
                    'name' => '',
                    'price' => 0
                )
            );
        }
        return $saved;
    }
    
    /**
     * Get additional services
     * 
     * @return array Additional services
     */
    public function get_additional_services() {
        // If no saved packages, return a single empty template
        $saved = get_option(self::OPTION_ADDITIONAL_SERVICES, array());
        if (empty($saved)) {
            return array(
                'service_1' => array(
                    'name' => '',
                    'price' => 0
                )
            );
        }
        return $saved;
    }

    /**
     * Static method to get packages from outside the class
     * 
     * @return array All packages
     */
    public static function get_all_packages() {
        $instance = new self();
        
        return array(
            'one_time' => $instance->get_one_time_packages(),
            'subscription' => $instance->get_subscription_packages(),
            'additional' => $instance->get_additional_services(),
        );
    }
}

// Initialize the class
$serwis_natu_admin_packages = new Serwis_Natu_Admin_Packages();
