<?php
/**
 * Step 2 of the Serwis Natu form
 *
 * @package Serwis_Natu
 */

defined('ABSPATH') || exit;
?>

<div class="step-content krok-2">
    <h2><?php _e('Dane kontaktowe i lokalizacja', 'serwis-natu'); ?></h2>
    <p class="step-description">
        <?php _e('Podaj swoje dane kontaktowe oraz adres, pod którym znajduje się akwarium.', 'serwis-natu'); ?>
    </p>

    <div class="form-section">
        <h3><?php _e('Dane kontaktowe', 'serwis-natu'); ?></h3>
        <div class="form-row two-columns">
            <div class="form-field">
                <label for="client_first_name"><?php _e('Imię', 'serwis-natu'); ?><span class="required">*</span></label>
                <input type="text" id="client_first_name" name="client_first_name" required>
            </div>
            
            <div class="form-field">
                <label for="client_last_name"><?php _e('Nazwisko', 'serwis-natu'); ?><span class="required">*</span></label>
                <input type="text" id="client_last_name" name="client_last_name" required>
            </div>
        </div>
        
        <div class="form-row two-columns">
            <div class="form-field">
                <label for="client_email"><?php _e('Email', 'serwis-natu'); ?><span class="required">*</span></label>
                <input type="email" id="client_email" name="client_email" required>
            </div>
            
            <div class="form-field">
                <label for="client_phone"><?php _e('Telefon', 'serwis-natu'); ?><span class="required">*</span></label>
                <input type="tel" id="client_phone" name="client_phone" required>
            </div>
        </div>
    </div>
    
    <div class="form-section">
        <h3><?php _e('Adres i lokalizacja akwarium', 'serwis-natu'); ?></h3>
        
        <div class="form-row two-columns">
            <div class="form-field">
                <label for="aquarium_address"><?php _e('Adres', 'serwis-natu'); ?><span class="required">*</span></label>
                <input type="text" id="aquarium_address" name="aquarium_address" required>
            </div>
      
            <div class="form-field">
                <label for="preferred_date"><?php _e('Preferowany termin', 'serwis-natu'); ?><span class="required">*</span></label>
                <input type="datetime-local" id="preferred_date" name="preferred_date" required>
            </div>
        </div>
    </div>
    
    <div class="form-section">
        <h3><?php _e('Zdjęcie akwarium (opcjonalne)', 'serwis-natu'); ?></h3>
        <p class="section-description">
            <?php _e('Możesz załączyć zdjęcie swojego akwarium, co pomoże nam lepiej ocenić zakres prac.', 'serwis-natu'); ?>
        </p>
        
        <div class="aquarium-photos-container">
            <div class="form-row photo-upload-row" id="photo-upload-1">
                <div class="form-field">
                    <label for="aquarium_photo_1"><?php _e('Zdjęcie akwarium 1', 'serwis-natu'); ?></label>
                    <input type="file" id="aquarium_photo_1" name="aquarium_photo_1" accept="image/*" class="file-upload">
                    <div class="file-preview"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="form-section">
        <h3><?php _e('Dodatkowe uwagi (opcjonalne)', 'serwis-natu'); ?></h3>
        <div class="form-row">
            <div class="form-field">
                <textarea id="additional_notes" name="additional_notes" rows="4"></textarea>
            </div>
        </div>
    </div>
    
    <div class="error-message" id="step2-error" style="display: none;">
        <?php _e('Proszę wypełnić wszystkie wymagane pola.', 'serwis-natu'); ?>
    </div>
    
    <div class="form-navigation">
        <button type="button" id="prev-step-2" class="prev-step sa-button"><?php _e('Wstecz', 'serwis-natu'); ?></button>
        <button type="button" id="next-step-2" class="next-step sa-button"><?php _e('Przejdź dalej', 'serwis-natu'); ?></button>
    </div>
</div>
