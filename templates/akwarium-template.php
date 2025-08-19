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
        
        <!-- Typ zbiornika -->
        <div class="subsection">
            <h4><?php _e('Typ zbiornika', 'serwis-natu'); ?></h4>
            <div class="checkbox-options">
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_lowtech" name="akw[1][typ][]" value="lowtech">
                    <label for="akw1_lowtech">
                        <?php _e('Low-tech (bez CO<sub>2</sub>, bez intensywnego światła)', 'serwis-natu'); ?>
                        <span class="tooltip-icon" data-tooltip="lowtech">?</span>
                    </label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_hightech" name="akw[1][typ][]" value="hightech">
                    <label for="akw1_hightech">
                        <?php _e('High-tech (z CO<sub>2</sub> nawożenie, intensywne oświetlenie)', 'serwis-natu'); ?>
                        <span class="tooltip-icon" data-tooltip="hightech">?</span>
                    </label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_biotopowy" name="akw[1][typ][]" value="biotopowy">
                    <label for="akw1_biotopowy">
                        <?php _e('Biotopowy (np. Amazonka, Tanganika)', 'serwis-natu'); ?>
                        <span class="tooltip-icon" data-tooltip="biotopowy">?</span>
                    </label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_roslinny" name="akw[1][typ][]" value="roslinny">
                    <label for="akw1_roslinny"><?php _e('Roślinny', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_dekoracyjny" name="akw[1][typ][]" value="dekoracyjny">
                    <label for="akw1_dekoracyjny"><?php _e('Dekoracyjny / Ekspozycyjny', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_firmowe" name="akw[1][typ][]" value="firmowe">
                    <label for="akw1_firmowe"><?php _e('Akwarium firmowe / usługowe', 'serwis-natu'); ?></label>
                </div>
            </div>
        </div>
        
        <!-- Cel zgłoszenia -->
        <div class="subsection">
            <h4><?php _e('Cel zgłoszenia', 'serwis-natu'); ?></h4>
            <div class="checkbox-options">
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_regularna" name="akw[1][cel][]" value="regularna">
                    <label for="akw1_regularna"><?php _e('Regularna pielęgnacja akwarium', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_czyszczenie" name="akw[1][cel][]" value="czyszczenie">
                    <label for="akw1_czyszczenie"><?php _e('Gruntowne czyszczenie i porządki', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_restart" name="akw[1][cel][]" value="restart">
                    <label for="akw1_restart"><?php _e('Restart zbiornika', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_dorazna" name="akw[1][cel][]" value="dorazna">
                    <label for="akw1_dorazna"><?php _e('Pomoc doraźna (awaria / pogorszenie stanu zbiornika)', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_aranzacja" name="akw[1][cel][]" value="aranzacja">
                    <label for="akw1_aranzacja"><?php _e('Zmiana aranżacji', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_montaz" name="akw[1][cel][]" value="montaz">
                    <label for="akw1_montaz"><?php _e('Montaż lub konfiguracja sprzętu', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_zakladanie" name="akw[1][cel][]" value="zakladanie">
                    <label for="akw1_zakladanie"><?php _e('Zakładanie akwarium od zera', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_ratowanie" name="akw[1][cel][]" value="ratowanie">
                    <label for="akw1_ratowanie"><?php _e('Ratowanie akwarium (glony, choroby, zatrucia)', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_ocena" name="akw[1][cel][]" value="ocena">
                    <label for="akw1_ocena"><?php _e('Ocena kondycji zbiornika', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_naglasyt" name="akw[1][cel][]" value="naglasyt">
                    <label for="akw1_naglasyt"><?php _e('Pogotowie akwarystyczne (nagła sytuacja)', 'serwis-natu'); ?></label>
                </div>
            </div>
        </div>
        
        <!-- Zakres oczekiwanych działań -->
        <div class="subsection">
            <h4><?php _e('Zakres oczekiwanych działań', 'serwis-natu'); ?></h4>
            <div class="checkbox-options">
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_wymiana" name="akw[1][zakres][]" value="wymiana">
                    <label for="akw1_wymiana"><?php _e('Podmiana i/lub dolanie wody', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_odmulanie" name="akw[1][zakres][]" value="odmulanie">
                    <label for="akw1_odmulanie"><?php _e('Odmulanie dna', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_czyszczenieSzyb" name="akw[1][zakres][]" value="czyszczenieSzyb">
                    <label for="akw1_czyszczenieSzyb"><?php _e('Czyszczenie szyb i dekoracji', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_przycinanie" name="akw[1][zakres][]" value="przycinanie">
                    <label for="akw1_przycinanie"><?php _e('Przycinanie/obsadzanie roślin', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_filtr" name="akw[1][zakres][]" value="filtr">
                    <label for="akw1_filtr"><?php _e('Czyszczenie/konserwacja filtra', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_nawozenie" name="akw[1][zakres][]" value="nawozenie">
                    <label for="akw1_nawozenie"><?php _e('Sprawdzenie i korekta nawożenia', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_badanie" name="akw[1][zakres][]" value="badanie">
                    <label for="akw1_badanie"><?php _e('Badanie parametrów wody', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_glony" name="akw[1][zakres][]" value="glony">
                    <label for="akw1_glony"><?php _e('Likwidacja glonów', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_przeglad" name="akw[1][zakres][]" value="przeglad">
                    <label for="akw1_przeglad"><?php _e('Przegląd techniczny sprzętu', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_montazSprzetu" name="akw[1][zakres][]" value="montazSprzetu">
                    <label for="akw1_montazSprzetu"><?php _e('Montaż nowego sprzętu (filtr, CO<sub>2</sub>, oświetlenie)', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_ocenaObsady" name="akw[1][zakres][]" value="ocenaObsady">
                    <label for="akw1_ocenaObsady"><?php _e('Ocena stanu obsady (ryby, krewetki itp.)', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_szkolenie" name="akw[1][zakres][]" value="szkolenie">
                    <label for="akw1_szkolenie"><?php _e('Szkolenie / instruktaż użytkowania zbiornika', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_aranzacjaDekoracji" name="akw[1][zakres][]" value="aranzacjaDekoracji">
                    <label for="akw1_aranzacjaDekoracji"><?php _e('Zmiana aranżacji/dekoracji', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_aplikacja" name="akw[1][zakres][]" value="aplikacja">
                    <label for="akw1_aplikacja"><?php _e('Aplikacja preparatów (bakterie, uzdatniacze, nawozy)', 'serwis-natu'); ?></label>
                </div>
            </div>
        </div>
        
        <!-- Inne potrzeby -->
        <div class="subsection inne-potrzeby">
            <h4><?php _e('Inne potrzeby', 'serwis-natu'); ?></h4>
            <div class="checkbox-options">
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_jednorazowa" name="akw[1][inne][]" value="jednorazowa">
                    <label for="akw1_jednorazowa"><?php _e('Potrzebuję tylko jednorazowej pomocy', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_stala" name="akw[1][inne][]" value="stala">
                    <label for="akw1_stala"><?php _e('Jestem zainteresowany stałą opieką (pakiet wielorazowy)', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_kompleksowo" name="akw[1][inne][]" value="kompleksowo">
                    <label for="akw1_kompleksowo"><?php _e('Chcę, by ktoś kompleksowo zajął się moim zbiornikiem', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_okazja" name="akw[1][inne][]" value="okazja">
                    <label for="akw1_okazja"><?php _e('Chcę przygotować akwarium na konkretną okazję', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_wakacje" name="akw[1][inne][]" value="wakacje">
                    <label for="akw1_wakacje"><?php _e('Wyjeżdżam na wakacje, potrzebuję na ten czas obsługi akwarium', 'serwis-natu'); ?></label>
                </div>
                
                <div class="checkbox-option">
                    <input type="checkbox" id="akw1_diagnoza" name="akw[1][inne][]" value="diagnoza">
                    <label for="akw1_diagnoza"><?php _e('Mam problem - nie wiem, co się dzieje, potrzebuję diagnozy', 'serwis-natu'); ?></label>
                </div>
            </div>
        </div>
    </div>
</div>
