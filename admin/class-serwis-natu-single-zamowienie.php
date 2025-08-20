<?php 

add_action('admin_menu', 'register_order_detail_page');

function register_order_detail_page() {
    // Tworzymy podstronę pod menu 'aquarium_orders'
    add_submenu_page(
        'aquarium_orders',            // parent slug
        'Szczegóły zamówienia',       // page title
        'Szczegóły zamówienia',       // menu title (będzie ukryty)
        'manage_options',             // capability
        'aquarium_order_detail',      // menu slug
        'display_order_detail_page'   // callback
    );
}

// Ukrycie subpage z menu, aby nie było widoczne
add_action('admin_head', function() {
    global $submenu;
    if (isset($submenu['aquarium_orders'])) {
        foreach ($submenu['aquarium_orders'] as $index => $item) {
            if ($item[2] === 'aquarium_order_detail') {
                unset($submenu['aquarium_orders'][$index]);
            }
        }
    }
});



function display_order_detail_page() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        wp_die(__('Nie masz uprawnień do przeglądania tej strony.', 'serwis-natu'));
    }
    
    if (!isset($_GET['id'])) {
        echo '<div class="notice notice-error"><p>Brak ID zamówienia</p></div>';
        return;
    }

    $id = intval($_GET['id']);
    global $wpdb;
    $table_name = $wpdb->prefix . 'serwis_natu_orders';

    $order = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id), ARRAY_A);

    if (!$order) {
        echo '<div class="notice notice-error"><p>Zamówienie nie znalezione</p></div>';
        return;
    }

    // Get aquariums data - use json_decode if data is JSON string, otherwise use as is
    $aquariums = $order['aquariums'];
    if (is_string($aquariums)) {
        $aquariums = json_decode($aquariums, true);
    }

    // Add custom CSS for the admin page
    echo '<style>
        .order-detail-container {
            margin: 20px 0;
            box-sizing: border-box;
        }
        .order-detail-header {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .order-detail-columns {
            display: flex;
            flex-wrap: wrap;
         
        }
        .order-detail-column {
            flex: 1;
            min-width: 300px;
            padding: 0 15px;
            box-sizing: border-box;
        }
        .order-info-box {
            background: #fff;
            border: 1px solid #ccd0d4;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .order-info-box h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 1.3em;
            color: #23282d;
        }
        .client-details {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 10px;
            margin-top: 15px;
        }
        .client-details .label {
            font-weight: bold;
            color: #23282d;
        }
  
        .aquarium-item {
            background: #fff;
            border: 1px solid #e5e5e5;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .aquarium-item h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            font-size: 1.2em;
            color: #23282d;
        }
        .aquarium-details {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
        }
        .aquarium-basic-details,
        .aquarium-services {
            flex: 1;
            min-width: 250px;
            padding: 0 10px;
        }
        .aquarium-item h4 {
            margin: 15px 0 5px 0;
            color: #23282d;
            font-size: 1.1em;
        }
        .aquarium-item h5 {
            margin: 10px 0 5px 0;
            color: #23282d;
            font-weight: 600;
        }
        .aquarium-item ul {
            margin: 0 0 15px 20px;
        }
        .aquarium-item p {
            margin: 5px 0 10px 0;
        }
        .detail-list, 
        .service-list {
            padding-left: 0;
            list-style-position: inside;
        }
        .order-actions {
            margin-top: 20px;
            text-align: right;
            padding-right:15px;
        }
        .order-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            background: #f0f6fc;
            color: #135e96;
            font-weight: bold;
        }
        .order-date {
            color: #666;
            margin-top: 5px;
            font-size: 14px;
        }
        .order-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 15px;
            background-color: #f9f9f9;
            border-radius: 3px;
        }
        .order-summary-item {
            text-align: center;
        }
        .order-summary-item .value {
            font-size: 24px;
            font-weight: bold;
            color: #23282d;
            display: block;
        }
        .order-summary-item .label {
            font-size: 13px;
            color: #646970;
        }
        .aquarium-photo {
            margin: 15px 0;
            text-align: left;
        }
        .aquarium-photo img {
            max-width: 300px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            cursor: pointer;
        }
        .order-price {
            font-size: 18px;
            font-weight: bold;
           
            margin-top: 15px;
            text-align: right;
            padding-right: 15px;
        }
        .cooperative-mode {
            background-color: #f0f6fc;
            color: #135e96;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            display: inline-block;
        }
        /* Responsive styles */
        @media screen and (max-width: 782px) {
            .client-details {
                grid-template-columns: 1fr;
            }
            .aquarium-details {
                display: block;
            }
        }
    </style>';

    echo '<div class="wrap order-detail-container">';
    
    echo '<div class="order-detail-header">';
    echo '<div>';
    echo '<h1>Szczegóły zamówienia #' . esc_html($order['id']) . '</h1>';
    echo '<p class="order-date">Data złożenia: ' . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($order['created_at'])) . '</p>';
    echo '</div>';
    echo '<div class="order-status">Nowe zamówienie</div>';
    echo '</div>';
    
    echo '<div class="order-detail-columns">';
    
    // Left column
    echo '<div class="order-detail-column">';
    
    // Client information box
    echo '<div class="order-info-box">';
    echo '<h2>Dane klienta</h2>';
    echo '<div class="client-details">';
    echo '<div class="label">Imię:</div><div>' . esc_html($order['client_first_name']) . '</div>';
    echo '<div class="label">Nazwisko:</div><div>' . esc_html($order['client_last_name']) . '</div>';
    echo '<div class="label">Email:</div><div>' . esc_html($order['client_email']) . '</div>';
    echo '<div class="label">Telefon:</div><div>' . esc_html($order['client_phone']) . '</div>';
    echo '<div class="label">Adres akwarium:</div><div>' . esc_html($order['aquarium_address']) . '</div>';
    echo '<div class="label">Preferowana data:</div><div>' . esc_html($order['preferred_date']) . '</div>';
    echo '</div>';
    echo '</div>';
    
    // Additional notes box
    echo '<div class="order-info-box">';
    echo '<h2>Uwagi dodatkowe</h2>';
    echo '<p>' . (empty($order['additional_notes']) ? 'Brak uwag' : esc_html($order['additional_notes'])) . '</p>';
    echo '</div>';
    
    echo '</div>'; // End left column
    
    // Right column
    echo '<div class="order-detail-column">';
    
    // Order Summary Stats
    $aquariumCount = is_array($aquariums) ? count($aquariums) : 0;
    $totalServices = 0;
    
    if (is_array($aquariums)) {
        foreach ($aquariums as $akw) {
            foreach ($akw as $key => $value) {
                if (is_array($value) && $key !== 'typ') {
                    $totalServices += count($value);
                }
            }
        }
    }
    
    // Display cooperative mode if available
    if (!empty($order['cooperative_mode'])) {
        echo '<div class="cooperative-mode"><strong>Tryb współpracy:</strong> ' . esc_html($order['cooperative_mode']) . '</div>';
    }
    
    // Aquariums and services box
    echo '<div class="order-info-box">';
    echo '<h2>Akwaria i usługi</h2>';

if ($aquariums && is_array($aquariums)) {
    echo '<div class="aquarium-list">';
    
    foreach ($aquariums as $akwId => $akw) {
        echo '<div class="aquarium-item">';
        echo '<h2>Akwarium #' . esc_html($akwId) . '</h2>';
        
        // Display aquarium photo if available
        if (isset($akw['photo_url'])) {
            echo '<div class="aquarium-photo">';
            echo '<a href="' . esc_url($akw['photo_url']) . '" target="_blank">';
            echo '<img src="' . esc_url($akw['photo_url']) . '" alt="Zdjęcie akwarium #' . esc_attr($akwId) . '" title="Kliknij, aby powiększyć">';
            echo '</a>';
            echo '</div>';
        }
        
        // Create a two-column layout for aquarium details
        echo '<div class="aquarium-details">';
        
        $details = [];
        $services = [];
        
        foreach ($akw as $key => $value) {
            // Organize data into details and services
            if ($key == 'typ' || $key == 'wielkosc' || $key == 'parametry' || $key == 'glebokosc') {
                $details[$key] = $value;
            } else if ($key != 'photo_url' && $key != 'photo_attachment_id') {
                $services[$key] = $value;
            }
        }
        
        // Display basic details first
        echo '<div class="aquarium-basic-details">';
        foreach ($details as $key => $value) {
            if (is_array($value)) {
                echo '<h4>' . esc_html(ucfirst($key)) . ':</h4>';
                echo '<ul class="detail-list">';
                foreach ($value as $item) {
                    echo '<li>' . esc_html($item) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<p><strong>' . esc_html(ucfirst($key)) . ':</strong> ' . esc_html($value) . '</p>';
            }
        }
        echo '</div>';
        
        // Display services
        if (!empty($services)) {
            echo '<div class="aquarium-services">';
          
            foreach ($services as $key => $value) {
                if (is_array($value)) {
                    echo '<h5>' . esc_html(ucfirst($key)) . ':</h5>';
                    echo '<ul class="service-list">';
                    foreach ($value as $item) {
                        echo '<li>' . esc_html($item) . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p><strong>' . esc_html(ucfirst($key)) . ':</strong> ' . esc_html($value) . '</p>';
                }
            }
            echo '</div>';
        }
        
        echo '</div>'; // End aquarium-details
        echo '</div>'; // End aquarium-item
    }
    
    echo '</div>'; // End aquarium-list
} else {
    echo '<p>Brak danych o akwariach.</p>';
}

    echo '</div>'; // Close order-info-box
    echo '</div>'; // Close right column
    
    echo '</div>'; // Close order-detail-columns
    
    // Display total price if available
    if (!empty($order['total_price'])) {
        echo '<div class="order-price">Łączna cena zamówienia: ' . number_format((float)$order['total_price'], 2, ',', ' ') . ' zł</div>';
    }
    
    // Order actions section
    echo '<div class="order-actions">';
    echo '<a class="button button-secondary" href="' . admin_url('admin.php?page=aquarium_orders') . '">Powrót do listy</a>';
    echo ' <a class="button button-primary" href="mailto:' . esc_attr($order['client_email']) . '?subject=' . urlencode('Zamówienie #' . $order['id']) . '">Wyślij email do klienta</a>';

    echo '</div>';
    
    echo '</div>'; // Close wrap
    
    // Add JavaScript for image handling
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Make images clickable with nice preview
            $('.aquarium-photo img').click(function(e) {
                e.preventDefault();
                var imgUrl = $(this).attr('src');
                window.open(imgUrl, '_blank');
            });
        });
    </script>
    <?php
}
