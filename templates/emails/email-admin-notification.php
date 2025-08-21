<?php
/**
 * Szablon e-maila powiadamiającego administratora o nowym zamówieniu usługi serwisowej
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
    <title><?php echo __('Nowe zamówienie usługi serwisowej', 'serwis-natu'); ?></title>
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
        
        /* Admin-specific styles */
        .admin-note {
            background-color: #fff8e1;
            border-left: 4px solid #ffc107;
            padding: 10px 15px;
            margin: 15px 0;
        }

        .button {
            display: inline-block;
            background-color: #FF8C00;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 3px;
            margin-top: 15px;
        }
        
        .order-info {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
        }

        .order-info-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #FF8C00;
        }
        
        .service-list {
            margin-top: 5px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><?php echo __('Nowe zamówienie usługi serwisowej #', 'serwis-natu') . $zamowienie_id; ?></h1>
        </div>

        <div class="content">
            <div class="admin-note">
                <p><strong>Informacja dla administratora:</strong> Otrzymano nowe zamówienie usługi serwisowej. Poniżej znajdują się szczegóły zgłoszenia.</p>
                <p>Możesz zarządzać tym zamówieniem w panelu administracyjnym.</p>
            </div>

            <div class="section">
                <h2><?php echo __('Dane klienta', 'serwis-natu'); ?></h2>
                <table>
                    <tr>
                        <td class="label"><?php echo __('Imię i nazwisko:', 'serwis-natu'); ?></td>
                        <td><?php echo esc_html($dane['imie'] . ' ' . $dane['nazwisko']); ?></td>
                    </tr>
                    <tr>
                        <td class="label"><?php echo __('E-mail:', 'serwis-natu'); ?></td>
                        <td><?php echo esc_html($dane['email']); ?></td>
                    </tr>
                    <tr>
                        <td class="label"><?php echo __('Telefon:', 'serwis-natu'); ?></td>
                        <td><?php echo esc_html($dane['telefon']); ?></td>
                    </tr>
                    <tr>
                        <td class="label"><?php echo __('Adres akwarium:', 'serwis-natu'); ?></td>
                        <td><?php echo esc_html($dane['adres']); ?></td>
                    </tr>
                    <tr>
                        <td class="label"><?php echo __('Preferowany termin:', 'serwis-natu'); ?></td>
                        <td><?php echo esc_html($dane['preferowany_termin']); ?></td>
                    </tr>
                </table>
            </div>

            <?php if (!empty($dane['tryb_wspolpracy'])) : ?>
                <div class="section">
                    <h2><?php echo __('Tryb współpracy', 'serwis-natu'); ?></h2>
                    <p><?php echo esc_html($dane['tryb_wspolpracy']); ?></p>
                </div>
            <?php endif; ?>

            <div class="section">
                <h2><?php echo __('Zgłoszone akwaria', 'serwis-natu'); ?></h2>
                
                <?php foreach ($aquariums as $index => $akwarium) : ?>
                    <div class="aquarium-box">
                        <h4><?php echo __('Akwarium', 'serwis-natu') . ' #' . $index; ?></h4>
                        
                        <?php if (isset($akwarium['typ'])) : ?>
                            <div class="service-list">
                                <strong><?php echo __('Typ akwarium:', 'serwis-natu'); ?></strong>
                                <?php if (is_array($akwarium['typ'])) : ?>
                                    <ul>
                                        <?php foreach ($akwarium['typ'] as $item) : ?>
                                            <li><?php echo esc_html($item); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <p><?php echo esc_html($akwarium['typ']); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($akwarium['Dopasowany pakiet'])) : ?>
                            <div class="service-list">
                                <strong><?php echo __('Dopasowany pakiet:', 'serwis-natu'); ?></strong>
                                <p><?php echo esc_html($akwarium['Dopasowany pakiet']); ?></p>
                            </div>
                        <?php endif; ?>

                        <?php
                        // Wyświetl pozostałe dane akwarium
                        $excluded_keys = array('typ', 'Dopasowany pakiet', 'photo_url', 'photo_attachment_id');
                        foreach ($akwarium as $key => $value) :
                            if (!in_array($key, $excluded_keys)) :
                        ?>
                            <div class="service-list">
                                <strong><?php echo esc_html($key) . ':'; ?></strong>
                                <?php if (is_array($value)) : ?>
                                    <ul>
                                        <?php foreach ($value as $item) : ?>
                                            <li><?php echo esc_html($item); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else : ?>
                                    <p><?php echo esc_html($value); ?></p>
                                <?php endif; ?>
                            </div>
                        <?php endif; endforeach; ?>

                        <?php if (isset($akwarium['photo_url'])) : ?>
                            <div class="service-list">
                                <strong><?php echo __('Zdjęcie akwarium:', 'serwis-natu'); ?></strong>
                                <p><a href="<?php echo esc_url($akwarium['photo_url']); ?>" target="_blank"><?php echo __('Zobacz zdjęcie', 'serwis-natu'); ?></a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($dane['cena'])) : ?>
                <div class="section">
                    <h2><?php echo __('Kwota zamówienia:', 'serwis-natu'); ?></h2>
                    <p><strong><?php echo number_format($dane['cena'], 2, ',', ' ') . ' zł'; ?></strong></p>
                </div>
            <?php endif; ?>

            <div class="section">
                <p style="text-align: center;">
                    <a href="<?php echo admin_url('admin.php?page=aquarium_order_detail&id=' . $zamowienie_id); ?>" class="button">
                        <?php echo __('Zarządzaj zamówieniem', 'serwis-natu'); ?>
                    </a>
                </p>
            </div>
        </div>

        <div class="footer">
            <p><?php echo __('Wiadomość wygenerowana automatycznie. Prosimy na nią nie odpowiadać.', 'serwis-natu'); ?></p>
            <p>&copy; <?php echo date('Y'); ?> Natuscape</p>
        </div>
    </div>
</body>

</html>
