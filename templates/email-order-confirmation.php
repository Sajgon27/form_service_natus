<?php
/**
 * Szablon e-maila potwierdzającego zamówienie usługi serwisowej
 */

// Zabezpieczenie bezpośredniego dostępu
if (!defined('ABSPATH')) {
    exit;
}

// Dane zamówienia dostępne w zmiennych:
// $dane - dane klienta i podstawowe informacje
// $zamowienie_id - ID zamówienia
// $aquariums - dane akwariów
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('Potwierdzenie zamówienia usługi serwisowej', 'serwis-natu'); ?></title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #444444;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #FF8C00;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            background-color: #ffffff;
        }

        .section {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section h2 {
            font-size: 18px;
            margin-top: 0;
            margin-bottom: 10px;
            color: #333333;
        }
        
        .aquarium-box {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .aquarium-box h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        
        .aquarium-box p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th,
        table td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f5f5f5;
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #777777;
        }

        .instructions {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }

        .instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }

        .instructions li {
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><?php echo __('Potwierdzenie zamówienia usługi serwisowej', 'serwis-natu'); ?></h1>
        </div>

        <div class="content">
            <p><?php echo sprintf(__('Witaj %s,', 'serwis-natu'), $dane['imie'] . ' ' . $dane['nazwisko']); ?></p>

            <p><?php echo __('Dziękujemy za złożenie zamówienia na usługę serwisu akwarystycznego. Poniżej znajdziesz szczegóły Twojego zamówienia:', 'serwis-natu'); ?></p>

            <div class="section">
                <h2><?php echo __('Szczegóły zamówienia', 'serwis-natu'); ?></h2>
                <p><strong><?php echo __('Numer zamówienia:', 'serwis-natu'); ?></strong> #<?php echo $zamowienie_id; ?></p>
                <p><strong><?php echo __('Data zamówienia:', 'serwis-natu'); ?></strong> <?php echo date('j F Y, H:i', current_time('timestamp')); ?></p>
            </div>

            <div class="section">
                <h2><?php echo __('Wybrane usługi', 'serwis-natu'); ?></h2>

                <?php if(isset($dane['tryb_wspolpracy'])): ?>
                <p><strong><?php echo __('Tryb współpracy:', 'serwis-natu'); ?></strong> 
                    <?php 
                    $tryb = $dane['tryb_wspolpracy'];
                    if($tryb == 'jednorazowa') echo __('Jednorazowa usługa serwisowa', 'serwis-natu');
                    elseif($tryb == 'wielorazowa') echo __('Pakiet wielorazowy (abonamentowy)', 'serwis-natu');
                    elseif($tryb == 'dodatkowa') echo __('Usługi dodatkowe', 'serwis-natu');
                    else echo $tryb;
                    ?>
                </p>
                <?php endif; ?>
                
                <h3><?php echo __('Szczegóły akwariów', 'serwis-natu'); ?></h3>
                
                <?php if(is_array($aquariums) && !empty($aquariums)): ?>
                    <?php foreach($aquariums as $index => $akw): ?>
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
                        
                        <?php if (!empty($akw['extra_services'])): ?>
                        <p><strong><?php echo __('Usługi dodatkowe:', 'serwis-natu'); ?></strong></p>
                        <ul>
                            <?php foreach($akw['extra_services'] as $service): ?>
                            <li><?php echo $service; ?></li>
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
                    <tr>
                        <td><?php echo __('Usługa serwisowa', 'serwis-natu'); ?></td>
                        <td><?php echo number_format($dane['cena'], 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td><?php echo __('Razem', 'serwis-natu'); ?></td>
                        <td><?php echo number_format($dane['cena'], 2, ',', ' '); ?> <?php echo __('zł', 'serwis-natu'); ?></td>
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
                <p><strong><?php echo __('Preferowany termin:', 'serwis-natu'); ?></strong> <?php echo date('j F Y, H:i', strtotime($dane['preferowany_termin'])); ?></p>
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

            <div class="section">
                <h2><?php echo __('Co dalej?', 'serwis-natu'); ?></h2>
                <p><?php echo __('Nasz przedstawiciel skontaktuje się z Tobą w ciągu 24 godzin w celu potwierdzenia terminu wizyty.', 'serwis-natu'); ?></p>
                <p><?php echo __('Jeśli masz jakiekolwiek pytania, możesz się z nami skontaktować:', 'serwis-natu'); ?></p>
                <ul>
                    <li><?php echo __('Telefonicznie: 530 072 247', 'serwis-natu'); ?></li>
                    <li><?php echo __('E-mail: sklep@natuscape.pl', 'serwis-natu'); ?></li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p><?php echo __('Z pozdrowieniami,', 'serwis-natu'); ?><br><?php echo __('Zespół Natuscape', 'serwis-natu'); ?></p>
            <p><?php echo __('Tel: 530 072 247 | Email: sklep@natuscape.pl', 'serwis-natu'); ?></p>
            <p><?php echo sprintf(__('© %s Natuscape. Wszystkie prawa zastrzeżone.', 'serwis-natu'), date('Y')); ?></p>
        </div>
    </div>
</body>

</html>
