<?php

add_action('admin_menu', 'register_orders_admin_page');

function register_orders_admin_page()
{
    add_submenu_page(
        'serwis-natu-settings',
        'Zamówienia serwisu',
        'Zamówienia',
        'manage_options',
        'serwis-natu-aquarium_orders',
        'display_orders_admin_page'
    );
}


function display_orders_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'serwis_natu_orders';

    $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A);

    echo '<div class="wrap">';
    echo '<h1>Zamówienia akwariów</h1>';
    echo '<table class="widefat striped">';
    echo '<thead><tr>
            <th>ID</th>
            <th>Imię</th>
            <th>Nazwisko</th>
            <th>Email</th>
            <th>Telefon</th>
            <th>Adres akwarium</th>
            <th>Preferowana data serwisu</th>
              <th>Data utworzenia</th>
            <th>Opcje</th>
            
          
          </tr></thead>';
    echo '<tbody>';

    if ($results) {
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row['id']) . '</td>';
            echo '<td>' . esc_html($row['client_first_name']) . '</td>';
            echo '<td>' . esc_html($row['client_last_name']) . '</td>';
            echo '<td>' . esc_html($row['client_email']) . '</td>';
            echo '<td>' . esc_html($row['client_phone']) . '</td>';
            echo '<td>' . esc_html($row['aquarium_address']) . '</td>';
            echo '<td>' . esc_html($row['preferred_date']) . '</td>';
            echo '<td>' . esc_html($row['created_at']) . '</td>';
            // Przycisk Zobacz szczegóły
            $detail_url = admin_url('admin.php?page=aquarium_order_detail&id=' . intval($row['id']));
            echo '<td><a class="button button-primary" href="' . esc_url($detail_url) . '">Zobacz szczegóły</a></td>';


            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="10">Brak zamówień</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
