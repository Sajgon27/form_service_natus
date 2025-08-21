<?php

/**
 * Main form container template
 *
 * @package Serwis_Natu
 */

defined('ABSPATH') || exit;
?>

<div class="serwis-natu-form-container">
    <form id="serwis-natu-form" method="post">

        <div class="sa-progress-bar">
            <div class="sa-progress-item active" data-step="1">
                <div class="sa-progress-label">Wybierz potrzeby<br> Twojego akwarium</div>
                <div class="sa-progress"></div>
            </div>
            <div class="sa-progress-line"></div>
            <div class="sa-progress-item" data-step="2">
                <div class="sa-progress-label">Dane kontaktowe i<br> lokalizacja</div>
                <div class="sa-progress"></div>
            </div>
            <div class="sa-progress-line"></div>
            <div class="sa-progress-item" data-step="3">
                <div class="sa-progress-label">Dopasowany pakiet i<br> wstępna wycena</div>
                <div class="sa-progress"></div>
            </div>
            <div class="sa-progress-line"></div>
            <div class="sa-progress-item" data-step="4">
                <div class="sa-progress-label">Podsumowanie i<br> wysyłka zgłoszenia</div>
                <div class="sa-progress"></div>
            </div>
        </div>


        <div class="serwis-natu-steps-container">
            <!-- Step 1 -->
            <div class="serwis-natu-step active" id="step-1">
                <?php include SERWIS_NATU_PATH . 'templates/steps/step-1.php'; ?>
            </div>

            <!-- Step 2 -->
            <div class="serwis-natu-step" id="step-2">
                <?php include SERWIS_NATU_PATH . 'templates/steps/step-2.php'; ?>
            </div>

            <!-- Step 3 -->
            <div class="serwis-natu-step" id="step-3">
                <?php include SERWIS_NATU_PATH . 'templates/steps/step-3.php'; ?>
            </div>

            <!-- Step 4 -->
            <div class="serwis-natu-step" id="step-4">
                <?php include SERWIS_NATU_PATH . 'templates/steps/step-4.php'; ?>
            </div>
        </div>
    </form>

    <!-- Include the hidden template for aquarium sections -->
    <?php include SERWIS_NATU_PATH . 'templates/akwarium-template.php'; ?>
</div>