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
                <input type="radio" id="jednorazowa_usluga" name="tryb_wspolpracy" value="jednorazowa" checked>
                <label for="jednorazowa_usluga">
                    <?php _e('Jednorazowa usługa serwisowa', 'serwis-natu'); ?>
                    <span class="tooltip-icon" data-tooltip="jednorazowa_usluga">?</span>
                </label>
            </div>
            
            <div class="radio-option">
                <input type="radio" id="pakiet_wielorazowy" name="tryb_wspolpracy" value="wielorazowy">
                <label for="pakiet_wielorazowy">
                    <?php _e('Pakiet wielorazowy (abonamentowy)', 'serwis-natu'); ?>
                    <span class="tooltip-icon" data-tooltip="pakiet_wielorazowy">?</span>
                </label>
            </div>
            
            <div class="radio-option">
                <input type="radio" id="uslugi_dodatkowe" name="tryb_wspolpracy" value="dodatkowe">
                <label for="uslugi_dodatkowe">
                    <?php _e('Usługi dodatkowe', 'serwis-natu'); ?>
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
                <input type="radio" id="ilosc_1" name="ilosc_akwarium" value="1" checked>
                <label for="ilosc_1"><?php _e('1 akwarium', 'serwis-natu'); ?></label>
            </div>
            
            <div class="radio-option">
                <input type="radio" id="ilosc_2" name="ilosc_akwarium" value="2">
                <label for="ilosc_2"><?php _e('2 akwaria', 'serwis-natu'); ?></label>
            </div>
            
            <div class="radio-option">
                <input type="radio" id="ilosc_wiecej" name="ilosc_akwarium" value="wiecej">
                <label for="ilosc_wiecej"><?php _e('Więcej', 'serwis-natu'); ?></label>
                
                <div id="wiecej_container" class="hidden">
                    <label for="ilosc_wiecej_input"><?php _e('Podaj ilość (3-10):', 'serwis-natu'); ?></label>
                    <input type="number" id="ilosc_wiecej_input" name="ilosc_wiecej_input" min="3" max="10" value="3">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Dane akwariów -->
    <div class="aquariums-container">
        <!-- Dynamicznie generowane sekcje akwariów -->
        <div id="akwarium-1" class="akwarium-section">
            <h3><?php _e('Akwarium', 'serwis-natu'); ?> 1</h3>
            
            <!-- Typ zbiornika -->
            <div class="subsection">
                <h4><?php _e('Typ zbiornika', 'serwis-natu'); ?></h4>
                <div class="checkbox-options">
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_lowtech" name="akw[1][typ][]" value="lowtech" data-text="Low-tech (bez CO2, bez intensywnego światła)">
                        <label for="akw1_lowtech">
                            <?php _e('Low-tech (bez CO<sub>2</sub>, bez intensywnego światła)', 'serwis-natu'); ?>
                            <span class="tooltip-icon" data-tooltip="lowtech">?</span>
                        </label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_hightech" name="akw[1][typ][]" value="hightech" data-text="High-tech (z CO2 nawożenie, intensywne oświetlenie)">
                        <label for="akw1_hightech">
                            <?php _e('High-tech (z CO<sub>2</sub> nawożenie, intensywne oświetlenie)', 'serwis-natu'); ?>
                            <span class="tooltip-icon" data-tooltip="hightech">?</span>
                        </label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_biotopowy" name="akw[1][typ][]" value="biotopowy" data-text="Biotopowy (np. Amazonka, Tanganika)">
                        <label for="akw1_biotopowy">
                            <?php _e('Biotopowy (np. Amazonka, Tanganika)', 'serwis-natu'); ?>
                            <span class="tooltip-icon" data-tooltip="biotopowy">?</span>
                        </label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_roslinny" name="akw[1][typ][]" value="roslinny" data-text="Roślinny">
                        <label for="akw1_roslinny"><?php _e('Roślinny', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_dekoracyjny" name="akw[1][typ][]" value="dekoracyjny" data-text="Dekoracyjny / Ekspozycyjny">
                        <label for="akw1_dekoracyjny"><?php _e('Dekoracyjny / Ekspozycyjny', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_firmowe" name="akw[1][typ][]" value="firmowe" data-text="Akwarium firmowe / usługowe">
                        <label for="akw1_firmowe"><?php _e('Akwarium firmowe / usługowe', 'serwis-natu'); ?></label>
                    </div>
                </div>
            </div>
            
            <!-- Cel zgłoszenia -->
            <div class="subsection">
                <h4><?php _e('Cel zgłoszenia', 'serwis-natu'); ?></h4>
                <div class="checkbox-options">
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_regularna" name="akw[1][cel][]" value="regularna" data-text="Regularna pielęgnacja akwarium">
                        <label for="akw1_regularna"><?php _e('Regularna pielęgnacja akwarium', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_czyszczenie" name="akw[1][cel][]" value="czyszczenie" data-text="Gruntowne czyszczenie i porządki">
                        <label for="akw1_czyszczenie"><?php _e('Gruntowne czyszczenie i porządki', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_restart" name="akw[1][cel][]" value="restart" data-text="Restart zbiornika">
                        <label for="akw1_restart"><?php _e('Restart zbiornika', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_dorazna" name="akw[1][cel][]" value="dorazna" data-text="Pomoc doraźna (awaria / pogorszenie stanu zbiornika)">
                        <label for="akw1_dorazna"><?php _e('Pomoc doraźna (awaria / pogorszenie stanu zbiornika)', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_aranzacja" name="akw[1][cel][]" value="aranzacja" data-text="Zmiana aranżacji">
                        <label for="akw1_aranzacja"><?php _e('Zmiana aranżacji', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_montaz" name="akw[1][cel][]" value="montaz" data-text="Montaż lub konfiguracja sprzętu">
                        <label for="akw1_montaz"><?php _e('Montaż lub konfiguracja sprzętu', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_zakladanie" name="akw[1][cel][]" value="zakladanie" data-text="Zakładanie akwarium od zera">
                        <label for="akw1_zakladanie"><?php _e('Zakładanie akwarium od zera', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_ratowanie" name="akw[1][cel][]" value="ratowanie" data-text="Ratowanie akwarium (glony, choroby, zatrucia)">
                        <label for="akw1_ratowanie"><?php _e('Ratowanie akwarium (glony, choroby, zatrucia)', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_ocena" name="akw[1][cel][]" value="ocena" data-text="Ocena kondycji zbiornika">
                        <label for="akw1_ocena"><?php _e('Ocena kondycji zbiornika', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_naglasyt" name="akw[1][cel][]" value="naglasyt" data-text="Pogotowie akwarystyczne (nagła sytuacja)">
                        <label for="akw1_naglasyt"><?php _e('Pogotowie akwarystyczne (nagła sytuacja)', 'serwis-natu'); ?></label>
                    </div>
                </div>
            </div>
            
            <!-- Zakres oczekiwanych działań -->
            <div class="subsection">
                <h4><?php _e('Zakres oczekiwanych działań', 'serwis-natu'); ?></h4>
                <div class="checkbox-options">
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_wymiana" name="akw[1][zakres][]" value="wymiana" data-text="Podmiana i/lub dolanie wody">
                        <label for="akw1_wymiana"><?php _e('Podmiana i/lub dolanie wody', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_odmulanie" name="akw[1][zakres][]" value="odmulanie" data-text="Odmulanie dna">
                        <label for="akw1_odmulanie"><?php _e('Odmulanie dna', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_czyszczenieSzyb" name="akw[1][zakres][]" value="czyszczenieSzyb" data-text="Czyszczenie szyb i dekoracji">
                        <label for="akw1_czyszczenieSzyb"><?php _e('Czyszczenie szyb i dekoracji', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_przycinanie" name="akw[1][zakres][]" value="przycinanie" data-text="Przycinanie/obsadzanie roślin">
                        <label for="akw1_przycinanie"><?php _e('Przycinanie/obsadzanie roślin', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_filtr" name="akw[1][zakres][]" value="filtr" data-text="Czyszczenie/konserwacja filtra">
                        <label for="akw1_filtr"><?php _e('Czyszczenie/konserwacja filtra', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_nawozenie" name="akw[1][zakres][]" value="nawozenie" data-text="Sprawdzenie i korekta nawożenia">
                        <label for="akw1_nawozenie"><?php _e('Sprawdzenie i korekta nawożenia', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_badanie" name="akw[1][zakres][]" value="badanie" data-text="Badanie parametrów wody">
                        <label for="akw1_badanie"><?php _e('Badanie parametrów wody', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_glony" name="akw[1][zakres][]" value="glony" data-text="Likwidacja glonów">
                        <label for="akw1_glony"><?php _e('Likwidacja glonów', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_przeglad" name="akw[1][zakres][]" value="przeglad" data-text="Przegląd techniczny sprzętu">
                        <label for="akw1_przeglad"><?php _e('Przegląd techniczny sprzętu', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_montazSprzetu" name="akw[1][zakres][]" value="montazSprzetu" data-text="Montaż nowego sprzętu (filtr, CO2, oświetlenie)">
                        <label for="akw1_montazSprzetu"><?php _e('Montaż nowego sprzętu (filtr, CO<sub>2</sub>, oświetlenie)', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_ocenaObsady" name="akw[1][zakres][]" value="ocenaObsady" data-text="Ocena stanu obsady (ryby, krewetki itp.)">
                        <label for="akw1_ocenaObsady"><?php _e('Ocena stanu obsady (ryby, krewetki itp.)', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_szkolenie" name="akw[1][zakres][]" value="szkolenie" data-text="Szkolenie / instruktaż użytkowania zbiornika">
                        <label for="akw1_szkolenie"><?php _e('Szkolenie / instruktaż użytkowania zbiornika', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_aranzacjaDekoracji" name="akw[1][zakres][]" value="aranzacjaDekoracji" data-text="Zmiana aranżacji/dekoracji">
                        <label for="akw1_aranzacjaDekoracji"><?php _e('Zmiana aranżacji/dekoracji', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_aplikacja" name="akw[1][zakres][]" value="aplikacja" data-text="Aplikacja preparatów (bakterie, uzdatniacze, nawozy)">
                        <label for="akw1_aplikacja"><?php _e('Aplikacja preparatów (bakterie, uzdatniacze, nawozy)', 'serwis-natu'); ?></label>
                    </div>
                </div>
            </div>
            
            <!-- Inne potrzeby -->
            <div class="subsection inne-potrzeby">
                <h4><?php _e('Inne potrzeby', 'serwis-natu'); ?></h4>
                <div class="checkbox-options">
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_jednorazowa" name="akw[1][inne][]" value="jednorazowa" data-text="Potrzebuję tylko jednorazowej pomocy">
                        <label for="akw1_jednorazowa"><?php _e('Potrzebuję tylko jednorazowej pomocy', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_stala" name="akw[1][inne][]" value="stala" data-text="Jestem zainteresowany stałą opieką (pakiet wielorazowy)">
                        <label for="akw1_stala"><?php _e('Jestem zainteresowany stałą opieką (pakiet wielorazowy)', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_kompleksowo" name="akw[1][inne][]" value="kompleksowo" data-text="Chcę, by ktoś kompleksowo zajął się moim zbiornikiem">
                        <label for="akw1_kompleksowo"><?php _e('Chcę, by ktoś kompleksowo zajął się moim zbiornikiem', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_okazja" name="akw[1][inne][]" value="okazja" data-text="Chcę przygotować akwarium na konkretną okazję">
                        <label for="akw1_okazja"><?php _e('Chcę przygotować akwarium na konkretną okazję', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_wakacje" name="akw[1][inne][]" value="wakacje" data-text="Wyjeżdżam na wakacje, potrzebuję na ten czas obsługi akwarium">
                        <label for="akw1_wakacje"><?php _e('Wyjeżdżam na wakacje, potrzebuję na ten czas obsługi akwarium', 'serwis-natu'); ?></label>
                    </div>
                    
                    <div class="checkbox-option">
                        <input type="checkbox" id="akw1_diagnoza" name="akw[1][inne][]" value="diagnoza" data-text="Mam problem - nie wiem, co się dzieje, potrzebuję diagnozy">
                        <label for="akw1_diagnoza"><?php _e('Mam problem - nie wiem, co się dzieje, potrzebuję diagnozy', 'serwis-natu'); ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="error-message" id="step1-error" style="display: none;">
        <?php _e('Proszę wybrać przynajmniej jedną opcję dla każdego akwarium.', 'serwis-natu'); ?>
    </div>
    
    <div class="form-navigation">
        <button type="button" id="next-step-1" class="next-step"><?php _e('Przejdź dalej', 'serwis-natu'); ?></button>
    </div>
</div>
