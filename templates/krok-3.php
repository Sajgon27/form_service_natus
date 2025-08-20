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
        <?php _e('Na podstawie podanych informacji dopasowaliśmy pakiet usług i przygotowaliśmy wstępną wycenę.', 'serwis-natu'); ?>
    </p>

    <!-- Recommended packages container -->
    <div id="recommended-packages">
        <div class="form-section">
            <h3><?php _e('Rekomendowane pakiety', 'serwis-natu'); ?></h3>
            <p class="section-description">
                <?php _e('Dopasowaliśmy pakiety serwisowe indywidualnie dla każdego zgłoszonego akwarium:', 'serwis-natu'); ?>
            </p>
            
            <div id="package-recommendations">
                <!-- Package recommendations will be loaded here by JavaScript -->
                <div class="loading-spinner">
                    <div class="spinner"></div>
                    <?php _e('Przygotowujemy rekomendacje...', 'serwis-natu'); ?>
                </div>
            </div>
        </div>
        
        <div class="form-section">
            <h3><?php _e('Podsumowanie kosztów', 'serwis-natu'); ?></h3>
            <div id="cost-summary" class="cost-summary">
                <!-- Cost summary will be loaded here by JavaScript -->
            </div>
        </div>
        
        <div class="form-section">
            <h3><?php _e('Uwagi do wyceny', 'serwis-natu'); ?></h3>
            <p>
                <?php _e('Powyższa wycena jest przybliżona i może ulec zmianie po dokładnej analizie stanu akwarium. Ostateczna cena zostanie ustalona po bezpłatnej konsultacji.', 'serwis-natu'); ?>
            </p>
            <p>
                <?php _e('Koszt dojazdu oraz dodatkowych materiałów eksploatacyjnych nie jest wliczony w cenę.', 'serwis-natu'); ?>
            </p>
        </div>
        
        <div class="form-section">
            <h3><?php _e('Lista rekomendowanych produktów', 'serwis-natu'); ?></h3>
            <div class="form-field checkbox-field">
                <label for="recommended_products_checkbox">
                    <input type="checkbox" id="recommended_products_checkbox" name="recommended_products" value="1">
                    <?php _e('Proszę o przygotowanie listy rekomendowanych produktów do mojego akwarium', 'serwis-natu'); ?>
                </label>
                <p class="field-description">
                    <?php _e('Zaznacz tę opcję, jeśli chcesz otrzymać listę produktów polecanych do Twojego akwarium (np. nawozy, media filtracyjne, preparaty).', 'serwis-natu'); ?>
                </p>
            </div>
            
            <!-- Recommended products container will be populated by JavaScript -->
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
        <button type="button" id="prev-step-3" class="prev-step"><?php _e('Wstecz', 'serwis-natu'); ?></button>
        <button type="button" id="next-step-3" class="next-step"><?php _e('Przejdź dalej', 'serwis-natu'); ?></button>
    </div>
</div>
