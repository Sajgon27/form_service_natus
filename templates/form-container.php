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
        <div class="serwis-natu-progress-bar">
            <div class="progress-step active" data-step="1"><?php _e('Wybierz potrzeby Twojego akwarium', 'serwis-natu'); ?></div>
            <div class="progress-step" data-step="2"><?php _e('Dane kontaktowe i lokalizacja', 'serwis-natu'); ?></div>
            <div class="progress-step" data-step="3"><?php _e('Dopasowany pakiet i wstępna wycena', 'serwis-natu'); ?></div>
            <div class="progress-step" data-step="4"><?php _e('Podsumowanie i wysyłka zgłoszenia', 'serwis-natu'); ?></div>
        </div>
        
        <div class="serwis-natu-steps-container">
            <!-- Step 1 -->
            <div class="serwis-natu-step active" id="step-1">
                <?php include SERWIS_NATU_PATH . 'templates/krok-1.php'; ?>
            </div>
            
            <!-- Step 2 -->
            <div class="serwis-natu-step" id="step-2">
                <?php include SERWIS_NATU_PATH . 'templates/krok-2.php'; ?>
            </div>
            
            <!-- Step 3 -->
            <div class="serwis-natu-step" id="step-3">
                <?php include SERWIS_NATU_PATH . 'templates/krok-3.php'; ?>
            </div>
            
            <!-- Step 4 -->
            <div class="serwis-natu-step" id="step-4">
                <?php include SERWIS_NATU_PATH . 'templates/krok-4.php'; ?>
            </div>
        </div>
    </form>
    
    <!-- Include the hidden template for aquarium sections -->
    <?php include SERWIS_NATU_PATH . 'templates/akwarium-template.php'; ?>
</div>
