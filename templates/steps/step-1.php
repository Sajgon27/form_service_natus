<?php

/**
 * Step 1 of the Serwis Natu form
 *
 * @package Serwis_Natu
 */

defined('ABSPATH') || exit;
?>

<div class="step-content krok-1">
    <h2><?php _e('Formularz umawiania wizyty', 'serwis-natu'); ?></h2>
    <p class="step-description">
        <?php _e('Wybierz interesujące Cię usługi. Na podstawie Twoich wyborów zaproponujemy najlepszy pakiet usług, dopasowany do Twoich potrzeb.', 'serwis-natu'); ?>
    </p>

    <!-- Tryb współpracy -->
    <div class="form-section">
        <h3><?php _e('Tryb współpracy', 'serwis-natu'); ?></h3>
        <div class="radio-options">
            <div class="radio-option">
                <label class="sa-radio-label" for="jednorazowa_usluga">
                    <input data-text="Jednorazowa usługa serwisowa" class="sa-radio" type="radio" id="jednorazowa_usluga" name="tryb_wspolpracy" value="jednorazowa" checked>

                    <span class="sa-radio-text"><?php _e('Jednorazowa usługa serwisowa', 'serwis-natu'); ?></span>
                    <span class="tooltip-icon" data-tooltip="jednorazowa_usluga">?</span>
                </label>
            </div>

            <div class="radio-option">
                <label class="sa-radio-label" for="pakiet_wielorazowy">
                    <input data-text="Pakiet wielorazowy (abonamentowy)" class="sa-radio" type="radio" id="pakiet_wielorazowy" name="tryb_wspolpracy" value="wielorazowy">

                    <span class="sa-radio-text"><?php _e('Pakiet wielorazowy (abonamentowy)', 'serwis-natu'); ?></span>
                    <span class="tooltip-icon" data-tooltip="pakiet_wielorazowy">?</span>
                </label>
            </div>

            <div class="radio-option">
                <label class="sa-radio-label" for="uslugi_dodatkowe">
                    <input data-text="Usługi dodatkowe" class="sa-radio" type="radio" id="uslugi_dodatkowe" name="tryb_wspolpracy" value="dodatkowe">

                    <span class="sa-radio-text"><?php _e('Usługi dodatkowe', 'serwis-natu'); ?></span>
                    <span class="tooltip-icon" data-tooltip="uslugi_dodatkowe">?</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Ilość zgłaszanych akwariów -->
    <div class="form-section aquariums-count-section">
        <h3><?php _e('Ilość zgłaszanych akwariów', 'serwis-natu'); ?></h3>
        <div class="radio-options">
            <div class="radio-option">
                <label class="sa-radio-label" for="ilosc_1">
                    <input class="sa-radio" type="radio" id="ilosc_1" name="ilosc_akwarium" value="1" checked>
                    <span class="sa-radio-text"><?php _e('1 akwarium', 'serwis-natu'); ?> </span>
                </label>
            </div>

            <div class="radio-option">
                <label class="sa-radio-label" for="ilosc_2">
                    <input class="sa-radio" type="radio" id="ilosc_2" name="ilosc_akwarium" value="2">
                    <span class="sa-radio-text"><?php _e('2 akwaria', 'serwis-natu'); ?></span>
                </label>
            </div>

            <div class="radio-option">
                <label class="sa-radio-label" for="ilosc_wiecej">
                    <input class="sa-radio" type="radio" id="ilosc_wiecej" name="ilosc_akwarium" value="wiecej">
                    <span class="sa-radio-text"> <?php _e('Więcej', 'serwis-natu'); ?></span></label>
                <div id="wiecej_container" class="hidden">
                    <input type="number" id="ilosc_wiecej_input" name="ilosc_wiecej_input" min="3" max="10" value="3">
                </div>
            </div>
        </div>
    </div>

    <div class="aquariums-container">
        <div id="akwarium-1" class="akwarium-section">
            <h3><?php _e('Akwarium', 'serwis-natu'); ?> 1</h3>
            <?php include SERWIS_NATU_PATH . 'templates/partials/aquarium-checklist.php'; ?>
        </div>
    </div>

    <div class="error-message" id="step1-error" style="display: none;">
        <?php _e('Proszę wybrać przynajmniej jedną opcję dla każdego akwarium.', 'serwis-natu'); ?>
    </div>

    <div class="form-navigation">
         <button type="button" id="clear-serwis" class="sa-button"><?php _e('Wyczyść', 'serwis-natu'); ?></button>
        <button type="button" id="next-step-1" class="next-step sa-button"><?php _e('Przejdź dalej', 'serwis-natu'); ?></button>
    </div>
</div>