<?php

add_action('admin_menu', 'register_order_detail_page');

function register_order_detail_page()
{
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
add_action('admin_head', function () {
    global $submenu;
    if (isset($submenu['aquarium_orders'])) {
        foreach ($submenu['aquarium_orders'] as $index => $item) {
            if ($item[2] === 'aquarium_order_detail') {
                unset($submenu['aquarium_orders'][$index]);
            }
        }
    }
});



function display_order_detail_page()
{
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
