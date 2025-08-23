<?php

add_action('admin_menu', 'register_order_detail_page');

// AJAX handler for updating order status
add_action('wp_ajax_update_order_status', 'serwis_natu_update_order_status');

function serwis_natu_update_order_status() {
    // Check nonce for security
    check_ajax_referer('update_order_status_nonce', 'security');
    
    // Check if user has permission
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Nie masz uprawnień do wykonania tej operacji.');
        return;
    }
    
    // Get and validate parameters
    $order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
    $status = isset($_POST['status']) ? sanitize_text_field($_POST['status']) : '';
    
    // Validate status
    $valid_statuses = array('Oczekujące', 'Potwierdzone', 'Anulowane');
    if (!in_array($status, $valid_statuses)) {
        wp_send_json_error('Nieprawidłowy status.');
        return;
    }
    
    // Get database table
    global $wpdb;
    $table_name = $wpdb->prefix . 'serwis_natu_orders';
    
    // Update the order status
    $result = $wpdb->update(
        $table_name,
        array('status' => $status),
        array('id' => $order_id),
        array('%s'),
        array('%d')
    );
    
    if ($result !== false) {
        wp_send_json_success(array('status' => $status));
    } else {
        wp_send_json_error('Nie udało się zaktualizować statusu zamówienia.');
    }
}

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
    echo '<p class="order-date">Data złożenia: ' . serwis_natu_format_polish_date($order['created_at']) . '</p>';
    echo '</div>';
    
    // Get current status or default to 'Oczekujące'
    $current_status = isset($order['status']) ? $order['status'] : 'Oczekujące';
    
    // Add status class for styling
    $status_class = strtolower(remove_accents($current_status));
    echo '<div class="order-status status-' . esc_attr($status_class) . '">' . esc_html($current_status) . '</div>';
    
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


        // Display selected products if available
    if (!empty($order['products'])) {
        // Decode products JSON
        $products = json_decode($order['products'], true);
        
        if (!empty($products) && is_array($products)) {
            echo '<div class="order-info-box products-info-box">';
            echo '<h2>Wybrane produkty</h2>';
            echo '<div class="products-list">';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead><tr>';
            echo '<th>Nazwa produktu</th>';
            echo '<th>Cena</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            
            $products_total = 0;
            
            foreach ($products as $product) {
                echo '<tr>';
                echo '<td>' . esc_html($product['name']) . '</td>';
                echo '<td>' . number_format((float)$product['price'], 2, ',', ' ') . ' zł</td>';
                echo '</tr>';
                
                // Add to products total
                $products_total += (float)$product['price'];
            }
            
            echo '</tbody>';
            echo '<tfoot>';
            echo '<tr>';
            echo '<th>Razem za produkty:</th>';
            echo '<th>' . number_format($products_total, 2, ',', ' ') . ' zł</th>';
            echo '</tr>';
            echo '</tfoot>';
            echo '</table>';
            echo '</div>'; // Close products-list
            echo '</div>'; // Close order-info-box
        }
    }

    // Status selection box
    echo '<div class="order-info-box">';
    echo '<h2>Status zamówienia</h2>';
    echo '<div id="status-update-container">';
    
    // Get current status or default to 'Oczekujące'
    $current_status = isset($order['status']) ? $order['status'] : 'Oczekujące';
    $status_options = array('Oczekujące', 'Potwierdzone', 'Anulowane');
    
    // Create the status select field
    echo '<select id="order-status-select" data-order-id="' . esc_attr($order['id']) . '">';
    foreach ($status_options as $status) {
        echo '<option value="' . esc_attr($status) . '" ' . selected($status, $current_status, false) . '>' . esc_html($status) . '</option>';
    }
    echo '</select>';
    
    echo '<div id="status-update-message"></div>';
    echo '</div>'; // Close status-update-container
    echo '</div>'; // Close order-info-box
    
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
                } else if ($key != 'photo_url' && $key != 'photo_attachment_id' && $key != 'extra_services_price') {
                    $services[$key] = $value;
                }
            }

      

            // Display services
            if (!empty($services)) {
                echo '<div class="aquarium-services">';
                
                // First display package info if available
                if (isset($services['Dopasowany pakiet'])) {
                    echo '<div class="recommended-package">';
                    echo '<h4>Dopasowany pakiet:</h4>';
                    echo '<p class="package-name">' . esc_html($services['Dopasowany pakiet']) . '</p>';
                    echo '</div>';
                    
                    // Remove these items so they don't show up again in the regular services list
                    unset($services['Dopasowany pakiet']);
                    unset($services['Cena pakietu']);
                }

                      // Display basic details first
            echo '<div class="aquarium-basic-details">';
            foreach ($details as $key => $value) {
                // Display key without modification as we expect 'typ' to already be in the format we want
                $display_key = $key;
                
                if (is_array($value)) {
                    echo '<h4>' . esc_html($display_key) . ':</h4>';
                    echo '<ul class="detail-list">';
                    foreach ($value as $item) {
                        echo '<li>' . esc_html($item) . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<h4>' . esc_html($display_key) . ':</h4> ' . esc_html($value) . '</p>';
                }
            }
            echo '</div>';

                foreach ($services as $key => $value) {
                    // Format the key for display - ensure first letter is capitalized
                    $display_key = $key;
                    
                    if (is_array($value)) {
                        echo '<h4>' . esc_html($display_key) . ':</h4>';
                        echo '<ul class="service-list">';
                        
                        foreach ($value as $item) {
                            // Check if the item is an array with name and price (new format)
                            if (is_array($item) && isset($item['name'])) {
                                echo '<li>';
                                echo esc_html($item['name']);
                                
                                // Add price if available
                                if (isset($item['price'])) {
                                    echo ' - <span class="service-price">' . number_format((float)$item['price'], 2, ',', ' ') . ' zł</span>';
                                }
                                
                                echo '</li>';
                            } else {
                                // Fallback for simple string items (old format)
                                echo '<li>' . esc_html($item) . '</li>';
                            }
                        }
                        
                        echo '</ul>';
                        
                        // If this is Dodatkowe usługi and we have a price, show total
                        if ($key === 'Dodatkowe usługi' && isset($akw['extra_services_price']) && $akw['extra_services_price'] > 0) {
                            echo '<div class="extra-services-total">Razem za dodatkowe usługi: <strong>' . 
                                number_format((float)$akw['extra_services_price'], 2, ',', ' ') . ' zł</strong></div>';
                        }
                    } else {
                        echo '<h4>' . esc_html($display_key) . ':</h4> ' . esc_html($value) . '</p>';
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
        echo '<div class="order-price">Łączna cena zamówienia wysłana do klienta: ' . number_format((float)$order['total_price'], 2, ',', ' ') . ' zł</div>';
    }

    // Order actions section
    echo '<div class="order-actions">';
    echo '<a class="button button-secondary" href="' . admin_url('admin.php?page=aquarium_orders') . '">Powrót do listy</a>';
    echo ' <a class="button button-primary" id="send-email-to-client" data-order-id="' . esc_attr($order['id']) . '" data-client-email="' . esc_attr($order['client_email']) . '">Wyślij email do klienta</a>';

    echo '</div>';
    
    // Create email modal
    echo '<div id="email-modal" class="email-modal">';
    echo '<div class="email-modal-content">';
    echo '<span class="email-modal-close">&times;</span>';
    echo '<h2>Wyślij wiadomość do klienta</h2>';
    echo '<div class="email-form">';
    echo '<p>Wiadomość zostanie wysłana na adres: <strong>' . esc_html($order['client_email']) . '</strong></p>';
    echo '<textarea id="email-message" rows="10" placeholder="Wpisz wiadomość do klienta..."></textarea>';
    echo '<div id="email-status"></div>';
    echo '<button id="send-email-button" class="button button-primary">Wyślij</button>';
    echo '</div>'; // close email-form
    echo '</div>'; // close email-modal-content
    echo '</div>'; // close email-modal

    echo '</div>'; // Close wrap

    // Add JavaScript for image handling and email modal
?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Make images clickable with nice preview
            $('.aquarium-photo img').click(function(e) {
                e.preventDefault();
                var imgUrl = $(this).attr('src');
                window.open(imgUrl, '_blank');
            });
            
            // Handle status change
            $('#order-status-select').change(function() {
                var $select = $(this);
                var orderId = $select.data('order-id');
                var newStatus = $select.val();
                var $message = $('#status-update-message');
                
                // Show loading message
                $message.html('<div class="status-loading">Aktualizowanie statusu...</div>');
                
                // Send AJAX request
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'update_order_status',
                        order_id: orderId,
                        status: newStatus,
                        security: '<?php echo wp_create_nonce("update_order_status_nonce"); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update the status display in the header
                            var statusClass = newStatus.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                            $('.order-status')
                                .attr('class', 'order-status status-' + statusClass)
                                .text(newStatus);
                                
                            // Show success message
                            $message.html('<div class="status-success">Status zamówienia zaktualizowany!</div>');
                            
                            // Hide message after 3 seconds
                            setTimeout(function() {
                                $message.empty();
                            }, 3000);
                        } else {
                            // Show error message
                            $message.html('<div class="status-error">Błąd: ' + (response.data || 'Nie udało się zaktualizować statusu') + '</div>');
                        }
                    },
                    error: function() {
                        $message.html('<div class="status-error">Błąd połączenia. Spróbuj ponownie.</div>');
                    }
                });
            });
            
            // Email Modal functionality
            var modal = $('#email-modal');
            var btn = $('#send-email-to-client');
            var span = $('.email-modal-close');
            var sendBtn = $('#send-email-button');
            var statusDiv = $('#email-status');
            
            // Open modal when button is clicked
            btn.click(function() {
                modal.css('display', 'block');
            });
            
            // Close modal when X is clicked
            span.click(function() {
                modal.css('display', 'none');
                $('#email-message').val('');
                statusDiv.html('');
            });
            
            // Close modal when clicking outside of it
            $(window).click(function(e) {
                if ($(e.target).is(modal)) {
                    modal.css('display', 'none');
                    $('#email-message').val('');
                    statusDiv.html('');
                }
            });
            
            // Handle send email button click
            sendBtn.click(function() {
                var message = $('#email-message').val();
                if (!message.trim()) {
                    statusDiv.html('<div class="status-error">Wiadomość nie może być pusta</div>');
                    return;
                }
                
                var orderId = btn.data('order-id');
                
                // Show loading status
                statusDiv.html('<div class="status-loading">Wysyłanie wiadomości...</div>');
                sendBtn.prop('disabled', true);
                
                // Send AJAX request
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'send_custom_email_to_client',
                        order_id: orderId,
                        message: message,
                        security: '<?php echo wp_create_nonce("send_email_to_client_nonce"); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            statusDiv.html('<div class="status-success">Wiadomość została wysłana!</div>');
                            setTimeout(function() {
                                modal.css('display', 'none');
                                $('#email-message').val('');
                                statusDiv.html('');
                                sendBtn.prop('disabled', false);
                            }, 2000);
                        } else {
                            statusDiv.html('<div class="status-error">Błąd: ' + (response.data || 'Nie udało się wysłać wiadomości') + '</div>');
                            sendBtn.prop('disabled', false);
                        }
                    },
                    error: function() {
                        statusDiv.html('<div class="status-error">Błąd połączenia. Spróbuj ponownie.</div>');
                        sendBtn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
    <style type="text/css">
        .recommended-package {
            background-color: #f0f7ff;
            border-left: 4px solid #2271b1;
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        .recommended-package h4 {
            color: #2271b1;
            margin-top: 0;
            margin-bottom: 10px;
        }
        .package-name {
            font-size: 16px;
            font-weight: 600;
            margin: 5px 0;
        }
   
        .products-list table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .products-list th {
            text-align: left;
            padding: 8px;
            font-weight: bold;
        }
        .products-list td {
            padding: 8px;
        }
        .products-list tfoot {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .aquarium-details h4 {
            margin-top: 15px;
            margin-bottom: 8px;
            color: #333;
        }
        .service-list, .detail-list {
            margin-top: 5px;
            margin-bottom: 15px;
        }
        
        .service-price {
            color: #2271b1;
            font-weight: 500;
        }
        
        .extra-services-total {
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #f0f7ff;
            border-radius: 3px;
            display: inline-block;
        }
        
        /* Status styles */
        .order-status {
            padding: 8px 15px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
        }
        .status-oczekujace {
            background-color: #f0ad4e;
        }
        .status-potwierdzone {
            background-color: #5cb85c;
        }
        .status-anulowane {
            background-color: #d9534f;
        }
        
        /* Status select styling */
        #order-status-select {
            padding: 8px;
            width: 100%;
            max-width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        /* Status messages */
        #status-update-message {
            margin-top: 10px;
            font-weight: 500;
        }
        .status-loading {
            color: #0073aa;
        }
        .status-success {
            color: #46b450;
        }
        .status-error {
            color: #dc3232;
        }
        
        /* Email modal styles */
        .email-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .email-modal-content {
            background-color: #f9f9f9;
            margin: 10% auto;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 60%;
            max-width: 700px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        
        .email-modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            color: #aaa;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .email-modal-close:hover {
            color: #555;
        }
        
        .email-form {
            margin-top: 20px;
        }
        
        #email-message {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
            font-family: inherit;
            font-size: 14px;
            margin-bottom: 15px;
        }
        
        #email-status {
            margin: 10px 0;
            min-height: 24px;
        }
        
        #send-email-button {
            padding: 8px 16px;
            cursor: pointer;
        }
        
        #send-email-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
<?php
}
