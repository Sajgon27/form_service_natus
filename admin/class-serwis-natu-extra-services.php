<?php
/**
 * Admin panel for managing extra services
 *
 * @package Serwis_Natu
 */

if (!defined('ABSPATH')) {
    exit; // Blokowanie bezpośredniego dostępu do pliku
}

/**
 * Extra services admin class
 */
class Serwis_Natu_Extra_Services {
    /**
     * Admin page slug
     *
     * @var string
     */
    private $page_slug = 'serwis-natu-extra-services';
    
    /**
     * Option group name
     *
     * @var string
     */
    private $option_group = 'serwis_natu_extra_services';
    
    /**
     * Option name for extra services
     *
     * @var string
     */
    private $option_name = 'serwis_natu_extra_services_data';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Register hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        add_submenu_page(
            'serwis-natu-settings', // Parent slug
            __('Usługi Dodatkowe', 'serwis-natu'),
            __('Usługi Dodatkowe', 'serwis-natu'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            $this->option_group,
            $this->option_name,
            array($this, 'sanitize_extra_services')
        );

        // Add settings section
        add_settings_section(
            'extra_services_section',
            __('Konfiguracja Usług Dodatkowych', 'serwis-natu'),
            array($this, 'render_section_description'),
            $this->page_slug
        );
        
        // Add settings fields
        $this->add_extra_services_fields();
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
                ?>
                
                <div class="extra-services-actions">
                    <button type="button" id="add-extra-service" class="button button-secondary">
                        <?php _e('Dodaj nową usługę', 'serwis-natu'); ?>
                    </button>
                </div>
                
                <?php submit_button(__('Zapisz ustawienia', 'serwis-natu')); ?>
            </form>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Add new extra service
            $('#add-extra-service').on('click', function() {
                var index = $('.extra-service-row').length;
                var template = `
                    <div class="extra-service-row">
                        <h3><?php _e('Usługa Dodatkowa', 'serwis-natu'); ?> #${index + 1}</h3>
                        <div class="service-field">
                            <label><?php _e('Identyfikator:', 'serwis-natu'); ?></label>
                            <input type="text" name="<?php echo esc_attr($this->option_name); ?>[${index}][id]" 
                                placeholder="<?php _e('np. badanie_wody', 'serwis-natu'); ?>" required>
                        </div>
                        <div class="service-field">
                            <label><?php _e('Nazwa usługi:', 'serwis-natu'); ?></label>
                            <input type="text" name="<?php echo esc_attr($this->option_name); ?>[${index}][name]" 
                                placeholder="<?php _e('np. Badanie wody', 'serwis-natu'); ?>" required>
                        </div>
                        <div class="service-field">
                            <label><?php _e('Cena (zł):', 'serwis-natu'); ?></label>
                            <input type="number" name="<?php echo esc_attr($this->option_name); ?>[${index}][price]" 
                                min="0" step="1" placeholder="50" required>
                        </div>
                        <div class="service-field">
                            <label><?php _e('Opis (tooltip):', 'serwis-natu'); ?></label>
                            <textarea name="<?php echo esc_attr($this->option_name); ?>[${index}][tooltip]" 
                                placeholder="<?php _e('np. Szczegółowa analiza parametrów wody w akwarium', 'serwis-natu'); ?>" rows="3"></textarea>
                        </div>
                        <div class="service-field actions">
                            <button type="button" class="button button-secondary remove-service">
                                <?php _e('Usuń', 'serwis-natu'); ?>
                            </button>
                        </div>
                    </div>
                `;
                $('.extra-services-actions').before(template);
            });
            
            // Remove extra service
            $(document).on('click', '.remove-service', function() {
                $(this).closest('.extra-service-row').remove();
                // Renumber indices
                $('.extra-service-row h3').each(function(i) {
                    $(this).text('<?php _e('Usługa Dodatkowa', 'serwis-natu'); ?> #' + (i + 1));
                });
            });
        });
        </script>
        
        <style>
        .extra-service-row {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .extra-service-row h3 {
            margin-top: 0;
            margin-bottom: 15px;
        }
        .service-field {
            margin-bottom: 10px;
        }
        .service-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .service-field input[type="text"],
        .service-field input[type="number"],
        .service-field textarea {
            width: 100%;
            max-width: 400px;
        }
        .service-field.actions {
            margin-top: 15px;
        }
        .extra-services-actions {
            margin: 20px 0;
        }
        </style>
        <?php
    }

    /**
     * Render section description
     */
    public function render_section_description() {
        ?>
        <p><?php _e('Zarządzaj dodatkowymi usługami, które klienci mogą wybrać podczas konfiguracji pakietu serwisowego.', 'serwis-natu'); ?></p>
        <p><?php _e('Każda usługa ma cenę, która zostanie dodana do kosztu pakietu, jeśli klient wybierze tę usługę.', 'serwis-natu'); ?></p>
        <?php
    }

    /**
     * Add extra services fields
     */
    private function add_extra_services_fields() {
        $extra_services = get_option($this->option_name, array());
        
        if (empty($extra_services)) {
            // Add a default empty field
            add_settings_field(
                'empty_extra_services',
                '',
                array($this, 'render_empty_services_message'),
                $this->page_slug,
                'extra_services_section'
            );
        } else {
            // Add existing services
            foreach ($extra_services as $index => $service) {
                add_settings_field(
                    "extra_service_{$index}",
                    '',
                    array($this, 'render_extra_service_field'),
                    $this->page_slug,
                    'extra_services_section',
                    array(
                        'index' => $index,
                        'service' => $service,
                    )
                );
            }
        }
    }

    /**
     * Render empty services message
     */
    public function render_empty_services_message() {
        ?>
        <p><?php _e('Nie dodano jeszcze żadnych usług dodatkowych. Kliknij przycisk "Dodaj nową usługę" poniżej.', 'serwis-natu'); ?></p>
        <?php
    }

    /**
     * Render extra service field
     *
     * @param array $args Field arguments
     */
    public function render_extra_service_field($args) {
        $index = $args['index'];
        $service = $args['service'];
        ?>
        <div class="extra-service-row">
            <h3><?php _e('Usługa Dodatkowa', 'serwis-natu'); ?> #<?php echo esc_html($index + 1); ?></h3>
            <div class="service-field">
                <label><?php _e('Identyfikator:', 'serwis-natu'); ?></label>
                <input type="text" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($index); ?>][id]" 
                    value="<?php echo esc_attr($service['id'] ?? ''); ?>" 
                    placeholder="<?php _e('np. badanie_wody', 'serwis-natu'); ?>" required>
            </div>
            <div class="service-field">
                <label><?php _e('Nazwa usługi:', 'serwis-natu'); ?></label>
                <input type="text" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($index); ?>][name]" 
                    value="<?php echo esc_attr($service['name'] ?? ''); ?>" 
                    placeholder="<?php _e('np. Badanie wody', 'serwis-natu'); ?>" required>
            </div>
            <div class="service-field">
                <label><?php _e('Cena (zł):', 'serwis-natu'); ?></label>
                <input type="number" name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($index); ?>][price]" 
                    value="<?php echo esc_attr($service['price'] ?? '0'); ?>" 
                    min="0" step="1" placeholder="50" required>
            </div>
            <div class="service-field">
                <label><?php _e('Opis (tooltip):', 'serwis-natu'); ?></label>
                <textarea name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($index); ?>][tooltip]" 
                    placeholder="<?php _e('np. Szczegółowa analiza parametrów wody w akwarium', 'serwis-natu'); ?>" 
                    rows="3"><?php echo esc_textarea($service['tooltip'] ?? ''); ?></textarea>
            </div>
            <div class="service-field actions">
                <button type="button" class="button button-secondary remove-service">
                    <?php _e('Usuń', 'serwis-natu'); ?>
                </button>
            </div>
        </div>
        <?php
    }

    /**
     * Sanitize extra services data
     *
     * @param array $input The input to sanitize
     * @return array Sanitized input
     */
    public function sanitize_extra_services($input) {
        $sanitized = array();
        
        if (is_array($input)) {
            $index = 0;
            foreach ($input as $service) {
                if (!empty($service['id']) && !empty($service['name'])) {
                    $sanitized[$index] = array(
                        'id' => sanitize_text_field($service['id']),
                        'name' => sanitize_text_field($service['name']),
                        'price' => absint($service['price']),
                        'tooltip' => sanitize_textarea_field($service['tooltip'] ?? ''),
                    );
                    $index++;
                }
            }
        }
        
        return $sanitized;
    }

    /**
     * Get all extra services
     *
     * @return array All extra services
     */
    public static function get_extra_services() {
        return get_option('serwis_natu_extra_services_data', array());
    }
}
