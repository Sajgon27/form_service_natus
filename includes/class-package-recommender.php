<?php
/**
 * Package recommendation class for Serwis Natu
 *
 * @package Serwis_Natu
 */

if (!defined('ABSPATH')) {
    exit; // Blokowanie bezpośredniego dostępu do pliku
}

/**
 * Package recommendation class
 */
class Serwis_Natu_Package_Recommender {
    /**
     * Get recommended packages for all aquariums
     *
     * @param array $form_data Form data from step 1
     * @return array Recommended packages for each aquarium
     */
    public static function get_recommendations($form_data) {
        $recommendations = array();
        $service_type = isset($form_data['tryb_wspolpracy']) ? $form_data['tryb_wspolpracy'] : 'jednorazowa';
        
        // Debug service type specifically
        error_log('Service type from form: ' . $service_type);
        
        // Debug form data
        error_log('Form data: ' . print_r($form_data, true));
        
        // Count aquariums
        $aquarium_count = 1;
        if (isset($form_data['ilosc_akwarium'])) {
            if ($form_data['ilosc_akwarium'] === '1') {
                $aquarium_count = 1;
            } elseif ($form_data['ilosc_akwarium'] === '2') {
                $aquarium_count = 2;
            } elseif ($form_data['ilosc_akwarium'] === 'wiecej') {
                $aquarium_count = isset($form_data['ilosc_wiecej']) ? intval($form_data['ilosc_wiecej']) : 3;
                $aquarium_count = max(3, min(10, $aquarium_count)); // Ensure between 3 and 10
            }
        }
        
        // Get recommendations for each aquarium and format them as expected by the JS
        $formatted_recommendations = array();
        
        for ($i = 1; $i <= $aquarium_count; $i++) {
            $package = self::get_package_for_aquarium($form_data, $service_type, $i);
            
            // Format the package data as expected by the JavaScript
            $formatted_recommendations[] = array(
                'aquariumIndex' => $i,
                'packageKey' => $package['key'],
                'packageName' => $package['name'],
                'packagePrice' => $package['price'],
                'packageDescription' => isset($package['description']) ? $package['description'] : 'Usługa dopasowana do potrzeb Twojego akwarium.'
            );
        }
        
        return $formatted_recommendations;
    }
    
    /**
     * Get package for a single aquarium
     *
     * @param array $form_data Form data from step 1
     * @param string $service_type Service type ('jednorazowa', 'pakiet', or 'dodatkowe')
     * @param int $aquarium_index Aquarium index
     * @return array Package data
     */
    private static function get_package_for_aquarium($form_data, $service_type, $aquarium_index) {
        // Get package mappings from admin settings
        require_once SERWIS_NATU_PATH . 'admin/class-admin.php';
        
        // Debug the form data for this aquarium
        if (isset($form_data['akw'][$aquarium_index])) {
            error_log('Aquarium ' . $aquarium_index . ' data: ' . print_r($form_data['akw'][$aquarium_index], true));
        } else {
            error_log('No data for aquarium ' . $aquarium_index);
        }
        
        // Convert service type to package type
        $package_type = 'one_time';
        if ($service_type === 'pakiet' || $service_type === 'wielorazowy' || $service_type === 'cykliczna') {
            $package_type = 'subscription';
            // Log this for debugging
            error_log('Using subscription package type for service type: ' . $service_type);
        }
        
        // Get all available packages
        $all_packages = Serwis_Natu_Admin::get_all_packages();
        
        // Find default package (first package in the appropriate category)
        if (empty($all_packages[$package_type])) {
            // If no packages are available in this category
            return array(
                'key' => '',
                'name' => __('Brak skonfigurowanego pakietu', 'serwis-natu'),
                'price' => 0,
            );
        }
        
        $package_keys = array_keys($all_packages[$package_type]);
        $default_key = reset($package_keys);
        $default_package = array(
            'key' => $default_key,
            'name' => $all_packages[$package_type][$default_key]['name'],
            'price' => $all_packages[$package_type][$default_key]['price'],
        );
        
        // If "Usługi dodatkowe" service type, ALWAYS use the "additional_services" package
        if ($service_type === 'dodatkowe') {
            // Create a new instance of the packages admin class to get additional services
            require_once SERWIS_NATU_PATH . 'admin/admin-pakiety.php';
            $packages_admin = new Serwis_Natu_Admin_Packages();
            
            // Get additional services
            $additional_services = $packages_admin->get_additional_services();
            
            // Check if we have the additional_services key
            if (isset($additional_services['additional_services'])) {
                return array(
                    'key' => 'additional_services',
                    'name' => $additional_services['additional_services']['name'],
                    'price' => $additional_services['additional_services']['price'],
                );
            }
            
            // If for some reason the additional_services key doesn't exist,
            // get the first available additional service
            if (!empty($additional_services)) {
                $first_key = array_key_first($additional_services);
                return array(
                    'key' => $first_key,
                    'name' => $additional_services[$first_key]['name'],
                    'price' => $additional_services[$first_key]['price'],
                );
            }
            
            // As a last resort, if no additional services are configured, use default package
            return $default_package;
        }
        
        // Get aquarium data
        if (!isset($form_data['akw'][$aquarium_index])) {
            return $default_package;
        }
        
        $aquarium_data = $form_data['akw'][$aquarium_index];
        $package_mappings = Serwis_Natu_Admin::get_package_mappings();
        
        // Build package priorities dynamically based on available packages
        $package_priorities = array();
        $priority = 1;
        
        if (isset($all_packages['one_time'])) {
            $package_priorities['one_time'] = array();
            foreach (array_keys($all_packages['one_time']) as $key) {
                $package_priorities['one_time'][$key] = $priority++;
            }
        }
        
        $priority = 1;
        if (isset($all_packages['subscription'])) {
            $package_priorities['subscription'] = array();
            foreach (array_keys($all_packages['subscription']) as $key) {
                $package_priorities['subscription'][$key] = $priority++;
            }
        }
        
        // Find highest priority package for this aquarium
        $highest_priority = 0;
        $selected_package_key = '';
        
        // Check each category of selections
        foreach ($aquarium_data as $category => $selections) {
            if (!is_array($selections)) {
                continue;
            }
            
            // Check each selection in this category
            foreach ($selections as $selection) {
                // Skip if no mapping exists for this selection
                if (!isset($package_mappings[$selection][$package_type]) || empty($package_mappings[$selection][$package_type])) {
                    continue;
                }
                
                $mapped_package_key = $package_mappings[$selection][$package_type];
                $mapped_priority = $package_priorities[$package_type][$mapped_package_key] ?? 0;
                
                // If this mapping has higher priority than current, update
                if ($mapped_priority > $highest_priority) {
                    $highest_priority = $mapped_priority;
                    $selected_package_key = $mapped_package_key;
                }
            }
        }
        
        // If no package was selected, use default
        if (empty($selected_package_key) || !isset($all_packages[$package_type][$selected_package_key])) {
            return $default_package;
        }
        
        // Return the selected package
        return array(
            'key' => $selected_package_key,
            'name' => $all_packages[$package_type][$selected_package_key]['name'],
            'price' => $all_packages[$package_type][$selected_package_key]['price'],
        );
    }
}
