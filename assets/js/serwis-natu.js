/**
 * Serwis Natu form scripts
 */

(function ($) {
  "use strict";

  // Initialize the form when the document is ready
  $(document).ready(function () {
    initForm();
  });

  /**
   * Initialize the form functionality
   */
  function initForm() {
    // Make sure our template is working properly - if dedicated template is missing,
    // create a backup from the existing section
    if ($("#template-akwarium").length === 0 && $("#akwarium-1").length > 0) {
      console.log(
        "No dedicated template found, creating one from existing section"
      );
      $('<div id="template-akwarium" style="display:none;"></div>')
        .append($("#akwarium-1").clone().attr("id", "akwarium-template"))
        .appendTo("body");
    }
    
    // Setup clear form button functionality
    setupClearButton();

    // Log if we found the template
    if ($("#template-akwarium").length > 0) {
      console.log("Template found and ready for use");
    } else if ($("#akwarium-1").length > 0) {
      console.log("Using first aquarium section as template");
    } else {
      console.error("No template or existing aquarium section found!");
    }

    setupTooltips();
    setupAquariumCount();
    setupServiceMode();
    setupFileUploadFields();
    setupStepNavigation();
    setupRecommendedProducts();
    setupTankTypeSelection();
  }

  /**
   * Setup tooltips functionality
   */
  function setupTooltips() {
    // Clear existing tooltip contents first to prevent duplicates
    $(".tooltip-content").remove();

    $(".tooltip-icon").each(function () {
      const tooltipKey = $(this).data("tooltip");

      // Get tooltip text from localized data
      if (
        window.serwisNatuData &&
        serwisNatuData.tooltips &&
        serwisNatuData.tooltips[tooltipKey]
      ) {
        const tooltipText = serwisNatuData.tooltips[tooltipKey];

        // Create tooltip content element
        const tooltipContent = $('<span class="tooltip-content"></span>').text(
          tooltipText
        );
        $(this).append(tooltipContent);
      } else {
        // Fallback tooltips if data not provided by WordPress
        const fallbackTooltips = {
          jednorazowa_usluga: "Jednorazowa usługa serwisowa bez zobowiązań",
          pakiet_wielorazowy: "Pakiet z regularnymi wizytami serwisowymi",
          uslugi_dodatkowe: "Dodatkowe usługi dla akwarium",
          lowtech: "Zbiorniki bez CO2 i intensywnego oświetlenia",
          hightech: "Zbiorniki z CO2 i intensywnym oświetleniem",
          biotopowy: "Zbiorniki biotopowe odwzorowujące naturalne środowiska",
        };

        const tooltipText =
          fallbackTooltips[tooltipKey] || "Informacja pomocnicza";
        const tooltipContent = $('<span class="tooltip-content"></span>').text(
          tooltipText
        );
        $(this).append(tooltipContent);
      }
    });
  }

  /**
   * Setup aquarium count functionality
   */
  function setupAquariumCount() {
    // Show/hide "więcej" input field when selected
    $('input[name="ilosc_akwarium"]').change(function () {
      if ($(this).val() === "wiecej") {
        $("#wiecej_container").removeClass("hidden").slideDown(200);
      } else {
        $("#wiecej_container").slideUp(200);
      }

      // Update aquarium sections based on selection
      updateAquariumSections();
    });

    // Listen for changes on the "więcej" input
    $("#ilosc_wiecej_input").on("input change", function () {
      const value = parseInt($(this).val()) || 3;
      // Enforce min/max values
      if (value < 3) $(this).val(3);
      if (value > 10) $(this).val(10);

      updateAquariumSections();
    });

    // Make sure the "więcej" container is initially hidden
    if ($('input[name="ilosc_akwarium"]:checked').val() !== "wiecej") {
      $("#wiecej_container").addClass("hidden");
    }

    // Initialize aquarium sections (default to 1)
    updateAquariumSections();
  }

  /**
   * Update aquarium sections based on selected count
   */
  function updateAquariumSections() {
    const $container = $(".aquariums-container");
    let count = 1; // Default value

    // Get selected value
    const selectedValue = $('input[name="ilosc_akwarium"]:checked').val();

    if (selectedValue === "1") {
      count = 1;
    } else if (selectedValue === "2") {
      count = 2;
    } else if (selectedValue === "wiecej") {
      count = parseInt($("#ilosc_wiecej_input").val()) || 3;
      // Ensure count is within range
      count = Math.min(Math.max(count, 3), 10);
      $("#ilosc_wiecej_input").val(count);
    }

    // Get current sections count for comparison
    const currentCount = $container.children(".akwarium-section").length;

    // If count didn't change, don't rebuild everything
    if (count === currentCount) {
      return;
    }

    // If count decreased, just remove extra sections
    if (count < currentCount) {
      $container.children(".akwarium-section").slice(count).remove();
      return;
    }

    // If count increased, generate only the new sections
    for (let i = currentCount + 1; i <= count; i++) {
      const $section = generateAquariumSection(i);
      $container.append($section);

      // Add a fade-in animation class
      $section.addClass("fade-in-section");
    }

    // Re-initialize tooltips for new sections
    setupTooltips();

    // Re-initialize service mode to ensure correct visibility
    updateServiceModeVisibility();

    // Apply tank type restrictions to new sections
    applyTankTypeRestrictions();

    // Add animation class to new sections
    $container.find(".akwarium-section").addClass("new-section");
  }

  /**
   * Generate HTML for a single aquarium section
   *
   * @param {number} index - The index of the aquarium
   * @return {jQuery} The jQuery object for the aquarium section
   */
  function generateAquariumSection(index) {
    // Try to get the template from our dedicated template container
    let templateSection = document.querySelector(
      "#template-akwarium #akwarium-template"
    );

    // If dedicated template not found, try the existing section
    if (!templateSection) {
      templateSection = document.querySelector("#akwarium-1");
    }

    // If still no template, log an error
    if (!templateSection) {
      console.error(
        "Aquarium template not found! Make sure the template exists in the DOM."
      );
      return $(
        '<div id="akwarium-' +
          index +
          '" class="akwarium-section">Error loading template</div>'
      );
    }

    // Clone the template
    let clonedSection = templateSection.cloneNode(true);

    // Update the ID
    clonedSection.id = "akwarium-" + index;

    // Update the heading
    let heading = clonedSection.querySelector("h3");
    if (heading) {
      const headingText = heading.innerHTML;
      heading.innerHTML = headingText.replace(/\d+/, index);
    }

    // Update all input IDs and names
    let inputs = clonedSection.querySelectorAll(
      'input[type="checkbox"], input[type="radio"]'
    );
    inputs.forEach(function (input) {
      // Update ID
      if (input.id) {
        input.id = input.id.replace("akw1_", "akw" + index + "_");
      }

      // Update name attribute
      if (input.name) {
        input.name = input.name.replace("akw[1]", "akw[" + index + "]");
      }

      // Make sure checkbox is unchecked in the clone
      input.checked = false;
    });

    // Update all label for attributes
    let labels = clonedSection.querySelectorAll("label");
    labels.forEach(function (label) {
      if (label.htmlFor) {
        label.htmlFor = label.htmlFor.replace("akw1_", "akw" + index + "_");
      }
    });

    // Update tooltip IDs too
    let tooltips = clonedSection.querySelectorAll(".tooltip-icon");
    tooltips.forEach(function (tooltip) {
      if (tooltip.dataset.tooltip) {
        // We don't need to change these as they reference global tooltips
      }
    });

    // Convert to jQuery object and make sure classes are correct
    let $section = $(clonedSection);
    $section.removeClass("hidden");
    $section.addClass("akwarium-section");

    return $section;
  }

  /**
   * Setup service mode functionality
   */
  function setupServiceMode() {
    $('input[name="tryb_wspolpracy"]').change(function () {
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
    if (selectedMode === "dodatkowe") {
      // If "Usługi dodatkowe" selected, only show "Inne potrzeby" checkboxes
      $(".akwarium-section .subsection").each(function () {
        const $subsection = $(this);
        if (!$subsection.hasClass("subsection-typ-zbiornika")) {
          // Hide the section
          $subsection.hide();

          // Disable all checkboxes in hidden sections
          $subsection.find('input[type="checkbox"]').prop("disabled", true);
        } else {
          // Show the "Inne potrzeby" section
          $subsection.show();

          // Enable checkboxes in "Inne potrzeby" section
          $subsection.find('input[type="checkbox"]').prop("disabled", false);
        }
      });
    } else {
      // Show all sections for other service modes
      $(".akwarium-section .subsection").show();

      // Enable all checkboxes
      $('.akwarium-section input[type="checkbox"]').prop("disabled", false);
    }
  }

  /**
   * Setup tank type selection - only one type can be selected per aquarium
   */
  function setupTankTypeSelection() {
    // Initial setup for existing aquariums
    applyTankTypeRestrictions();

    // Handle when new checkboxes are added (when adding new aquariums)
    $(document).on(
      "click",
      '.akwarium-section input[name^="akw"][name$="[typ][]"]',
      function () {
        const $clickedCheckbox = $(this);
        const aquariumId = $clickedCheckbox
          .closest(".akwarium-section")
          .attr("id");

        if ($clickedCheckbox.prop("checked")) {
          // Disable all other type checkboxes in this aquarium section
          $(`#${aquariumId} input[name^="akw"][name$="[typ][]"]`)
            .not($clickedCheckbox)
            .prop("checked", false);
          $(`#${aquariumId} input[name^="akw"][name$="[typ][]"]`)
            .not($clickedCheckbox)
            .prop("disabled", true);

          // Add disabled-checkbox class for styling
          $(`#${aquariumId} input[name^="akw"][name$="[typ][]"]`)
            .not($clickedCheckbox)
            .closest(".checkbox-option")
            .addClass("disabled-checkbox");
        } else {
          // If unchecked, enable all type checkboxes in this aquarium section
          $(`#${aquariumId} input[name^="akw"][name$="[typ][]"]`).prop(
            "disabled",
            false
          );
          $(`#${aquariumId} input[name^="akw"][name$="[typ][]"]`)
            .closest(".checkbox-option")
            .removeClass("disabled-checkbox");
        }
      }
    );
  }

  /**
   * Apply tank type selection restrictions to all aquarium sections
   */
  function applyTankTypeRestrictions() {
    $(".akwarium-section").each(function () {
      const $aquarium = $(this);
      const aquariumId = $aquarium.attr("id");

      // Check if any tank type is already selected
      const $selectedType = $(
        `#${aquariumId} input[name^="akw"][name$="[typ][]"]:checked`
      );

      if ($selectedType.length > 0) {
        // Disable all other type checkboxes in this aquarium section
        $(`#${aquariumId} input[name^="akw"][name$="[typ][]"]`)
          .not($selectedType)
          .prop("checked", false);
        $(`#${aquariumId} input[name^="akw"][name$="[typ][]"]`)
          .not($selectedType)
          .prop("disabled", true);
        $(`#${aquariumId} input[name^="akw"][name$="[typ][]"]`)
          .not($selectedType)
          .closest(".checkbox-option")
          .addClass("disabled-checkbox");
      }
    });
  }

  /**
   * Setup clear button functionality
   */
  function setupClearButton() {
    // Add click event handler for the clear button in step 1
    $(document).on("click", "#clear-serwis", function() {
      // Uncheck all checkboxes in all aquarium sections
      $('.akwarium-section input[type="checkbox"]').prop("checked", false);
      
      // Enable all type checkboxes that might have been disabled
      $('.akwarium-section input[name^="akw"][name$="[typ][]"]').prop("disabled", false);
      
      // Remove disabled-checkbox class from all checkbox options
      $('.akwarium-section .checkbox-option').removeClass("disabled-checkbox");
      
      // Show a brief confirmation message
      const $errorMessage = $("#step1-error");
      $errorMessage.text("Wszystkie zaznaczenia zostały wyczyszczone").show();
      
      // Hide the message after 3 seconds
      setTimeout(function() {
        $errorMessage.fadeOut(300);
      }, 3000);
    });
  }

  /**
   * Setup file upload fields
   */
  function setupFileUploadFields() {
    // Update photo upload fields based on aquarium count from step 1
    updatePhotoUploadFields();

    // Handle file upload preview
    $(document).on("change", ".file-upload", function () {
      const fileInput = this;
      const previewContainer = $(this).siblings(".file-preview");

      // Clear previous preview
      previewContainer.html("");

      // Check if a file is selected
      if (fileInput.files && fileInput.files[0]) {
        const reader = new FileReader();

        reader.onload = function (e) {
          const img = $("<img>")
            .attr("src", e.target.result)
            .addClass("file-preview-image");
          previewContainer.append(img);

          // Add remove button
          const removeBtn = $("<button>")
            .addClass("remove-file-btn")
            .html("&times;")
            .attr("type", "button")
            .on("click", function () {
              // Clear the file input and preview
              fileInput.value = "";
              previewContainer.html("");
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
    const $container = $(".aquarium-photos-container");
    let count = 1; // Default value

    // Get selected aquarium count from step 1
    const selectedValue = $('input[name="ilosc_akwarium"]:checked').val();

    if (selectedValue === "1") {
      count = 1;
    } else if (selectedValue === "2") {
      count = 2;
    } else if (selectedValue === "wiecej") {
      count = parseInt($("#ilosc_wiecej_input").val()) || 3;
      // Ensure count is within range
      count = Math.min(Math.max(count, 3), 10);
    }

    // Get current upload fields count
    const currentCount = $container.children(".photo-upload-row").length;

    // If count didn't change, don't rebuild everything
    if (count === currentCount) {
      return;
    }

    // If count decreased, just remove extra upload fields
    if (count < currentCount) {
      $container.children(".photo-upload-row").slice(count).remove();
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
    $("#next-step-1").click(function (e) {
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
    $("#prev-step-2").click(function (e) {
      e.preventDefault();
      goToStep(1);
    });

    $("#next-step-2").click(function (e) {
      e.preventDefault();
      if (validateStep2()) {
        // Generate package recommendations before showing step 3
        generatePackageRecommendations();
        goToStep(3);
      }
    });

    // Step 3 navigation
    $("#prev-step-3").click(function (e) {
      e.preventDefault();
      goToStep(2);
    });

    $("#next-step-3").click(function (e) {
      e.preventDefault();
      // Generate summary before showing step 4
      generateFormSummary();
      goToStep(4);
    });

    // Step 4 navigation
    $("#prev-step-4").click(function (e) {
      e.preventDefault();
      goToStep(3);
    });

    // Helper: convert FormData into nested object with arrays
    function formDataToNestedObject(form) {
      const formData = new FormData(form);
      const result = {};

      for (const [key, value] of formData.entries()) {
        // Find the corresponding input element (important for checkboxes with data attributes)
        const input = form.querySelector(
          `[name="${CSS.escape(key)}"][value="${CSS.escape(value)}"]`
        );
        const finalValue =
          input && input.dataset.text ? input.dataset.text : value;

        // Split keys like "extra_services[1][]" → ["extra_services", "1", ""]
        const parts = key.split(/\[|\]/).filter(Boolean);

        let current = result;
        parts.forEach((part, index) => {
          const isLast = index === parts.length - 1;

          if (isLast) {
            // Last part → assign value (handle arrays)
            if (current[part]) {
              if (!Array.isArray(current[part])) {
                current[part] = [current[part]];
              }
              current[part].push(finalValue);
            } else {
              current[part] = finalValue;
            }
          } else {
            // Not last → create object if missing
            if (!current[part]) {
              current[part] = {};
            }
            current = current[part];
          }
        });
      }

      return result;
    }

    // Submit form
    $("#submit-form").click(function (e) {
      e.preventDefault();

      if (validateStep4()) {
        const form = document.getElementById("serwis-natu-form");
        let data = formDataToNestedObject(form);

        // Get the total cost value from the final-cost-total-value element
        const totalCostElement = document.querySelector(
          ".final-cost-total-value"
        );
        if (totalCostElement) {
          // Extract the value and remove the currency symbol (zł) and convert to float
          const totalCostText = totalCostElement.textContent.trim();
          const totalCost = parseFloat(totalCostText.replace("zł", "").trim());
          // Add to form data
          data.total_cost = totalCost;
        }

        // Get recommendations
        const recommendations = window.serwisNatuRecommendations || [];
        
        // Collect selected service products with full details
        const selectedServiceProducts = [];
        $('input.service-product-checkbox-input:checked').each(function() {
          const $row = $(this).closest('tr');
          const productId = $(this).val();
          const productName = $row.find('td:nth-child(2)').text().trim();
          const priceText = $row.find('td:nth-child(3)').text().trim();
          const price = parseFloat(priceText.replace(/[^\d.,]/g, '').replace(',', '.')) || 0;
          
          selectedServiceProducts.push({
            id: productId,
            name: productName,
            price: price
          });
        });
        
        // Add selected service products to form data
        if (selectedServiceProducts.length > 0) {
          data.selected_products = selectedServiceProducts;
        }
        
        // Merge extra_services into akw and add recommended packages
        for (let akwId in data.akw) {
          // Get full extra services data with name and price
          const extraServicesWithPrices = getSelectedExtraServices(akwId);
          
          // Add extra services with price
          if (extraServicesWithPrices.length > 0) {
            data.akw[akwId]["Dodatkowe usługi"] = extraServicesWithPrices;
          } else {
            data.akw[akwId]["Dodatkowe usługi"] = [];
          }
          
          // Calculate total price for extra services
          let extraServicesTotal = 0;
          extraServicesWithPrices.forEach(service => {
            if (service.price) {
              extraServicesTotal += parseFloat(service.price);
            }
          });
          data.akw[akwId]["extra_services_price"] = extraServicesTotal;
          
          // Add recommended package information
          const aquariumRecommendation = recommendations.find(rec => rec.aquariumIndex == akwId);
          if (aquariumRecommendation && aquariumRecommendation.packageName) {
            data.akw[akwId]["Dopasowany pakiet"] = aquariumRecommendation.packageName;
            data.akw[akwId]["Cena pakietu"] = aquariumRecommendation.packagePrice;
          }
        }

        // Remove old keys as we've renamed and moved their data
        delete data.extra_services;

        console.log(data);

        // Create FormData for file uploads
        const formData = new FormData();
        formData.append("action", "submit_order");
        formData.append("form_data", JSON.stringify(data));

        // Add all file inputs to FormData
        const fileInputs = document.querySelectorAll(
          'input[type="file"].file-upload'
        );
        fileInputs.forEach((input) => {
          if (input.files && input.files[0]) {
            formData.append(input.name, input.files[0]);
          }
        });

        let ajaxurl = "/wp-admin/admin-ajax.php";
        // Send to PHP via AJAX with FormData
        $.ajax({
          url: ajaxurl,
          method: "POST",
          data: formData,
          processData: false, // Prevent jQuery from processing the data
          contentType: false, // Let the browser set the content type for FormData
          success: function (response) {
            console.log(response);
            alert("Zgłoszenie wysłane pomyślnie!");
            location.reload();
          },
          error: function (err) {
            console.error(err);
           // alert("Błąd podczas wysyłania zgłoszenia.");
          },
        });
      }
    });
  }

  /**
   * Navigate to a specific step
   *
   * @param {number} stepNumber - The step number to navigate to
   */
  function goToStep(stepNumber) {
    // Hide all steps
    $(".serwis-natu-step").removeClass("active");

    // Show the target step
    $("#step-" + stepNumber).addClass("active");

    // Update progress bar
    $(".sa-progress-item").removeClass("active completed");

    // Mark current step as active
    $('.sa-progress-item[data-step="' + stepNumber + '"]').addClass("active");

    // Mark previous steps as completed
    for (let i = 1; i < stepNumber; i++) {
      $('.sa-progress-item[data-step="' + i + '"]').addClass("completed");
    }

    // Scroll to top of form
    $("html, body").animate(
      {
        scrollTop: $(".serwis-natu-form-container").offset().top - 50,
      },
      300
    );
  }

  /**
   * Validate step 1 form data
   *
   * @return {boolean} Whether the validation passed
   */
  function validateStep1() {
    const $errorMessage = $("#step1-error");
    let isValid = true;

    // Get selected service mode
    const selectedMode = $('input[name="tryb_wspolpracy"]:checked').val();

    // Count visible sections and checkboxes
    $(".akwarium-section").each(function () {
      const $section = $(this);
      let hasSelection = false;

      if (selectedMode === "dodatkowe") {
        // For "Usługi dodatkowe" mode, check only "Inne potrzeby" subsections
        const $visibleSubsection = $section.find(".subsection-typ-zbiornika");

        // Check if any checkbox is selected in this subsection
        $visibleSubsection
          .find('input[type="checkbox"]:not(:disabled)')
          .each(function () {
            if ($(this).is(":checked")) {
              hasSelection = true;
            }
          });

        if (!hasSelection && $visibleSubsection.is(":visible")) {
          isValid = false;
        }
      } else {
        // For other modes, check all visible sections
        // Check if any checkbox is selected in visible subsections
        let hasVisibleSubsection = false;

        $section.find(".subsection:visible").each(function () {
          hasVisibleSubsection = true;

          $(this)
            .find('input[type="checkbox"]:not(:disabled)')
            .each(function () {
              if ($(this).is(":checked")) {
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
    const $errorMessage = $("#step2-error");
    let isValid = true;

    // Required fields validation
    const requiredFields = [
      "client_first_name",
      "client_last_name",
      "client_email",
      "client_phone",
      "aquarium_address",
      "preferred_date",
    ];

    // Check each required field
    requiredFields.forEach(function (fieldId) {
      const $field = $("#" + fieldId);
      if (!$field.val().trim()) {
        isValid = false;
        $field.addClass("error");
      } else {
        $field.removeClass("error");
      }
    });

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const $emailField = $("#client_email");
    if (
      $emailField.val().trim() &&
      !emailRegex.test($emailField.val().trim())
    ) {
      isValid = false;
      $emailField.addClass("error");
    }

    // Phone validation - allow only numbers, spaces, and basic phone characters
    const phoneRegex = /^[0-9\s\+\(\)\-]+$/;
    const $phoneField = $("#client_phone");
    if (
      $phoneField.val().trim() &&
      !phoneRegex.test($phoneField.val().trim())
    ) {
      isValid = false;
      $phoneField.addClass("error");
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
    const $container = $("#package-recommendations");
    const $costSummary = $("#cost-summary");

    // Clear previous recommendations
    $container.html(
      '<div class="loading-spinner"><div class="spinner"></div>Przygotowujemy rekomendacje...</div>'
    );
    $costSummary.empty();

    // Get form data
    const formData = getFormData();

    // Calculate package recommendations (always returns a Promise now)
    calculateRecommendations(formData)
      .then(function (recommendations) {
        // Display recommendations
        displayRecommendations(recommendations);

        // Calculate and display cost summary
        displayCostSummary(recommendations);
      })
      .catch(function (error) {
        // Display error message
        $container.html(
          '<div class="error-message"><p>' +
            error.message +
            "</p><p>Proszę spróbować ponownie lub skontaktować się z administratorem.</p></div>"
        );
        $costSummary.html(
          '<div class="error-message"><p>Nie można wyświetlić podsumowania kosztów.</p></div>'
        );
        console.error("Error getting recommendations:", error);
      });
  }

  /**
   * Collect all form data
   *
   * @return {object} Form data
   */
  function getFormData() {
    // Get selected tryb_wspolpracy element
    const $selectedTryb = $('input[name="tryb_wspolpracy"]:checked');
    
    const formData = {
      tryb_wspolpracy: $selectedTryb.val() || "jednorazowa",
      tryb_wspolpracy_text: $selectedTryb.data('text') || "Jednorazowa usługa serwisowa",
      ilosc_akwarium: $('input[name="ilosc_akwarium"]:checked').val() || "1",
      ilosc_wiecej: $("#ilosc_wiecej_input").val() || "3",
      akw: {},
    };

    // Determine number of aquariums
    let aquariumCount = 1;
    if (formData.ilosc_akwarium === "1") {
      aquariumCount = 1;
    } else if (formData.ilosc_akwarium === "2") {
      aquariumCount = 2;
    } else if (formData.ilosc_akwarium === "wiecej") {
      aquariumCount = parseInt(formData.ilosc_wiecej) || 3;
      aquariumCount = Math.max(3, Math.min(10, aquariumCount)); // Ensure between 3 and 10
    }

    // Collect data for each aquarium
    for (let i = 1; i <= aquariumCount; i++) {
      formData.akw[i] = {
        typ: [],
        cel: [],
        zakres: [],
        inne: [],
      };

      // Collect checkbox values for each category
      $('input[name="akw[' + i + '][typ][]"]:checked').each(function () {
        formData.akw[i].typ.push($(this).val());
      });

      $('input[name="akw[' + i + '][cel][]"]:checked').each(function () {
        formData.akw[i].cel.push($(this).val());
      });

      $('input[name="akw[' + i + '][zakres][]"]:checked').each(function () {
        formData.akw[i].zakres.push($(this).val());
      });

      $('input[name="akw[' + i + '][inne][]"]:checked').each(function () {
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
      console.warn("serwisNatuData object not found, creating fallback");
    }

    // If ajaxurl isn't set, try to use the WordPress admin-ajax URL
    if (!serwisNatuData.ajaxurl) {
      // Check if we can find WordPress admin URL in the page
      const adminAjaxUrl = "/wp-admin/admin-ajax.php";
      serwisNatuData.ajaxurl = adminAjaxUrl;
      console.warn(
        "AJAX URL not found in serwisNatuData, using fallback: " + adminAjaxUrl
      );
    }

    // Create a nonce if it doesn't exist
    if (!serwisNatuData.nonce) {
      serwisNatuData.nonce = "";
      console.warn("Nonce not found in serwisNatuData");
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
    return new Promise(function (resolve, reject) {
      // Make sure we have a valid URL
      let ajaxUrl = serwisNatuData.ajaxurl || "/wp-admin/admin-ajax.php";

      // Ensure ajaxUrl is an absolute URL if it doesn't start with http or /
      if (!ajaxUrl.startsWith("http") && !ajaxUrl.startsWith("/")) {
        ajaxUrl = "/" + ajaxUrl;
      }

      // Get or create a nonce
      let nonce = "";
      if (serwisNatuData && serwisNatuData.nonce) {
        nonce = serwisNatuData.nonce;
      } else {
        // Try to get nonce from the page
        const nonceField = document.querySelector('input[name="_wpnonce"]');
        if (nonceField) {
          nonce = nonceField.value;
        }
      }

      console.log("Sending AJAX request to:", ajaxUrl);
      console.log("Form data being sent:", formData);

      $.ajax({
        url: ajaxUrl,
        type: "POST",
        data: {
          action: "get_package_recommendations",
          nonce: nonce,
          form_data: formData,
        },
        success: function (response) {
          console.log("AJAX response:", response);
          if (response.success && response.data) {
            // Store extra services in a global variable for later use
            if (response.data.extraServices) {
              window.serwisNatuExtraServices = response.data.extraServices;
              console.log(
                "Extra services loaded:",
                window.serwisNatuExtraServices
              );
            }

            if (response.data.recommendations) {
              resolve(response.data.recommendations);
            } else {
              reject(new Error("Nie udało się uzyskać rekomendacji pakietów."));
            }
          } else {
            const errorMessage =
              response.data && response.data.message
                ? response.data.message
                : "Nie udało się uzyskać rekomendacji pakietów.";
            console.error("Server error response:", response);
            reject(new Error(errorMessage));
          }
        },
        error: function (xhr, status, error) {
          console.error("AJAX error:", { xhr, status, error });
          console.error("Response text:", xhr.responseText);
          reject(
            new Error("Błąd komunikacji z serwerem: " + (error || status))
          );
        },
      });
    });
  }

  /**
   * Display package recommendations
   *
   * @param {array} recommendations Package recommendations
   */
  function displayRecommendations(recommendations) {
    const $container = $("#package-recommendations");
    $container.empty();

    if (!recommendations || recommendations.length === 0) {
      $container.html("<p>Nie udało się wygenerować rekomendacji.</p>");
      return;
    }

    // Get extra services from the global scope if they exist
    const extraServices = window.serwisNatuExtraServices || [];
    const hasExtraServices = extraServices && extraServices.length > 0;

    // For each recommendation, create a package element and add extra services
    recommendations.forEach(function (recommendation) {
      const $recommendation = $(`
                <div class="package-recommendation" data-aquarium="${recommendation.aquariumIndex}" data-package="${recommendation.packageKey}">
                    <div class="package-header">
                        <h3 class="package-name">Akwarium ${recommendation.aquariumIndex} - ${recommendation.packageName}</h3>
                       
                    </div>
                    <div class="section-description">
                        ${recommendation.packageDescription}
                    </div>
                </div>
            `);

      // If we have extra services, add them beneath each package
      if (hasExtraServices) {
        const $extraServicesContainer = $(
          '<div class="extra-services-container"></div>'
        );
        const $extraServicesHeader = $(
          '<h4 class="extra-services-header">Wybierz dodatkowe usługi:</h4>'
        );
        $extraServicesContainer.append($extraServicesHeader);

        // Create a list of checkboxes for extra services
        const $extraServicesList = $('<div class="extra-services-list"></div>');

        extraServices.forEach(function (service) {
          if (!service || !service.name || !service.price) return;

          const serviceId = `extra-service-${recommendation.aquariumIndex}-${
            service.id || service.name.replace(/\s+/g, "-").toLowerCase()
          }`;
          const $serviceItem = $(`
                        <div class="extra-service-item">
                            <label for="${serviceId}">
                                <input type="checkbox" 
                                    id="${serviceId}" 
                                    name="extra_services[${
                                      recommendation.aquariumIndex
                                    }][]" 
                                    value="${service.id || service.name}" 
                                    data-price="${service.price}" 
                                    data-text="${service.name}"
                                    class="extra-service-checkbox">
                                <span class="extra-service-name">${
                                  service.name
                                }</span>
                                
                            </label>
                            ${
                              service.description
                                ? `<p class="extra-service-description">${service.description}</p>`
                                : ""
                            }
                        </div>
                    `);

          $extraServicesList.append($serviceItem);
        });

        $extraServicesContainer.append($extraServicesList);
        $recommendation.append($extraServicesContainer);
      }

      $container.append($recommendation);
    });

    // Initialize event listeners for extra services
    setupExtraServicesListeners();
  }

  /**
   * Setup event listeners for extra services
   */
  function setupExtraServicesListeners() {
    // Listen for changes on extra service checkboxes
    $(".extra-service-checkbox").on("change", function () {
      // Recalculate total cost when services are selected/deselected
      updateCostSummary();
    });
  }

  /**
   * Display cost summary
   *
   * @param {array} recommendations Package recommendations
   */
  function displayCostSummary(recommendations) {
    const $costSummary = $("#cost-summary");
    $costSummary.empty();

    if (!recommendations || recommendations.length === 0) {
      $costSummary.html(
        "<p>Nie udało się przygotować podsumowania kosztów.</p>"
      );
      return;
    }

    // Store recommendations globally so we can access them from updateCostSummary
    window.serwisNatuRecommendations = recommendations;

    // Update cost summary
    updateCostSummary();
  }

  /**
   * Update cost summary based on selected packages and extra services
   */
  function updateCostSummary() {
    const $costSummary = $("#cost-summary");
    $costSummary.empty();

    const recommendations = window.serwisNatuRecommendations || [];

    if (!recommendations || recommendations.length === 0) {
      $costSummary.html(
        "<p>Nie udało się przygotować podsumowania kosztów.</p>"
      );
      return;
    }

    // Calculate total cost
    let totalCost = 0;
    const costItems = [];

    // Add package costs
    recommendations.forEach(function (recommendation) {
      costItems.push({
        label: `Akwarium ${recommendation.aquariumIndex} - ${recommendation.packageName}`,
        price: recommendation.packagePrice,
        type: "package",
      });

      totalCost += parseFloat(recommendation.packagePrice);
    });

    // Add extra services costs
    $(".extra-service-checkbox:checked").each(function () {
      const $checkbox = $(this);
      const servicePrice = parseFloat($checkbox.data("price"));
      const serviceName = $checkbox.siblings(".extra-service-name").text();
      const aquariumIndex = $checkbox
        .closest(".package-recommendation")
        .data("aquarium");

      costItems.push({
        label: `Akwarium ${aquariumIndex} - ${serviceName} (usługa dodatkowa)`,
        price: servicePrice,
        type: "extra-service",
      });

      totalCost += servicePrice;
    });

    // Build HTML
    const $costItemsContainer = $('<div class="cost-items"></div>');

    costItems.forEach(function (item) {
      const $costItem = $(`
                <div class="cost-item ${item.type}">
                    <span class="cost-item-label">${item.label}</span>
                    <span class="cost-item-price">${item.price} zł</span>
                </div>
            `);

      $costItemsContainer.append($costItem);
    });

    const $totalCost = $(`
            <div class="total-cost">
                <span>Razem:</span>
                <span>${totalCost.toFixed(2)} zł</span>
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
    $("#recommended_products_checkbox").on("change", function () {
      const $container = $("#recommended-products-container");

      if ($(this).is(":checked")) {
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
    const $container = $("#recommended-products-container");
    const $productsList = $("#recommended-products-list");

    // Show loading spinner
    $container.show();
    $productsList.hide();
    $(".loading-spinner", $container).show();

    // Get form data
    const formData = getFormData();

    console.log("serwisNatuData object:", serwisNatuData);

    // Get the appropriate AJAX URL and nonce
    const ajaxUrl = serwisNatuData.ajaxurl || "/wp-admin/admin-ajax.php";
    // Try multiple possible nonce key names
    const nonce = serwisNatuData.ajaxNonce || serwisNatuData.nonce || "";

    console.log("Using AJAX URL:", ajaxUrl);
    console.log("Form data to be sent:", formData);

    // Send AJAX request to get recommended products
    $.ajax({
      url: ajaxUrl,
      type: "POST",
      data: {
        action: "get_recommended_products",
        nonce: nonce,
        form_data: formData,
      },
      success: function (response) {
        console.log("Recommended products AJAX response:", response);
        if (
          response &&
          response.success &&
          response.data &&
          response.data.products
        ) {
          displayRecommendedProducts(response.data.products);
        } else {
          console.error("Invalid AJAX response structure:", response);
          displayNoProductsMessage("Niepoprawna odpowiedź z serwera.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error loading recommended products:", error);
        console.error("XHR status:", status);
        console.error("XHR response text:", xhr.responseText);

        // Try to parse error response if it's JSON
        let errorMessage =
          "Wystąpił błąd podczas ładowania rekomendowanych produktów.";
        try {
          const jsonResponse = JSON.parse(xhr.responseText);
          if (jsonResponse && jsonResponse.data && jsonResponse.data.message) {
            errorMessage = jsonResponse.data.message;
          }
        } catch (e) {
          // Not JSON or parsing failed, use default message
        }

        displayNoProductsMessage(errorMessage);
      },
      complete: function () {
        // Hide loading spinner
        $(".loading-spinner", $container).hide();
        $productsList.show();
      },
    });
  }

  /**
   * Display recommended products in a table
   *
   * @param {Array} products Array of product objects
   */
  function displayRecommendedProducts(products) {
    const $productsList = $("#recommended-products-list");
    $productsList.empty();

    console.log("Displaying recommended products:", products);

    if (!products || products.length === 0) {
      displayNoProductsMessage();
      return;
    }

    // Create table
    const $table = $('<table class="recommended-products-table"></table>');
    const $thead = $("<thead></thead>");
    const $tbody = $("<tbody></tbody>");

    // Add table header
    $thead.append(`
            <tr>
                <th><h3>Zdjęcie</h3></th>
                <th><h3>Nazwa produktu</h3></th>
                <th><h3>Cena</h3></th>
                <th><h3>Zabierz na serwis</h3></th>
            </tr>
        `);

    // Add products to table
    products.forEach(function (product) {
      try {
        const $row = $("<tr></tr>");

        // Handle possible missing data with defaults
        const imageContent =
          product.image || '<div class="no-image">Brak zdjęcia</div>';
        const productName = product.name || "Produkt bez nazwy";
        const productUrl = product.url || "#";
        const productPrice = product.price || "-";
        const productId = product.id || "";

        $row.append(`<td>${imageContent}</td>`);
        $row.append(
          `<td><a href="${productUrl}" target="_blank">${productName}</a></td>`
        );
        $row.append(`<td>${productPrice}</td>`);
        $row.append(
          `<td>
            <div class="service-product-checkbox">
              <input type="checkbox" id="service_product_${productId}" 
                name="service_products[]" value="${productId}" 
                class="service-product-checkbox-input">
              <label for="service_product_${productId}"></label>
            </div>
           </td>`
        );

        $tbody.append($row);
      } catch (err) {
        console.error("Error displaying product:", err, product);
      }
    });

    // Assemble table
    $table.append($thead);
    $table.append($tbody);
    $productsList.append($table);

    // Add a helpful message
    $productsList.append(
      '<p class="section-description">Powyższe produkty są rekomendowane na podstawie wybranych opcji formularza.</p>'
    );
  }

  /**
   * Display a message when no products are available
   *
   * @param {string} customMessage Optional custom message
   */
  function displayNoProductsMessage(customMessage) {
    const $container = $("#recommended-products-container");
    const $productsList = $("#recommended-products-list");

    // Hide loading spinner if it's visible
    $(".loading-spinner", $container).hide();

    const message =
      customMessage || "Brak rekomendowanych produktów dla wybranych opcji.";
    $productsList
      .html(`<div class="no-products-message">${message}</div>`)
      .show();

    console.log("Displaying no products message:", message);
  }

  /**
   * Generate complete form summary for step 4
   */
  function generateFormSummary() {
    // Generate aquariums summary
    generateAquariumsSummary();

    // Generate contact information summary
    generateContactSummary();

    // Generate selected products summary
    generateSelectedProductsSummary();

    // Generate final cost summary
    generateFinalCostSummary();
  }
  
  /**
   * Generate summary of selected products
   */
  function generateSelectedProductsSummary() {
    let $container = $("#products-summary");
    
    // If container doesn't exist, create it
    if ($container.length === 0) {
      $("#contact-summary").after('<div id="products-summary" class="form-section"></div>');
      $container = $("#products-summary");
    }
    
    $container.html(""); // Clear container
    
    // Check if any product is selected
    const selectedProducts = [];
    $('input.service-product-checkbox-input:checked').each(function() {
      const $row = $(this).closest('tr');
      const productId = $(this).val();
      const productName = $row.find('td:nth-child(2)').text().trim();
      const priceText = $row.find('td:nth-child(3)').text().trim();
      const price = parseFloat(priceText.replace(/[^\d.,]/g, '').replace(',', '.')) || 0;
      
      selectedProducts.push({
        id: productId,
        name: productName,
        price: price
      });
    });
    
    // If no products selected, don't show this section
    if (selectedProducts.length === 0) {
      $container.hide();
      return;
    }
    
    // Build summary content
    $container.append('<h3>Produkty do zabrania na serwis</h3>');
    const $productsList = $('<ul class="selected-products-list"></ul>');
    
    selectedProducts.forEach(function(product) {
      let displayText = product.name;
      if (product.price > 0) {
        displayText += ` (${product.price.toFixed(2)} zł)`;
      }
      $productsList.append(`<li>${displayText}</li>`);
    });
    
    $container.append($productsList);
    $container.show();
  }

  /**
   * Generate summary of all aquariums
   */
  function generateAquariumsSummary() {
    const $container = $("#aquariums-summary");
    $container.html(""); // Clear container

    // Get form data
    const formData = getFormData();

    // Get recommendations
    const recommendations = window.serwisNatuRecommendations || [];

    // Determine number of aquariums
    let aquariumCount = 1;
    if (formData.ilosc_akwarium === "1") {
      aquariumCount = 1;
    } else if (formData.ilosc_akwarium === "2") {
      aquariumCount = 2;
    } else if (formData.ilosc_akwarium === "wiecej") {
      aquariumCount = parseInt(formData.ilosc_wiecej) || 3;
    }

    // For each aquarium create a summary card
    for (let i = 1; i <= aquariumCount; i++) {
      const aquariumData = formData.akw[i] || {};

      // Get recommendation for this aquarium
      const recommendation =
        recommendations.find((rec) => rec.aquariumIndex === i) || {};

      // Create aquarium summary card
      const $card = $('<div class="aquarium-summary-card"></div>');

      // Add header
      $card.append(`
                <div class="aquarium-summary-header">
                    <h3>Akwarium ${i}</h3>
                    
                </div>
            `);

      // Create body container
      const $body = $('<div class="aquarium-summary-body"></div>');

      // Add package info if available
      if (recommendation.packageName) {
        $body.append(`
                    <div class="package-summary">
                        <h4 class="package-summary-name">Wybrany pakiet: ${recommendation.packageName}</h4>
                        <span class="package-summary-price">${recommendation.packagePrice} zł</span>
                       
                    </div>
                `);
      }

      // Add options summary
      const $optionsSummary = $('<div class="options-summary"></div>');

      // Add options from each category if available
      if (aquariumData.typ && aquariumData.typ.length > 0) {
        $optionsSummary.append("<h4>Typ akwarium:</h4>");
        const $typeList = $('<ul class="options-list"></ul>');

        aquariumData.typ.forEach(function (type) {
          $typeList.append(`<li>${getLabelForOption(type)}</li>`);
        });

        $optionsSummary.append($typeList);
      }

      if (aquariumData.cel && aquariumData.cel.length > 0) {
        $optionsSummary.append("<h4>Cel serwisu:</h4>");
        const $goalList = $('<ul class="options-list"></ul>');

        aquariumData.cel.forEach(function (goal) {
          $goalList.append(`<li>${getLabelForOption(goal)}</li>`);
        });

        $optionsSummary.append($goalList);
      }

      if (aquariumData.zakres && aquariumData.zakres.length > 0) {
        $optionsSummary.append("<h4>Zakres serwisu:</h4>");
        const $scopeList = $('<ul class="options-list"></ul>');

        aquariumData.zakres.forEach(function (scope) {
          $scopeList.append(`<li>${getLabelForOption(scope)}</li>`);
        });

        $optionsSummary.append($scopeList);
      }

      if (aquariumData.inne && aquariumData.inne.length > 0) {
        $optionsSummary.append("<h4>Inne potrzeby:</h4>");
        const $otherList = $('<ul class="options-list"></ul>');

        aquariumData.inne.forEach(function (other) {
          $otherList.append(`<li>${getLabelForOption(other)}</li>`);
        });

        $optionsSummary.append($otherList);
      }

      $body.append($optionsSummary);

      // Get extra services for this aquarium if any
      const extraServices = getSelectedExtraServices(i);

      if (extraServices.length > 0) {
        const $extraServicesSummary = $(
          '<div class="extra-services-summary"></div>'
        );
        $extraServicesSummary.append("<h4>Dodatkowe usługi:</h4>");

        extraServices.forEach(function (service) {
          $extraServicesSummary.append(`
                        <div class="extra-service-summary-item">
                            <span class="extra-service-summary-item-name">${service.name}</span>
                            <span>${service.price} zł</span>
                        </div>
                    `);
        });

        $body.append($extraServicesSummary);
      }

      // Add photo if available
      const photoInput = document.getElementById(`aquarium_photo_${i}`);
      if (photoInput && photoInput.files && photoInput.files[0]) {
        const $photoSummary = $('<div class="aquarium-photo-summary"></div>');
        $photoSummary.append("<h4>Zdjęcie akwarium:</h4>");

        const reader = new FileReader();
        reader.onload = function (e) {
          $photoSummary.append(
            `<img src="${e.target.result}" alt="Zdjęcie akwarium ${i}">`
          );
        };

        reader.readAsDataURL(photoInput.files[0]);
        $body.append($photoSummary);
      }

      $card.append($body);
      $container.append($card);
    }
  }

  /**
   * Get selected extra services for an aquarium
   *
   * @param {number} aquariumIndex Aquarium index
   * @return {array} Array of selected extra services
   */
  function getSelectedExtraServices(aquariumIndex) {
    const services = [];

    $(
      `.package-recommendation[data-aquarium="${aquariumIndex}"] .extra-service-checkbox:checked`
    ).each(function () {
      const $checkbox = $(this);
      const name = $checkbox.siblings(".extra-service-name").text();
      const price = parseFloat($checkbox.data("price"));

      services.push({
        name: name,
        price: price,
      });
    });

    return services;
  }

  /**
   * Generate contact information summary
   */
  function generateContactSummary() {
    const $container = $("#contact-summary");
    $container.html(""); // Clear container

    // Create grid for contact information
    const $grid = $('<div class="contact-summary-grid"></div>');

    // Get contact field values
    const contactFields = {
      client_first_name: "Imię",
      client_last_name: "Nazwisko",
      client_email: "Email",
      client_phone: "Telefon",
      aquarium_address: "Adres akwarium",
      preferred_date: "Preferowana data wizyty",
      alternative_date: "Alternatywna data",
      client_comments: "Dodatkowe uwagi",
    };

    // Add each contact field
    for (const [id, label] of Object.entries(contactFields)) {
      const value = $("#" + id).val() || "-";

      if (value && value !== "-") {
        const $item = $(`
                    <div class="contact-summary-item">
                        <h4 class="contact-summary-label">${label}:</h4>
                        <span class="contact-summary-value">${value}</span>
                    </div>
                `);

        $grid.append($item);
      }
    }

    $container.append($grid);
  }

  /**
   * Generate final cost summary
   */
  function generateFinalCostSummary() {
    const $container = $("#final-cost-summary");
    $container.html(""); // Clear container

    const recommendations = window.serwisNatuRecommendations || [];

    // Calculate total cost
    let totalCost = 0;
    const costItems = [];

    // Add package costs
    recommendations.forEach(function (recommendation) {
      costItems.push({
        label: `Akwarium ${recommendation.aquariumIndex} - ${recommendation.packageName}`,
        price: parseFloat(recommendation.packagePrice),
      });

      totalCost += parseFloat(recommendation.packagePrice);
    });

    // Add extra services costs for each aquarium
    recommendations.forEach(function (recommendation) {
      const aquariumIndex = recommendation.aquariumIndex;
      const extraServices = getSelectedExtraServices(aquariumIndex);

      extraServices.forEach(function (service) {
        costItems.push({
          label: `Akwarium ${aquariumIndex} - ${service.name} (usługa dodatkowa)`,
          price: service.price,
        });

        totalCost += service.price;
      });
    });
    
    // Add selected products costs
    const selectedProducts = [];
    $('input.service-product-checkbox-input:checked').each(function() {
      const $row = $(this).closest('tr');
      const productId = $(this).val();
      const productName = $row.find('td:nth-child(2)').text().trim();
      // Extract price from the text (e.g., "99.00 zł" -> 99.00)
      const priceText = $row.find('td:nth-child(3)').text().trim();
      const price = parseFloat(priceText.replace(/[^\d.,]/g, '').replace(',', '.')) || 0;
      
      selectedProducts.push({
        id: productId,
        name: productName,
        price: price
      });
      
      // Add to cost items
      if (price > 0) {
        costItems.push({
          label: `Produkt: ${productName}`,
          price: price,
        });
        
        totalCost += price;
      }
    });

    // Build HTML
    costItems.forEach(function (item) {
      $container.append(`
                <div class="final-cost-item">
                    <span>${item.label}</span>
                    <span>${item.price.toFixed(2)} zł</span>
                </div>
            `);
    });

    // Add total
    $container.append(`
            <div class="final-cost-total">
                <span>Razem:</span>
                <span class="final-cost-total-value">${totalCost.toFixed(
                  2
                )} zł</span>
            </div>
        `);
  }

  /**
   * Get human-readable label for option value
   *
   * @param {string} optionValue Option value
   * @return {string} Human-readable label
   */
  function getLabelForOption(optionValue) {
    // Find the input with the given value
    const $option = $(`input[value="${optionValue}"]`);
    if ($option.length) {
      // Return its data-text attribute (if defined), otherwise fallback to value
      return $option.data("text") || optionValue;
    }
    return optionValue;
  }

  /**
   * Validate step 4 (required checkboxes)
   *
   * @return {boolean} Whether the validation passed
   */
  function validateStep4() {
    const $errorMessage = $("#step4-error");
    let isValid = true;

    // Check privacy policy acceptance
    if (!$("#privacy_policy").prop("checked")) {
      isValid = false;
      $("#privacy_policy").addClass("error");
    } else {
      $("#privacy_policy").removeClass("error");
    }

    // Check terms and conditions acceptance
    if (!$("#terms_conditions").prop("checked")) {
      isValid = false;
      $("#terms_conditions").addClass("error");
    } else {
      $("#terms_conditions").removeClass("error");
    }

    // Display error message if validation failed
    if (!isValid && $errorMessage.length) {
      $errorMessage.slideDown(200);
    } else if ($errorMessage.length) {
      $errorMessage.slideUp(200);
    }

    return isValid;
  }
})(jQuery);
