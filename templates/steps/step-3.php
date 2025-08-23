<?php
/**
 * Step 3 of the Serwis Natu form
 *
 * @package Serwis_Natu
 */

defined('ABSPATH') || exit;
?>

<div class="step-content krok-3">
    <h2><?php _e('Dopasowany pakiet i wstępna wycena', 'serwis-natu'); ?></h2>
    <p class="step-description">
        <?php _e('Na podstawie podanych informacji dopasowaliśmy pakiety serwisowe indywidualnie dla każdego zgłoszonego akwarium.', 'serwis-natu'); ?>
    </p>

    <div id="recommended-packages">
        <div class="form-section">
          
            <div id="package-recommendations">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <?php _e('Przygotowujemy rekomendacje...', 'serwis-natu'); ?>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><?php _e('Lista rekomendowanych produktów', 'serwis-natu'); ?></h3>
            <div class="form-field checkbox-field">
                <label for="recommended_products_checkbox">
                    <input type="checkbox" id="recommended_products_checkbox" name="recommended_products" value="1">
                    <?php _e('Proszę o przygotowanie listy rekomendowanych produktów do mojego akwarium', 'serwis-natu'); ?>
                </label>
              
            </div>
            
            <div id="recommended-products-container" style="display: none;">
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <?php _e('Przygotowujemy rekomendacje produktów...', 'serwis-natu'); ?>
                </div>
                <div id="recommended-products-list"></div>
            </div>
        </div>
    </div>
    
    <div class="form-navigation">
        <button type="button" id="prev-step-3" class="prev-step sa-button"><?php _e('Wstecz', 'serwis-natu'); ?></button>
        <button type="button" id="next-step-3" class="next-step sa-button"><?php _e('Przejdź dalej', 'serwis-natu'); ?></button>
    </div>
</div>
