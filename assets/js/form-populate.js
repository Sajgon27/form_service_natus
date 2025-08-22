/**
 * Form Population JavaScript for Serwis Natu
 * 
 * This script handles pre-populating the form fields when a zamowienie parameter is present in the URL
 */

(function($) {
    "use strict";

    // Initialize when the document is ready
    $(document).ready(function() {
        // Check if we're on the form page and have an order ID parameter
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get('zamowienie');
        console.log(orderId);
        if (orderId) {
            console.log('Order ID detected:', orderId);
            
            // First show a loading message
            showFormLoadingMessage();
            
            // Fetch the order data
            fetchOrderData(orderId)
                .then(function(data) {
                    if (data && data.success) {
                        // Pre-populate the form
                        populateForm(data.data);
                        
                        // Go to step 2
                        setTimeout(function() {
                            goToStep(2);
                            hideFormLoadingMessage();
                        }, 500);
                    } else {
                        console.error('Failed to load order data:', data ? data.data.message : 'Unknown error');
                        hideFormLoadingMessage();
                    }
                })
                .catch(function(error) {
                    console.error('Error loading order data:', error);
                    hideFormLoadingMessage();
                });
        }
    });

    /**
     * Show a loading message while we fetch the data
     */
    function showFormLoadingMessage() {
        const loadingHtml = '<div id="serwis-natu-loading" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(255,255,255,0.8); z-index: 999; display: flex; align-items: center; justify-content: center;">' +
                            '<div style="text-align: center; padding: 20px; background-color: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 5px;">' +
                            '<p style="margin: 0; font-size: 16px;">Wczytywanie danych zamówienia...</p>' +
                            '</div></div>';
        
        $('.serwis-natu-form-container').css('position', 'relative').append(loadingHtml);
    }

    /**
     * Hide the loading message
     */
    function hideFormLoadingMessage() {
        $('#serwis-natu-loading').fadeOut(300, function() {
            $(this).remove();
        });
    }

    /**
     * Fetch order data via AJAX
     */
    function fetchOrderData(orderId) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                url: serwisNatuFormPopulate.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_order_data_for_form',
                    order_id: orderId,
                    nonce: serwisNatuFormPopulate.nonce
                },
                success: function(response) {
                    resolve(response);
                },
                error: function(xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    /**
     * Populate the form fields with the retrieved data
     */
    function populateForm(data) {
        console.log('Populating form with data:', data);
        
        // Set Tryb współpracy (cooperative mode)
        const modeMapping = {
            'Jednorazowa usługa serwisowa': 'jednorazowa',
            'jednorazowa': 'jednorazowa',
            'Pakiet wielorazowy (abonamentowy)': 'wielorazowy',
            'wielorazowa': 'wielorazowy', // In case there's a naming discrepancy
            'Usługi dodatkowe': 'dodatkowe',
            'dodatkowa': 'dodatkowe' // In case there's a naming discrepancy
        };
        
        const cooperativeMode = data.form_settings.cooperative_mode;
        const modeValue = modeMapping[cooperativeMode] || 'jednorazowa';
        
        // Set the radio button for cooperative mode
        $(`input[name="tryb_wspolpracy"][value="${modeValue}"]`).prop('checked', true).trigger('change');
        
        // Calculate the number of aquariums based on the actual data
        const aquariumCount = data.aquariums ? Object.keys(data.aquariums).length : 1;
        console.log('Number of aquariums detected:', aquariumCount);
        
        // Select the appropriate radio button based on aquarium count
        if (aquariumCount === 1) {
            $('#ilosc_1').prop('checked', true).trigger('change');
        } else if (aquariumCount === 2) {
            $('#ilosc_2').prop('checked', true).trigger('change');
        } else if (aquariumCount >= 3 && aquariumCount <= 10) {
            $('#ilosc_wiecej').prop('checked', true).trigger('change');
            // Wait a moment for the "wiecej" field to appear
            setTimeout(() => {
                $('#ilosc_wiecej_input').val(aquariumCount).trigger('change');
            }, 200);
        }
        
        // Now configure each aquarium
        if (data.aquariums && Object.keys(data.aquariums).length > 0) {
            // Wait for the aquarium sections to be created based on the radio button selection
            setTimeout(() => {
                $.each(data.aquariums, function(index, aquarium) {
                    // We need to make sure the aquarium sections are created
                    ensureAquariumSectionsExist(aquariumCount).then(() => {
                        populateAquariumSection(index, aquarium);
                    });
                });
            }, 500); // Give some time for the radio button change to take effect
        }
        
        // Populate client data (for step 2)
        if (data.client_info) {
            $('#client_first_name').val(data.client_info.client_first_name);
            $('#client_last_name').val(data.client_info.client_last_name);
            $('#client_email').val(data.client_info.client_email);
            $('#client_phone').val(data.client_info.client_phone);
            $('#aquarium_address').val(data.client_info.aquarium_address);
        }
    }
    
    /**
     * Ensure that the required number of aquarium sections exist in the DOM
     */
    function ensureAquariumSectionsExist(count) {
        return new Promise((resolve) => {
            console.log(`Ensuring ${count} aquarium sections exist...`);
            
            // Check if we have enough aquarium sections
            const existingSections = $('.akwarium-section').length;
            console.log(`Currently have ${existingSections} aquarium sections`);
            
            if (existingSections >= count) {
                console.log('Required aquarium sections already exist');
                resolve();
                return;
            }
            
            // If we need to wait for sections to be created, check periodically
            const checkInterval = setInterval(() => {
                const currentSections = $('.akwarium-section').length;
                console.log(`Waiting... Now have ${currentSections}/${count} aquarium sections`);
                
                if (currentSections >= count) {
                    console.log('All required aquarium sections now exist');
                    clearInterval(checkInterval);
                    resolve();
                }
            }, 100);
            
            // Set a timeout to resolve anyway after a reasonable time
            setTimeout(() => {
                const finalSections = $('.akwarium-section').length;
                console.log(`Timeout reached. Have ${finalSections}/${count} aquarium sections. Continuing anyway.`);
                clearInterval(checkInterval);
                resolve();
            }, 5000); // Extended timeout to 5 seconds
        });
    }
    
    /**
     * Populate a specific aquarium section with data
     */
    function populateAquariumSection(index, aquariumData) {
        const sectionId = `#akwarium-${index}`;
        let $section = $(sectionId);
        
        if (!$section.length) {
            console.warn(`Aquarium section ${index} not found with selector "${sectionId}"`);
            
            // Try alternative selectors
            const alternativeSelectors = [
                `.akwarium-section:eq(${parseInt(index) - 1})`, // zero-based index
                `#akw${index}`,
                `.akwarium-section[data-index="${index}"]`
            ];
            
            for (const selector of alternativeSelectors) {
                const altSection = $(selector);
                if (altSection.length) {
                    console.log(`Found aquarium section using alternative selector: ${selector}`);
                    $section = altSection;
                    break;
                }
            }
            
            if (!$section.length) {
                console.error(`Could not find aquarium section ${index} with any selector. Aborting population.`);
                return;
            }
        }
        
        console.log(`Populating aquarium section ${index}:`, aquariumData);
        
        // Set tank type
        if (aquariumData.typ) {
            const tankTypes = Array.isArray(aquariumData.typ) 
                ? aquariumData.typ 
                : [aquariumData.typ];
                
            console.log(`Setting tank types for aquarium ${index}:`, tankTypes);
            
            // Get all tank type checkboxes in this section to find matching ones
            $section.find('input[name^="akw"][name$="[typ][]"]').each(function() {
                const $checkbox = $(this);
                const value = $checkbox.val();
                const dataText = $checkbox.attr('data-text');
                
                // Try to match by value or data-text
                tankTypes.forEach(type => {
                    if (
                        value.toLowerCase() === type.toLowerCase() || 
                        (dataText && dataText.toLowerCase().includes(type.toLowerCase()))
                    ) {
                        $checkbox.prop('checked', true).trigger('change');
                        console.log(`Set tank type "${type}" using checkbox with value "${value}"`);
                    }
                });
            });
        }
        
        // Set purpose (cel)
        if (aquariumData.cel) {
            const purposes = Array.isArray(aquariumData.cel) 
                ? aquariumData.cel 
                : [aquariumData.cel];
                
            console.log(`Setting purposes for aquarium ${index}:`, purposes);
            
            // Get all purpose checkboxes in this section to find matching ones
            $section.find('input[name^="akw"][name$="[cel][]"]').each(function() {
                const $checkbox = $(this);
                const value = $checkbox.val();
                const dataText = $checkbox.attr('data-text');
                
                // Try to match by value or data-text
                purposes.forEach(purpose => {
                    if (
                        value.toLowerCase() === purpose.toLowerCase() || 
                        (dataText && dataText.toLowerCase().includes(purpose.toLowerCase()))
                    ) {
                        $checkbox.prop('checked', true).trigger('change');
                        console.log(`Set purpose "${purpose}" using checkbox with value "${value}"`);
                    }
                });
            });
        }
        
        // Set scope (zakres)
        if (aquariumData.zakres) {
            const scopes = Array.isArray(aquariumData.zakres) 
                ? aquariumData.zakres 
                : [aquariumData.zakres];
                
            console.log(`Setting scopes for aquarium ${index}:`, scopes);
            
            // Get all scope checkboxes in this section to find matching ones
            $section.find('input[name^="akw"][name$="[zakres][]"]').each(function() {
                const $checkbox = $(this);
                const value = $checkbox.val();
                const dataText = $checkbox.attr('data-text');
                
                // Try to match by value or data-text
                scopes.forEach(scope => {
                    if (
                        value.toLowerCase() === scope.toLowerCase() || 
                        (dataText && dataText.toLowerCase().includes(scope.toLowerCase()))
                    ) {
                        $checkbox.prop('checked', true).trigger('change');
                        console.log(`Set scope "${scope}" using checkbox with value "${value}"`);
                    }
                });
            });
        }
        
        // Set additional services (Dodatkowe usługi)
        if (aquariumData['Dodatkowe usługi']) {
            const additionalServices = Array.isArray(aquariumData['Dodatkowe usługi'])
                ? aquariumData['Dodatkowe usługi']
                : [aquariumData['Dodatkowe usługi']];
                
            console.log(`Setting additional services for aquarium ${index}:`, additionalServices);
            
            // Based on the data structure and HTML, we should look for matching values in all form fields
            // Since "Dodatkowe usługi" might map to various fields, we'll try to match with any checkbox
            additionalServices.forEach(service => {
                // Match "Badanie wody" to "badanie" checkbox
                let matchFound = false;
                
                if (service.toLowerCase().includes("badanie")) {
                    const $badanieCheckbox = $section.find('input[value="badanie"]');
                    if ($badanieCheckbox.length) {
                        $badanieCheckbox.prop('checked', true).trigger('change');
                        console.log(`Set additional service "${service}" using "badanie" checkbox`);
                        matchFound = true;
                    }
                }
                
                // Try to find any checkbox that has matching text
                if (!matchFound) {
                    $section.find('input[type="checkbox"]').each(function() {
                        const $checkbox = $(this);
                        const value = $checkbox.val();
                        const dataText = $checkbox.attr('data-text') || '';
                        const labelText = $checkbox.next('label').text() || '';
                        
                        if (
                            value.toLowerCase().includes(service.toLowerCase()) ||
                            dataText.toLowerCase().includes(service.toLowerCase()) ||
                            labelText.toLowerCase().includes(service.toLowerCase())
                        ) {
                            $checkbox.prop('checked', true).trigger('change');
                            console.log(`Set additional service "${service}" using checkbox with value "${value}"`);
                            matchFound = true;
                            return false; // Break the each loop
                        }
                    });
                }
                
                if (!matchFound) {
                    console.warn(`Could not find matching checkbox for additional service "${service}" in aquarium ${index}`);
                }
            });
        }
        
        // Set other information (inne)
        if (aquariumData.inne) {
            console.log(`Setting other information for aquarium ${index}:`, aquariumData.inne);
            
            const inneValues = Array.isArray(aquariumData.inne) 
                ? aquariumData.inne 
                : [aquariumData.inne];
                
            // Looking at step-1.php, "inne" are checkboxes
            $section.find('input[name^="akw"][name$="[inne][]"]').each(function() {
                const $checkbox = $(this);
                const value = $checkbox.val();
                const dataText = $checkbox.attr('data-text');
                
                // Try to match by value or data-text
                inneValues.forEach(inneValue => {
                    if (
                        value.toLowerCase() === inneValue.toLowerCase() || 
                        (dataText && dataText.toLowerCase().includes(inneValue.toLowerCase()))
                    ) {
                        $checkbox.prop('checked', true).trigger('change');
                        console.log(`Set other information "${inneValue}" using checkbox with value "${value}"`);
                    }
                });
            });
        }
    }
    
    /**
     * Navigate to a specific step in the form
     */
    function goToStep(stepNumber) {
        // Use the existing form navigation if it exists
        if (typeof navigateToStep === 'function') {
            navigateToStep(stepNumber);
            return;
        }
        
        // Fallback navigation implementation
        $('.serwis-natu-step').removeClass('active');
        $('#step-' + stepNumber).addClass('active');
        
        // Update progress indicators
        $('.sa-progress-item').removeClass('active completed');
        
        for (let i = 1; i <= stepNumber; i++) {
            if (i < stepNumber) {
                $('.sa-progress-item[data-step="' + i + '"]').addClass('completed');
            } else {
                $('.sa-progress-item[data-step="' + i + '"]').addClass('active');
            }
        }
        
        // Scroll to top of the form
        $('html, body').animate({
            scrollTop: $('.serwis-natu-form-container').offset().top - 100
        }, 500);
    }

})(jQuery);
