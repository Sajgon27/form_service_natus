<?php

/**
 * Szablon e-maila od administratora do klienta
 */

// Zabezpieczenie bezpośredniego dostępu
if (!defined('ABSPATH')) {
    exit;
}

// Dane dostępne w zmiennych:
// $dane - dane klienta i podstawowe informacje
// $zamowienie_id - ID zamówienia
// $aquariums - dane akwariów
// $custom_message - wiadomość od administratora
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('Wiadomość dt. usługi serwisowej', 'serwis-natu'); ?></title>
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

        .admin-message {
            background-color: #fff;
            padding: 15px;
            border-left: 4px solid #FF8C00;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1><?php echo __('Wiadomość dt. usługi serwisowej', 'serwis-natu'); ?></h1>
        </div>

        <div class="content">
            <div class="admin-message">
                <?php echo wpautop(wp_kses_post($custom_message)); ?>
            </div>


            <?php
            include_once(SERWIS_NATU_PATH . 'templates/emails/partials/order-content-to-client.php');
            ?>

        </div>

        <?php
        wc_get_template(
            'emails/partials/email-footer.php',
            [],
            '',
            SERWIS_NATU_PATH . 'templates/'
        );
        ?>
    </div>
</body>

</html>