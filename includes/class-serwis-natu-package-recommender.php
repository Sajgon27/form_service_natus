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
        require_once SERWIS_NATU_PATH . 'admin/class-serwis-natu-admin.php';
        
        // Debug the form data for this aquarium
        if (isset($form_data['akw'][$aquarium_index])) {
            error_log('Aquarium ' . $aquarium_index . ' data: ' . print_r($form_data['akw'][$aquarium_index], true));
        } else {
            error_log('No data for aquarium ' . $aquarium_index);
        }
        
        // Convert service type to package type
        $package_type = 'one_time';
        if ($service_type === 'pakiet') {
            $package_type = 'subscription';
        }
        
        // Default packages
        $all_packages = Serwis_Natu_Admin::get_all_packages();
        $default_key = ($package_type === 'one_time') ? 'basic' : 'monthly_basic';
        $default_package = array(
            'key' => $default_key,
            'name' => $all_packages[$package_type][$default_key]['name'],
            'price' => $all_packages[$package_type][$default_key]['price'],
        );
        
        // If "Usługi dodatkowe" service type, always return consultation package
        if ($service_type === 'dodatkowe') {
            return array(
                'key' => 'consultation',
                'name' => $all_packages['one_time']['consultation']['name'],
                'price' => $all_packages['one_time']['consultation']['price'],
            );
        }
        
        // Get aquarium data
        if (!isset($form_data['akw'][$aquarium_index])) {
            return $default_package;
        }
        
        $aquarium_data = $form_data['akw'][$aquarium_index];
        $package_mappings = Serwis_Natu_Admin::get_package_mappings();
        
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
        if (empty($selected_package_key)) {
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
