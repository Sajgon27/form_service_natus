/**
 * Serwis Natu form scripts
 */

(function($) {
    'use strict';
    
    // Initialize the form when the document is ready
    $(document).ready(function() {
        initForm();
    });
    
    /**
     * Initialize the form functionality
     */
    function initForm() {
        // Make sure our template is working properly - if dedicated template is missing, 
        // create a backup from the existing section
        if ($('#template-akwarium').length === 0 && $('#akwarium-1').length > 0) {
            console.log('No dedicated template found, creating one from existing section');
            $('<div id="template-akwarium" style="display:none;"></div>')
                .append($('#akwarium-1').clone().attr('id', 'akwarium-template'))
                .appendTo('body');
        }
        
        // Log if we found the template
        if ($('#template-akwarium').length > 0) {
            console.log('Template found and ready for use');
        } else if ($('#akwarium-1').length > 0) {
            console.log('Using first aquarium section as template');
        } else {
            console.error('No template or existing aquarium section found!');
        }
        
        setupTooltips();
        setupAquariumCount();
        setupServiceMode();
        setupFileUploadFields();
        setupStepNavigation();
        setupRecommendedProducts();
    }
    
    /**
     * Setup tooltips functionality
     */
    function setupTooltips() {
        // Clear existing tooltip contents first to prevent duplicates
        $('.tooltip-content').remove();
        
        $('.tooltip-icon').each(function() {
            const tooltipKey = $(this).data('tooltip');
            
            // Get tooltip text from localized data
            if (window.serwisNatuData && serwisNatuData.tooltips && serwisNatuData.tooltips[tooltipKey]) {
                const tooltipText = serwisNatuData.tooltips[tooltipKey];
                
                // Create tooltip content element
                const tooltipContent = $('<span class="tooltip-content"></span>').text(tooltipText);
                $(this).append(tooltipContent);
            } else {
                // Fallback tooltips if data not provided by WordPress
                const fallbackTooltips = {
                    'jednorazowa_usluga': 'Jednorazowa usługa serwisowa bez zobowiązań',
                    'pakiet_wielorazowy': 'Pakiet z regularnymi wizytami serwisowymi',
                    'uslugi_dodatkowe': 'Dodatkowe usługi dla akwarium',
                    'lowtech': 'Zbiorniki bez CO2 i intensywnego oświetlenia',
                    'hightech': 'Zbiorniki z CO2 i intensywnym oświetleniem',
                    'biotopowy': 'Zbiorniki biotopowe odwzorowujące naturalne środowiska'
                };
                
                const tooltipText = fallbackTooltips[tooltipKey] || 'Informacja pomocnicza';
                const tooltipContent = $('<span class="tooltip-content"></span>').text(tooltipText);
                $(this).append(tooltipContent);
            }
        });
    }
    
    /**
     * Setup aquarium count functionality
     */
    function setupAquariumCount() {
        // Show/hide "więcej" input field when selected
        $('input[name="ilosc_akwarium"]').change(function() {
            if ($(this).val() === 'wiecej') {
                $('#wiecej_container').removeClass('hidden').slideDown(200);
            } else {
                $('#wiecej_container').slideUp(200);
            }
            
            // Update aquarium sections based on selection
            updateAquariumSections();
        });
        
        // Listen for changes on the "więcej" input
        $('#ilosc_wiecej_input').on('input change', function() {
            const value = parseInt($(this).val()) || 3;
            // Enforce min/max values
            if (value < 3) $(this).val(3);
            if (value > 10) $(this).val(10);
            
            updateAquariumSections();
        });
        
        // Make sure the "więcej" container is initially hidden
        if ($('input[name="ilosc_akwarium"]:checked').val() !== 'wiecej') {
            $('#wiecej_container').addClass('hidden');
        }
        
        // Initialize aquarium sections (default to 1)
        updateAquariumSections();
    }
    
    /**
     * Update aquarium sections based on selected count
     */
    function updateAquariumSections() {
        const $container = $('.aquariums-container');
        let count = 1; // Default value
        
        // Get selected value
        const selectedValue = $('input[name="ilosc_akwarium"]:checked').val();
        
        if (selectedValue === '1') {
            count = 1;
        } else if (selectedValue === '2') {
            count = 2;
        } else if (selectedValue === 'wiecej') {
            count = parseInt($('#ilosc_wiecej_input').val()) || 3;
            // Ensure count is within range
            count = Math.min(Math.max(count, 3), 10);
            $('#ilosc_wiecej_input').val(count);
        }
        
        // Get current sections count for comparison
        const currentCount = $container.children('.akwarium-section').length;
        
        // If count didn't change, don't rebuild everything
        if (count === currentCount) {
            return;
        }
        
        // If count decreased, just remove extra sections
        if (count < currentCount) {
            $container.children('.akwarium-section').slice(count).remove();
            return;
        }
        
        // If count increased, generate only the new sections
        for (let i = currentCount + 1; i <= count; i++) {
            const $section = generateAquariumSection(i);
            $container.append($section);
            
            // Add a fade-in animation class
            $section.addClass('fade-in-section');
        }
        
        // Re-initialize tooltips for new sections
        setupTooltips();
        
        // Re-initialize service mode to ensure correct visibility
        updateServiceModeVisibility();
        
        // Add animation class to new sections
        $container.find('.akwarium-section').addClass('new-section');
    }
    
    /**
     * Generate HTML for a single aquarium section
     * 
     * @param {number} index - The index of the aquarium
     * @return {jQuery} The jQuery object for the aquarium section
     */
    function generateAquariumSection(index) {
        // Try to get the template from our dedicated template container
        let templateSection = document.querySelector('#template-akwarium #akwarium-template');
        
        // If dedicated template not found, try the existing section
        if (!templateSection) {
            templateSection = document.querySelector('#akwarium-1');
        }
        
        // If still no template, log an error
        if (!templateSection) {
            console.error('Aquarium template not found! Make sure the template exists in the DOM.');
            return $('<div id="akwarium-' + index + '" class="akwarium-section">Error loading template</div>');
        }
        
        // Clone the template
        let clonedSection = templateSection.cloneNode(true);
        
        // Update the ID
        clonedSection.id = 'akwarium-' + index;
        
        // Update the heading
        let heading = clonedSection.querySelector('h3');
        if (heading) {
            const headingText = heading.innerHTML;
            heading.innerHTML = headingText.replace(/\d+/, index);
        }
        
        // Update all input IDs and names
        let inputs = clonedSection.querySelectorAll('input[type="checkbox"], input[type="radio"]');
        inputs.forEach(function(input) {
            // Update ID
            if (input.id) {
                input.id = input.id.replace('akw1_', 'akw' + index + '_');
            }
            
            // Update name attribute
            if (input.name) {
                input.name = input.name.replace('akw[1]', 'akw[' + index + ']');
            }
            
            // Make sure checkbox is unchecked in the clone
            input.checked = false;
        });
        
        // Update all label for attributes
        let labels = clonedSection.querySelectorAll('label');
        labels.forEach(function(label) {
            if (label.htmlFor) {
                label.htmlFor = label.htmlFor.replace('akw1_', 'akw' + index + '_');
            }
        });
        
        // Update tooltip IDs too
        let tooltips = clonedSection.querySelectorAll('.tooltip-icon');
        tooltips.forEach(function(tooltip) {
            if (tooltip.dataset.tooltip) {
                // We don't need to change these as they reference global tooltips
            }
        });
        
        // Convert to jQuery object and make sure classes are correct
        let $section = $(clonedSection);
        $section.removeClass('hidden');
        $section.addClass('akwarium-section');
        
        return $section;
    }
    
    /**
     * Setup service mode functionality
     */
    function setupServiceMode() {
        $('input[name="tryb_wspolpracy"]').change(function() {
            updateServiceModeVisibility();
        });
        
        // Initialize visibility based on default selection
        updateServiceModeVisibility();
    }
    
    /**
     * Update form sections visibility based on selected service mode
     */
    function updateServiceModeVisibility() {
        const selectedMode = $('input[name="tryb_wspolpracy"]:checked').val();
        
        // Show/hide sections based on service mode
        if (selectedMode === 'dodatkowe') {
            // If "Usługi dodatkowe" selected, only show "Inne potrzeby" checkboxes
            $('.akwarium-section .subsection').each(function() {
                const $subsection = $(this);
                if (!$subsection.hasClass('inne-potrzeby')) {
                    // Hide the section
                    $subsection.hide();
                    
                    // Disable all checkboxes in hidden sections
                    $subsection.find('input[type="checkbox"]').prop('disabled', true);
                } else {
                    // Show the "Inne potrzeby" section
                    $subsection.show();
                    
                    // Enable checkboxes in "Inne potrzeby" section
                    $subsection.find('input[type="checkbox"]').prop('disabled', false);
                }
            });
        } else {
            // Show all sections for other service modes
            $('.akwarium-section .subsection').show();
            
            // Enable all checkboxes
            $('.akwarium-section input[type="checkbox"]').prop('disabled', false);
        }
    }
    
    /**
     * Setup file upload fields
     */
    function setupFileUploadFields() {
        // Update photo upload fields based on aquarium count from step 1
        updatePhotoUploadFields();
        
        // Handle file upload preview
        $(document).on('change', '.file-upload', function() {
            const fileInput = this;
            const previewContainer = $(this).siblings('.file-preview');
            
            // Clear previous preview
            previewContainer.html('');
            
            // Check if a file is selected
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const img = $('<img>').attr('src', e.target.result)
                        .addClass('file-preview-image');
                    previewContainer.append(img);
                    
                    // Add remove button
                    const removeBtn = $('<button>').addClass('remove-file-btn')
                        .html('&times;')
                        .attr('type', 'button')
                        .on('click', function() {
                            // Clear the file input and preview
                            fileInput.value = '';
                            previewContainer.html('');
                        });
                    
                    previewContainer.append(removeBtn);
                };
                
                reader.readAsDataURL(fileInput.files[0]);
            }
        });
    }
    
    /**
     * Update photo upload fields based on aquarium count from step 1
     */
    function updatePhotoUploadFields() {
        const $container = $('.aquarium-photos-container');
        let count = 1; // Default value
        
        // Get selected aquarium count from step 1
        const selectedValue = $('input[name="ilosc_akwarium"]:checked').val();
        
        if (selectedValue === '1') {
            count = 1;
        } else if (selectedValue === '2') {
            count = 2;
        } else if (selectedValue === 'wiecej') {
            count = parseInt($('#ilosc_wiecej_input').val()) || 3;
            // Ensure count is within range
            count = Math.min(Math.max(count, 3), 10);
        }
        
        // Get current upload fields count
        const currentCount = $container.children('.photo-upload-row').length;
        
        // If count didn't change, don't rebuild everything
        if (count === currentCount) {
            return;
        }
        
        // If count decreased, just remove extra upload fields
        if (count < currentCount) {
            $container.children('.photo-upload-row').slice(count).remove();
            return;
        }
        
        // If count increased, add more upload fields
        for (let i = currentCount + 1; i <= count; i++) {
            const $uploadField = $(`
                <div class="form-row photo-upload-row" id="photo-upload-${i}">
                    <div class="form-field">
                        <label for="aquarium_photo_${i}">Zdjęcie akwarium ${i}</label>
                        <input type="file" id="aquarium_photo_${i}" name="aquarium_photo_${i}" accept="image/*" class="file-upload">
                        <div class="file-preview"></div>
                    </div>
                </div>
            `);
            
            $container.append($uploadField);
        }
    }

    /**
     * Setup step navigation
     */
    function setupStepNavigation() {
        // Step 1 to Step 2
        $('#next-step-1').click(function(e) {
            e.preventDefault();
            
            // Make sure service mode visibility is updated before validation
            updateServiceModeVisibility();
            
            // Validate step 1
            if (validateStep1()) {
                // Update photo upload fields before showing step 2
                updatePhotoUploadFields();
                goToStep(2);
            }
        });
        
        // Step 2 navigation
        $('#prev-step-2').click(function(e) {
            e.preventDefault();
            goToStep(1);
        });
        
        $('#next-step-2').click(function(e) {
            e.preventDefault();
            if (validateStep2()) {
                // Generate package recommendations before showing step 3
                generatePackageRecommendations();
                goToStep(3);
            }
        });
        
        // Step 3 navigation
        $('#prev-step-3').click(function(e) {
            e.preventDefault();
            goToStep(2);
        });
        
        $('#next-step-3').click(function(e) {
            e.preventDefault();
            goToStep(4);
        });
        
        // Step 4 navigation
        $('#prev-step-4').click(function(e) {
            e.preventDefault();
            goToStep(3);
        });
        
        // Submit form
        $('#submit-form').click(function(e) {
            // This will be implemented later
            // Will submit the form when step 4 is implemented
            console.log('Form submitted');
        });
    }
    
    /**
     * Navigate to a specific step
     *
     * @param {number} stepNumber - The step number to navigate to
     */
    function goToStep(stepNumber) {
        // Hide all steps
        $('.serwis-natu-step').removeClass('active');
        
        // Show the target step
        $('#step-' + stepNumber).addClass('active');
        
        // Update progress bar
        $('.progress-step').removeClass('active completed');
        
        // Mark current step as active
        $('.progress-step[data-step="' + stepNumber + '"]').addClass('active');
        
        // Mark previous steps as completed
        for (let i = 1; i < stepNumber; i++) {
            $('.progress-step[data-step="' + i + '"]').addClass('completed');
        }
        
        // Scroll to top of form
        $('html, body').animate({
            scrollTop: $('.serwis-natu-form-container').offset().top - 50
        }, 300);
    }
    
    /**
     * Validate step 1 form data
     * 
     * @return {boolean} Whether the validation passed
     */
    function validateStep1() {
        const $errorMessage = $('#step1-error');
        let isValid = true;
        
        // Get selected service mode
        const selectedMode = $('input[name="tryb_wspolpracy"]:checked').val();
        
        // Count visible sections and checkboxes
        $('.akwarium-section').each(function() {
            const $section = $(this);
            let hasSelection = false;
            
            if (selectedMode === 'dodatkowe') {
                // For "Usługi dodatkowe" mode, check only "Inne potrzeby" subsections
                const $visibleSubsection = $section.find('.subsection.inne-potrzeby');
                
                // Check if any checkbox is selected in this subsection
                $visibleSubsection.find('input[type="checkbox"]:not(:disabled)').each(function() {
                    if ($(this).is(':checked')) {
                        hasSelection = true;
                    }
                });
                
                if (!hasSelection && $visibleSubsection.is(':visible')) {
                    isValid = false;
                }
            } else {
                // For other modes, check all visible sections
                // Check if any checkbox is selected in visible subsections
                let hasVisibleSubsection = false;
                
                $section.find('.subsection:visible').each(function() {
                    hasVisibleSubsection = true;
                    
                    $(this).find('input[type="checkbox"]:not(:disabled)').each(function() {
                        if ($(this).is(':checked')) {
                            hasSelection = true;
                        }
                    });
                });
                
                if (!hasSelection && hasVisibleSubsection) {
                    isValid = false;
                }
            }
        });
        
        if (!isValid) {
            $errorMessage.slideDown(200);
        } else {
            $errorMessage.slideUp(200);
        }
        
        return isValid;
    }
    
    /**
     * Validate step 2 form data
     * 
     * @return {boolean} Whether the validation passed
     */
    function validateStep2() {
        const $errorMessage = $('#step2-error');
        let isValid = true;
        
        // Required fields validation
        const requiredFields = [
            'client_first_name',
            'client_last_name',
            'client_email',
            'client_phone',
            'aquarium_address',
            'preferred_date'
        ];
        
        // Check each required field
        requiredFields.forEach(function(fieldId) {
            const $field = $('#' + fieldId);
            if (!$field.val().trim()) {
                isValid = false;
                $field.addClass('error');
            } else {
                $field.removeClass('error');
            }
        });
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const $emailField = $('#client_email');
        if ($emailField.val().trim() && !emailRegex.test($emailField.val().trim())) {
            isValid = false;
            $emailField.addClass('error');
        }
        
        // Phone validation - allow only numbers, spaces, and basic phone characters
        const phoneRegex = /^[0-9\s\+\(\)\-]+$/;
        const $phoneField = $('#client_phone');
        if ($phoneField.val().trim() && !phoneRegex.test($phoneField.val().trim())) {
            isValid = false;
            $phoneField.addClass('error');
        }
        
        // Display error message if validation failed
        if (!isValid) {
            $errorMessage.slideDown(200);
        } else {
            $errorMessage.slideUp(200);
        }
        
        return isValid;
    }
    
    /**
     * Generate package recommendations for step 3
     */
    function generatePackageRecommendations() {
        const $container = $('#package-recommendations');
        const $costSummary = $('#cost-summary');
        
        // Clear previous recommendations
        $container.html('<div class="loading-spinner"><div class="spinner"></div>Przygotowujemy rekomendacje...</div>');
        $costSummary.empty();
        
        // Get form data
        const formData = getFormData();
        
        // Calculate package recommendations (always returns a Promise now)
        calculateRecommendations(formData)
            .then(function(recommendations) {
                // Display recommendations
                displayRecommendations(recommendations);
                
                // Calculate and display cost summary
                displayCostSummary(recommendations);
            })
            .catch(function(error) {
                // Display error message
                $container.html('<div class="error-message"><p>' + error.message + '</p><p>Proszę spróbować ponownie lub skontaktować się z administratorem.</p></div>');
                $costSummary.html('<div class="error-message"><p>Nie można wyświetlić podsumowania kosztów.</p></div>');
                console.error('Error getting recommendations:', error);
            });
    }
    
    /**
     * Collect all form data
     * 
     * @return {object} Form data
     */
    function getFormData() {
        const formData = {
            tryb_wspolpracy: $('input[name="tryb_wspolpracy"]:checked').val() || 'jednorazowa',
            ilosc_akwarium: $('input[name="ilosc_akwarium"]:checked').val() || '1',
            ilosc_wiecej: $('#ilosc_wiecej_input').val() || '3',
            akw: {}
        };
        
        // Determine number of aquariums
        let aquariumCount = 1;
        if (formData.ilosc_akwarium === '1') {
            aquariumCount = 1;
        } else if (formData.ilosc_akwarium === '2') {
            aquariumCount = 2;
        } else if (formData.ilosc_akwarium === 'wiecej') {
            aquariumCount = parseInt(formData.ilosc_wiecej) || 3;
            aquariumCount = Math.max(3, Math.min(10, aquariumCount)); // Ensure between 3 and 10
        }
        
        // Collect data for each aquarium
        for (let i = 1; i <= aquariumCount; i++) {
            formData.akw[i] = {
                typ: [],
                cel: [],
                zakres: [],
                inne: []
            };
            
            // Collect checkbox values for each category
            $('input[name="akw[' + i + '][typ][]"]:checked').each(function() {
                formData.akw[i].typ.push($(this).val());
            });
            
            $('input[name="akw[' + i + '][cel][]"]:checked').each(function() {
                formData.akw[i].cel.push($(this).val());
            });
            
            $('input[name="akw[' + i + '][zakres][]"]:checked').each(function() {
                formData.akw[i].zakres.push($(this).val());
            });
            
            $('input[name="akw[' + i + '][inne][]"]:checked').each(function() {
                formData.akw[i].inne.push($(this).val());
            });
        }
        
        return formData;
    }
    
    /**
     * Calculate package recommendations
     * 
     * @param {object} formData Form data
     * @return {Promise} Promise that resolves with recommendations
     */
    function calculateRecommendations(formData) {
        // Always get recommendations via AJAX from admin settings
        if (!window.serwisNatuData) {
            window.serwisNatuData = {};
            console.warn('serwisNatuData object not found, creating fallback');
        }
        
        // If ajaxurl isn't set, try to use the WordPress admin-ajax URL
        if (!serwisNatuData.ajaxurl) {
            // Check if we can find WordPress admin URL in the page
            const adminAjaxUrl = '/wp-admin/admin-ajax.php';
            serwisNatuData.ajaxurl = adminAjaxUrl;
            console.warn('AJAX URL not found in serwisNatuData, using fallback: ' + adminAjaxUrl);
        }
        
        // Create a nonce if it doesn't exist
        if (!serwisNatuData.nonce) {
            serwisNatuData.nonce = '';
            console.warn('Nonce not found in serwisNatuData');
        }
        
        return getRecommendationsViaAjax(formData);
    }
    
    /**
     * Get recommendations via AJAX
     *
     * @param {object} formData Form data
     * @return {Promise} Promise that resolves with recommendations
     */
    function getRecommendationsViaAjax(formData) {
        return new Promise(function(resolve, reject) {
            // Make sure we have a valid URL
            let ajaxUrl = serwisNatuData.ajaxurl || '/wp-admin/admin-ajax.php';
            
            // Ensure ajaxUrl is an absolute URL if it doesn't start with http or /
            if (!ajaxUrl.startsWith('http') && !ajaxUrl.startsWith('/')) {
                ajaxUrl = '/' + ajaxUrl;
            }
            
            // Get or create a nonce
            let nonce = '';
            if (serwisNatuData && serwisNatuData.nonce) {
                nonce = serwisNatuData.nonce;
            } else {
                // Try to get nonce from the page
                const nonceField = document.querySelector('input[name="_wpnonce"]');
                if (nonceField) {
                    nonce = nonceField.value;
                }
            }
            
            console.log('Sending AJAX request to:', ajaxUrl);
            console.log('Form data being sent:', formData);
            
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    action: 'get_package_recommendations',
                    nonce: nonce,
                    form_data: formData
                },
                success: function(response) {
                    console.log('AJAX response:', response);
                    if (response.success && response.data && response.data.recommendations) {
                        resolve(response.data.recommendations);
                    } else {
                        const errorMessage = response.data && response.data.message ? 
                            response.data.message : 'Nie udało się uzyskać rekomendacji pakietów.';
                        console.error('Server error response:', response);
                        reject(new Error(errorMessage));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', { xhr, status, error });
                    console.error('Response text:', xhr.responseText);
                    reject(new Error('Błąd komunikacji z serwerem: ' + (error || status)));
                }
            });
        });
    }
    
    /* 
     * Local recommendations function has been removed.
     * All recommendations are now retrieved exclusively from the admin panel via AJAX.
     */
    
    /**
     * Display package recommendations
     * 
     * @param {array} recommendations Package recommendations
     */
    function displayRecommendations(recommendations) {
        const $container = $('#package-recommendations');
        $container.empty();
        
        if (!recommendations || recommendations.length === 0) {
            $container.html('<p>Nie udało się wygenerować rekomendacji.</p>');
            return;
        }
        
        recommendations.forEach(function(recommendation) {
            const $recommendation = $(`
                <div class="package-recommendation" data-aquarium="${recommendation.aquariumIndex}" data-package="${recommendation.packageKey}">
                    <div class="package-header">
                        <span class="package-name">Akwarium ${recommendation.aquariumIndex} - ${recommendation.packageName}</span>
                        <span class="package-price">${recommendation.packagePrice} zł</span>
                    </div>
                    <div class="package-description">
                        ${recommendation.packageDescription}
                    </div>
                </div>
            `);
            
            $container.append($recommendation);
        });
    }
    
    /**
     * Display cost summary
     * 
     * @param {array} recommendations Package recommendations
     */
    function displayCostSummary(recommendations) {
        const $costSummary = $('#cost-summary');
        $costSummary.empty();
        
        if (!recommendations || recommendations.length === 0) {
            $costSummary.html('<p>Nie udało się przygotować podsumowania kosztów.</p>');
            return;
        }
        
        // Calculate total cost
        let totalCost = 0;
        const costItems = [];
        
        recommendations.forEach(function(recommendation) {
            costItems.push({
                label: `Akwarium ${recommendation.aquariumIndex} - ${recommendation.packageName}`,
                price: recommendation.packagePrice
            });
            
            totalCost += recommendation.packagePrice;
        });
        
        // Build HTML
        const $costItemsContainer = $('<div class="cost-items"></div>');
        
        costItems.forEach(function(item) {
            const $costItem = $(`
                <div class="cost-item">
                    <span class="cost-item-label">${item.label}</span>
                    <span class="cost-item-price">${item.price} zł</span>
                </div>
            `);
            
            $costItemsContainer.append($costItem);
        });
        
        const $totalCost = $(`
            <div class="total-cost">
                <span>Razem:</span>
                <span>${totalCost} zł</span>
            </div>
        `);
        
        $costSummary.append($costItemsContainer);
        $costSummary.append($totalCost);
    }
    
    /**
     * Setup recommended products functionality
     */
    function setupRecommendedProducts() {
        // Handle the checkbox change event
        $('#recommended_products_checkbox').on('change', function() {
            const $container = $('#recommended-products-container');
            
            if ($(this).is(':checked')) {
                $container.slideDown(300);
                loadRecommendedProducts();
            } else {
                $container.slideUp(300);
            }
        });
    }
    
    /**
     * Load recommended products based on form selections
     */
    function loadRecommendedProducts() {
        const $container = $('#recommended-products-container');
        const $productsList = $('#recommended-products-list');
        
        // Show loading spinner
        $container.show();
        $productsList.hide();
        $('.loading-spinner', $container).show();
        
        // Get form data
        const formData = getFormData();
        
        console.log('serwisNatuData object:', serwisNatuData);
        
        // Get the appropriate AJAX URL and nonce
        const ajaxUrl = serwisNatuData.ajaxurl || '/wp-admin/admin-ajax.php';
        // Try multiple possible nonce key names
        const nonce = serwisNatuData.ajaxNonce || serwisNatuData.nonce || '';
        
        console.log('Using AJAX URL:', ajaxUrl);
        console.log('Form data to be sent:', formData);
        
        // Send AJAX request to get recommended products
        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: 'get_recommended_products',
                nonce: nonce,
                form_data: formData
            },
            success: function(response) {
                console.log('Recommended products AJAX response:', response);
                if (response && response.success && response.data && response.data.products) {
                    displayRecommendedProducts(response.data.products);
                } else {
                    console.error('Invalid AJAX response structure:', response);
                    displayNoProductsMessage('Niepoprawna odpowiedź z serwera.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading recommended products:', error);
                console.error('XHR status:', status);
                console.error('XHR response text:', xhr.responseText);
                
                // Try to parse error response if it's JSON
                let errorMessage = 'Wystąpił błąd podczas ładowania rekomendowanych produktów.';
                try {
                    const jsonResponse = JSON.parse(xhr.responseText);
                    if (jsonResponse && jsonResponse.data && jsonResponse.data.message) {
                        errorMessage = jsonResponse.data.message;
                    }
                } catch(e) {
                    // Not JSON or parsing failed, use default message
                }
                
                displayNoProductsMessage(errorMessage);
            },
            complete: function() {
                // Hide loading spinner
                $('.loading-spinner', $container).hide();
                $productsList.show();
            }
        });
    }
    
    /**
     * Display recommended products in a table
     * 
     * @param {Array} products Array of product objects
     */
    function displayRecommendedProducts(products) {
        const $productsList = $('#recommended-products-list');
        $productsList.empty();
        
        console.log('Displaying recommended products:', products);
        
        if (!products || products.length === 0) {
            displayNoProductsMessage();
            return;
        }
        
        // Create table
        const $table = $('<table class="recommended-products-table"></table>');
        const $thead = $('<thead></thead>');
        const $tbody = $('<tbody></tbody>');
        
        // Add table header
        $thead.append(`
            <tr>
                <th>Zdjęcie</th>
                <th>Nazwa produktu</th>
                <th>Cena</th>
            </tr>
        `);
        
        // Add products to table
        products.forEach(function(product) {
            try {
                const $row = $('<tr></tr>');
                
                // Handle possible missing data with defaults
                const imageContent = product.image || '<div class="no-image">Brak zdjęcia</div>';
                const productName = product.name || 'Produkt bez nazwy';
                const productUrl = product.url || '#';
                const productPrice = product.price || '-';
                
                $row.append(`<td>${imageContent}</td>`);
                $row.append(`<td><a href="${productUrl}" target="_blank">${productName}</a></td>`);
                $row.append(`<td>${productPrice}</td>`);
                
                $tbody.append($row);
            } catch (err) {
                console.error('Error displaying product:', err, product);
            }
        });
        
        // Assemble table
        $table.append($thead);
        $table.append($tbody);
        $productsList.append($table);
        
        // Add a helpful message
        $productsList.append('<p class="products-note">Powyższe produkty są rekomendowane na podstawie wybranych opcji formularza.</p>');
    }
    
    /**
     * Display a message when no products are available
     * 
     * @param {string} customMessage Optional custom message
     */
    function displayNoProductsMessage(customMessage) {
        const $container = $('#recommended-products-container');
        const $productsList = $('#recommended-products-list');
        
        // Hide loading spinner if it's visible
        $('.loading-spinner', $container).hide();
        
        const message = customMessage || 'Brak rekomendowanych produktów dla wybranych opcji.';
        $productsList.html(`<div class="no-products-message">${message}</div>`).show();
        
        console.log('Displaying no products message:', message);
    }
})(jQuery);
