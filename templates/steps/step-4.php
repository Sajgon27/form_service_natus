<?php
/**
 * Step 4 of the Serwis Natu form - Summary and confirmation
 *
 * @package Serwis_Natu
 */

defined('ABSPATH') || exit;
?>

<div class="step-content krok-4">
    <h2><?php _e('Podsumowanie i potwierdzenie zamówienia', 'serwis-natu'); ?></h2>
    <p class="step-description">
        <?php _e('Sprawdź poprawność danych przed zamówieniem usługi.', 'serwis-natu'); ?>
    </p>

    <div class="form-section">
        <h3><?php _e('Dane kontaktowe', 'serwis-natu'); ?></h3>
        <div id="contact-summary" class="contact-summary">
        </div>
    </div>

    <div class="form-section">
        <h3><?php _e('Podsumowanie akwariów', 'serwis-natu'); ?></h3>
        <div id="aquariums-summary" class="aquariums-summary">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <?php _e('Ładowanie danych...', 'serwis-natu'); ?>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3><?php _e('Podsumowanie kosztów', 'serwis-natu'); ?></h3>
        <div id="final-cost-summary" class="final-cost-summary">
        </div>
        <p class="cost-disclaimer">
            <?php _e('Uwaga: Ostateczna cena może się różnić po ocenie stanu akwarium przez serwisanta.', 'serwis-natu'); ?>
        </p>
    </div>

    <div class="form-section">
        <h3><?php _e('Instrukcja przygotowania do wizyty', 'serwis-natu'); ?></h3>
        <ol class="preparation-instructions">
            <li><?php _e('Zapewnij łatwy dostęp do akwarium z przodu i z góry.', 'serwis-natu'); ?></li>
            <li><?php _e('Odłącz urządzenia elektryczne, które mogą przeszkadzać (oświetlenie zewnętrzne, pokrywy).', 'serwis-natu'); ?></li>
            <li><?php _e('Przygotuj wiadra lub pojemniki na wodę (jeśli posiadasz).', 'serwis-natu'); ?></li>
            <li><?php _e('Jeśli możliwe, przygotuj wcześniej wodę do podmiany (odstać, uzdatnić).', 'serwis-natu'); ?></li>
            <li><?php _e('Zabezpiecz miejsce wokół akwarium przed możliwym zachlapaniem.', 'serwis-natu'); ?></li>
            <li><?php _e('Przygotuj listę pytań lub wątpliwości, które chcesz omówić z serwisantem.', 'serwis-natu'); ?></li>
        </ol>
    </div>

    <div class="form-section terms-section">
        <div id="step4-error" class="error-message" style="display: none;">
            <?php _e('Aby zamówić usługę, musisz zaakceptować politykę prywatności i regulamin.', 'serwis-natu'); ?>
        </div>
        
        <div class="form-field checkbox-field">
            <label for="privacy_policy">
                <input type="checkbox" id="privacy_policy" name="privacy_policy" value="1" required>
                <?php 
                    printf(
                        __('Zapoznałem/am się i akceptuję %spolitykę prywatności%s.', 'serwis-natu'),
                        '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">', 
                        '</a>'
                    ); 
                ?>
            </label>
        </div>
        <div class="form-field checkbox-field">
            <label for="terms_conditions">
                <input type="checkbox" id="terms_conditions" name="terms_conditions" value="1" required>
                <?php 
                    printf(
                        __('Zapoznałem/am się i akceptuję %sregulamin usługi%s.', 'serwis-natu'),
                        '<a href="#" target="_blank">', 
                        '</a>'
                    ); 
                ?>
            </label>
        </div>
    </div>
    
    <div class="form-navigation">
        <button type="button" id="prev-step-4" class="prev-step sa-button"><?php _e('Wróć', 'serwis-natu'); ?></button>
        <button type="button" id="submit-form" class="submit-form next-step sa-button"><?php _e('Zamów usługę', 'serwis-natu'); ?></button>
    </div>
</div>
