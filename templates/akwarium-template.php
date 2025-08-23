<?php

/**
 * Aquarium template for cloning
 * This template is used as a source for dynamically generating aquarium sections
 *
 * @package Serwis_Natu
 */

defined('ABSPATH') || exit;
?>

<div id="template-akwarium" style="display: none;">
    <div id="akwarium-template" class="akwarium-section">
        <h3><?php _e('Akwarium', 'serwis-natu'); ?> 1</h3>
        <?php include SERWIS_NATU_PATH . 'templates/partials/aquarium-checklist.php'; ?>
    </div>
</div>