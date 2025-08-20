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

    $aquariums = maybe_unserialize($order['aquariums']); // lub json_decode jeśli używasz wp_json_encode

    echo '<div class="wrap">';
    echo '<h1>Szczegóły zamówienia #' . esc_html($order['id']) . '</h1>';

    echo '<h2>Dane klienta</h2>';
    echo '<ul>';
    echo '<li>Imię: ' . esc_html($order['client_first_name']) . '</li>';
    echo '<li>Nazwisko: ' . esc_html($order['client_last_name']) . '</li>';
    echo '<li>Email: ' . esc_html($order['client_email']) . '</li>';
    echo '<li>Telefon: ' . esc_html($order['client_phone']) . '</li>';
    echo '<li>Adres akwarium: ' . esc_html($order['aquarium_address']) . '</li>';
    echo '<li>Preferowana data: ' . esc_html($order['preferred_date']) . '</li>';
    echo '</ul>';

    echo '<h2>Akwaria i usługi</h2>';
 
$aquariums = json_decode($aquariums, true); // dekodujemy JSON do tablicy PHP

if ($aquariums && is_array($aquariums)) {
    echo '<div class="aquarium-list">';
    
    foreach ($aquariums as $akwId => $akw) {
        echo '<div class="aquarium-item" style="border:1px solid #ccc;padding:15px;margin-bottom:10px;">';
        echo '<h2>Akwarium #' . esc_html($akwId) . '</h2>';
        
        foreach ($akw as $key => $value) {
            // jeśli wartość jest tablicą, wyświetlamy listę punktowaną
            if (is_array($value)) {
                echo '<h4>' . esc_html(ucfirst($key)) . ':</h4>';
                echo '<ul>';
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
    
    echo '</div>';
} else {
    echo '<p>Brak danych o akwariach.</p>';
}



    echo '<h2>Uwagi dodatkowe</h2>';
    echo '<p>' . esc_html($order['additional_notes']) . '</p>';

    echo '<p><a class="button" href="' . admin_url('admin.php?page=aquarium_orders') . '">Powrót do listy</a></p>';

    echo '</div>';
}
