<?php

add_action('admin_menu', 'register_orders_admin_page');
add_action('admin_init', 'handle_order_delete');
add_action('admin_enqueue_scripts', 'enqueue_orders_admin_styles');

/**
 * Enqueue styles for the orders admin page
 * 
 * @param string $hook Current admin page
 */
function enqueue_orders_admin_styles($hook) {
    // Only enqueue on our orders page
    if ($hook === 'serwis_page_serwis-natu-aquarium_orders') {
        wp_enqueue_style(
            'serwis-natu-orders-admin',
            plugin_dir_url(dirname(__FILE__)) . 'admin/css/orders-admin.css',
            array(),
            SERWIS_NATU_VERSION
        );
    }
}

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

/**
 * Handle order deletion
 */
function handle_order_delete()
{
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && isset($_GET['_wpnonce'])) {
        // Verify nonce for security
        if (!wp_verify_nonce($_GET['_wpnonce'], 'delete_order_' . $_GET['id'])) {
            wp_die('Błąd bezpieczeństwa. Proszę spróbować ponownie.');
        }

        // Delete the order
        global $wpdb;
        $table_name = $wpdb->prefix . 'serwis_natu_orders';
        $order_id = intval($_GET['id']);

        $result = $wpdb->delete($table_name, array('id' => $order_id), array('%d'));

        // Redirect back to the orders page
        $redirect_url = admin_url('admin.php?page=serwis-natu-aquarium_orders');
        
        if ($result) {
            $redirect_url = add_query_arg('message', 'deleted', $redirect_url);
        } else {
            $redirect_url = add_query_arg('message', 'error', $redirect_url);
        }
        
        wp_redirect($redirect_url);
        exit;
    }
}

/**
 * Display orders admin page with pagination
 */
function display_orders_admin_page()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'serwis_natu_orders';
    
    // Pagination settings
    $items_per_page = 10;
    $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;
    
    // Get total count for pagination
    $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    $total_pages = ceil($total_items / $items_per_page);
    
    // Get paginated results
    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $items_per_page,
            $offset
        ), 
        ARRAY_A
    );

    // Display status messages
    if (isset($_GET['message'])) {
        if ($_GET['message'] === 'deleted') {
            echo '<div class="notice notice-success is-dismissible"><p>Zamówienie zostało usunięte.</p></div>';
        } elseif ($_GET['message'] === 'error') {
            echo '<div class="notice notice-error is-dismissible"><p>Wystąpił błąd podczas usuwania zamówienia.</p></div>';
        }
    }

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
            <th colspan="2">Akcje</th>
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
            
            // Przycisk Usuń
            $delete_url = wp_nonce_url(
                admin_url('admin.php?page=serwis-natu-aquarium_orders&action=delete&id=' . intval($row['id'])),
                'delete_order_' . $row['id']
            );
            echo '<td><a class="button button-secondary" href="' . esc_url($delete_url) . '" onclick="return confirm(\'Czy na pewno chcesz usunąć to zamówienie? Tej operacji nie można cofnąć.\');">Usuń</a></td>';

            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="10">Brak zamówień</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    
    // Add pagination
    if ($total_pages > 1) {
        echo '<div class="tablenav bottom">';
        echo '<div class="tablenav-pages">';
        echo '<span class="displaying-num">' . sprintf(_n('%s element', '%s elementów', $total_items, 'serwis-natu'), number_format_i18n($total_items)) . '</span>';
        
        echo '<span class="pagination-links">';
        
        // First page link
        if ($current_page > 1) {
            echo '<a class="first-page button" href="' . esc_url(add_query_arg('paged', 1)) . '"><span class="screen-reader-text">Pierwsza strona</span><span aria-hidden="true">«</span></a>';
        } else {
            echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>';
        }
        
        // Previous page link
        if ($current_page > 1) {
            echo '<a class="prev-page button" href="' . esc_url(add_query_arg('paged', max(1, $current_page - 1))) . '"><span class="screen-reader-text">Poprzednia strona</span><span aria-hidden="true">‹</span></a>';
        } else {
            echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
        }
        
        echo '<span class="paging-input">' . 
             '<label for="current-page-selector" class="screen-reader-text">Bieżąca strona</label>' .
             '<input class="current-page" id="current-page-selector" type="text" name="paged" value="' . esc_attr($current_page) . '" size="1" aria-describedby="table-paging" />' .
             '<span class="tablenav-paging-text"> z <span class="total-pages">' . esc_html($total_pages) . '</span></span>' .
             '</span>';
        
        // Next page link
        if ($current_page < $total_pages) {
            echo '<a class="next-page button" href="' . esc_url(add_query_arg('paged', min($total_pages, $current_page + 1))) . '"><span class="screen-reader-text">Następna strona</span><span aria-hidden="true">›</span></a>';
        } else {
            echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>';
        }
        
        // Last page link
        if ($current_page < $total_pages) {
            echo '<a class="last-page button" href="' . esc_url(add_query_arg('paged', $total_pages)) . '"><span class="screen-reader-text">Ostatnia strona</span><span aria-hidden="true">»</span></a>';
        } else {
            echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>';
        }
        
        echo '</span>';
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
}
