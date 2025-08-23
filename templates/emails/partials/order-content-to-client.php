<div class="section">
    <h2><?php echo __('Szczegóły zamówienia', 'serwis-natu'); ?></h2>
    <p><strong><?php echo __('Numer zamówienia:', 'serwis-natu'); ?></strong> #<?php echo $zamowienie_id; ?></p>
    <p><strong><?php echo __('Data zamówienia:', 'serwis-natu'); ?></strong>
        <?php
        // Format date in Polish
        echo serwis_natu_format_polish_date(current_time('timestamp'));
        ?></p>
</div>

<div class="section">
    <h2><?php echo __('Wybrane usługi', 'serwis-natu'); ?></h2>

    <?php if (isset($dane['tryb_wspolpracy'])): ?>
        <p><strong><?php echo __('Tryb współpracy:', 'serwis-natu'); ?></strong>
            <?php
            $tryb = $dane['tryb_wspolpracy'];
            if ($tryb == 'jednorazowa') echo __('Jednorazowa usługa serwisowa', 'serwis-natu');
            elseif ($tryb == 'wielorazowa') echo __('Pakiet wielorazowy (abonamentowy)', 'serwis-natu');
            elseif ($tryb == 'dodatkowa') echo __('Usługi dodatkowe', 'serwis-natu');
            else echo $tryb;
            ?>
        </p>
    <?php endif; ?>

    <h3><?php echo __('Szczegóły akwariów', 'serwis-natu'); ?></h3>

    <?php if (is_array($aquariums) && !empty($aquariums)): ?>
        <?php foreach ($aquariums as $index => $akw): ?>
            <div class="aquarium-box">
                <h4><?php echo sprintf(__('Akwarium #%s', 'serwis-natu'), $index); ?></h4>

                <?php if (!empty($akw['typ'])): ?>
                    <p><strong><?php echo __('Typ zbiornika:', 'serwis-natu'); ?></strong>
                        <?php echo is_array($akw['typ']) ? implode(', ', $akw['typ']) : $akw['typ']; ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($akw['cel'])): ?>
                    <p><strong><?php echo __('Cel wizyty:', 'serwis-natu'); ?></strong>
                        <?php echo is_array($akw['cel']) ? implode(', ', $akw['cel']) : $akw['cel']; ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($akw['zakres'])): ?>
                    <p><strong><?php echo __('Zakres prac:', 'serwis-natu'); ?></strong>
                        <?php echo is_array($akw['zakres']) ? implode(', ', $akw['zakres']) : $akw['zakres']; ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($akw['inne'])): ?>
                    <p><strong><?php echo __('Dodatkowe informacje:', 'serwis-natu'); ?></strong> <?php echo $akw['inne']; ?></p>
                <?php endif; ?>

                <?php if (!empty($akw['Dodatkowe usługi']) && is_array($akw['Dodatkowe usługi'])): ?>
                    <p><strong><?php echo __('Usługi dodatkowe:', 'serwis-natu'); ?></strong></p>
                    <ul>
                        <?php foreach ($akw['Dodatkowe usługi'] as $service): ?>
                            <li>
                                <?php 
                                if (is_array($service) && isset($service['name'])) {
                                    echo esc_html($service['name']);
                                    if (isset($service['price'])) {
                                        echo ' - ' . number_format((float)$service['price'], 2, ',', ' ') . ' ' . __('zł', 'serwis-natu');
                                    }
                                } else {
                                    echo is_string($service) ? esc_html($service) : '';
                                }
                                ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p><?php echo __('Brak szczegółowych informacji o akwariach.', 'serwis-natu'); ?></p>
    <?php endif; ?>

    <table>
        <tr>
            <th><?php echo __('Pozycja', 'serwis-natu'); ?></th>
            <th><?php echo __('Cena', 'serwis-natu'); ?></th>
        </tr>
        
        <?php
        // Initialize totals
        $package_total = 0;
        $extra_services_total = 0;
        $products_total = 0;
        $grand_total = 0;
        
        // Display package prices
        if (is_array($aquariums) && !empty($aquariums)):
            foreach ($aquariums as $index => $akw):
                if (!empty($akw['Dopasowany pakiet']) && isset($akw['Cena pakietu'])):
                    $package_price = (float)$akw['Cena pakietu'];
                    $package_total += $package_price;
        ?>
                <tr>
                    <td><?php echo sprintf(__('Pakiet: %s (Akwarium #%s)', 'serwis-natu'), 
                        esc_html($akw['Dopasowany pakiet']), $index); ?></td>
                    <td><?php echo number_format($package_price, 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></td>
                </tr>
        <?php
                endif;
            endforeach;
        endif;
        
        // Display extra services prices by aquarium
        if (is_array($aquariums) && !empty($aquariums)):
            foreach ($aquariums as $index => $akw):
                if (!empty($akw['Dodatkowe usługi']) && is_array($akw['Dodatkowe usługi'])):
                    // Add a separator/header for the aquarium's extra services
                    if (count($akw['Dodatkowe usługi']) > 0):
        ?>
                <tr>
                    <td colspan="2" style="background-color: #f9f9f9; padding: 5px;">
                        <strong><?php echo sprintf(__('Usługi dodatkowe - Akwarium #%s', 'serwis-natu'), $index); ?></strong>
                    </td>
                </tr>
        <?php
                    endif;
                    
                    foreach ($akw['Dodatkowe usługi'] as $service):
                        if (is_array($service) && isset($service['name']) && isset($service['price'])):
                            $service_price = (float)$service['price'];
                            $extra_services_total += $service_price;
        ?>
                <tr>
                    <td><?php echo esc_html($service['name']); ?></td>
                    <td><?php echo number_format($service_price, 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></td>
                </tr>
        <?php
                        endif;
                    endforeach;
                endif;
                
                // Also check for extra_services_price field if no individual services are present
                if (empty($akw['Dodatkowe usługi']) && isset($akw['extra_services_price']) && $akw['extra_services_price'] > 0):
                    $es_price = (float)$akw['extra_services_price'];
                    $extra_services_total += $es_price;
        ?>
                <tr>
                    <td><?php echo sprintf(__('Usługi dodatkowe - Akwarium #%s', 'serwis-natu'), $index); ?></td>
                    <td><?php echo number_format($es_price, 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></td>
                </tr>
        <?php
                endif;
            endforeach;
        endif;
        
        // Display subtotal for services if there's more than one component
        if ($package_total > 0 && $extra_services_total > 0):
            $services_subtotal = $package_total + $extra_services_total;
        ?>
        <tr>
            <td><strong><?php echo __('Podsumowanie usług serwisowych', 'serwis-natu'); ?></strong></td>
            <td><strong><?php echo number_format($services_subtotal, 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></strong></td>
        </tr>
        <?php 
        endif;
        
        // Display selected products if available
        $products = isset($dane['products']) ? json_decode($dane['products'], true) : [];
        
        if (!empty($products) && is_array($products)):
            // Add a header for products section if there are products
            if (count($products) > 0):
        ?>
        <tr>
            <td colspan="2" style="background-color: #f9f9f9; padding: 5px;">
                <strong><?php echo __('Wybrane produkty', 'serwis-natu'); ?></strong>
            </td>
        </tr>
        <?php
            endif;
            
            foreach ($products as $product):
                if (isset($product['name']) && isset($product['price'])):
                    $product_price = (float)$product['price'];
                    $products_total += $product_price;
        ?>
        <tr>
            <td><?php echo esc_html($product['name']); ?></td>
            <td><?php echo number_format($product_price, 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></td>
        </tr>
        <?php
                endif;
            endforeach;
            
            // Show products subtotal if there are multiple products
            if (count($products) > 1):
        ?>
        <tr>
            <td><strong><?php echo __('Podsuma produktów', 'serwis-natu'); ?></strong></td>
            <td><strong><?php echo number_format($products_total, 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></strong></td>
        </tr>
        <?php
            endif;
        endif;
        
        // Calculate final total
        $grand_total = $package_total + $extra_services_total + $products_total;
        ?>

        <tr class="total-row">
            <td><?php echo __('Razem', 'serwis-natu'); ?></td>
            <td><?php echo number_format($grand_total, 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></td>
        </tr>
    </table>
    <p><em><?php echo __('Uwaga: Ostateczna cena może się różnić po ocenie stanu akwarium przez serwisanta.', 'serwis-natu'); ?></em></p>
</div>

<div class="section">
    <h2><?php echo __('Dane kontaktowe', 'serwis-natu'); ?></h2>
    <p><strong><?php echo __('Imię i nazwisko:', 'serwis-natu'); ?></strong> <?php echo $dane['imie'] . ' ' . $dane['nazwisko']; ?></p>
    <p><strong><?php echo __('Email:', 'serwis-natu'); ?></strong> <?php echo $dane['email']; ?></p>
    <p><strong><?php echo __('Telefon:', 'serwis-natu'); ?></strong> <?php echo $dane['telefon']; ?></p>
    <p><strong><?php echo __('Adres:', 'serwis-natu'); ?></strong> <?php echo $dane['adres']; ?></p>
    <p><strong><?php echo __('Preferowany termin:', 'serwis-natu'); ?></strong> <?php
                                                                                echo serwis_natu_format_polish_date($dane['preferowany_termin']);
                                                                                ?></p>
</div>

<div class="section">
    <h2><?php echo __('Instrukcja przygotowania do wizyty', 'serwis-natu'); ?></h2>
    <div class="instructions">
        <ol>
            <li><?php echo __('Zapewnij łatwy dostęp do akwarium z przodu i z góry.', 'serwis-natu'); ?></li>
            <li><?php echo __('Odłącz urządzenia elektryczne, które mogą przeszkadzać (oświetlenie zewnętrzne, pokrywy).', 'serwis-natu'); ?></li>
            <li><?php echo __('Przygotuj wiadra lub pojemniki na wodę (jeśli posiadasz).', 'serwis-natu'); ?></li>
            <li><?php echo __('Jeśli możliwe, przygotuj wcześniej wodę do podmiany (odstać, uzdatnić).', 'serwis-natu'); ?></li>
            <li><?php echo __('Zabezpiecz miejsce wokół akwarium przed możliwym zachlapaniem.', 'serwis-natu'); ?></li>
        </ol>
    </div>
</div>