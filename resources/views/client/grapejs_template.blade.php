<!DOCTYPE html>
<html
  lang="en"
  class="light-style"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="/assets/"
  data-template="vertical-menu-template"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>{{env('APP_NAME')}}</title>
    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Bootstrap Icons -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/grapesjs/dist/css/grapes.min.css">
    
    <link
      href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
  </head>

  @include('client.custom_settings')

  <body>
    @yield('content')
    
    <!-- Modal for dropdown services-->
    <div class="modal fade" id="serviceDropdownModal" tabindex="-1" aria-labelledby="serviceDropdownModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-4 shadow-sm rounded">
          <div class="modal-header border-bottom">
            <h5 class="modal-title fw-bold">Edit Field</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Field Name</label>
              <input type="text" id="fieldNameInput" class="form-control" placeholder="Select services">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Services</label>
              <select id="serviceListModal" class="form-select" multiple></select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Default Service</label>
              <select id="serviceDefaultListModal" class="form-select"></select>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="requiredField">
              <label class="form-check-label" for="requiredField">
                Required field
              </label>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="allowMultipleSelections">
              <label class="form-check-label" for="allowMultipleSelections">
                Allow multiple selections
              </label>
            </div>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="allowMultipleQuantities">
              <label class="form-check-label" for="allowMultipleQuantities">
                Allow multiple quantities
              </label>
            </div>
          </div>

          <div class="modal-footer border-top">
            <button id="confirmServices" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal for services options -->
    <div class="modal fade" id="serviceOptionsDropdownModal" tabindex="-1" aria-labelledby="serviceOptionsDropdownModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-4 shadow-sm rounded">
          <div class="modal-header border-bottom">
            <h5 class="modal-title fw-bold">Edit Field</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label fw-semibold">Field Name</label>
              <input type="text" id="fieldNameInput1" class="form-control" placeholder="Select services">
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Services</label>
              <select id="serviceListModal1" class="form-select" multiple></select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Default Selection</label>
              <select id="serviceDefaultListModal1" class="form-select"></select>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="requiredField1">
              <label class="form-check-label" for="requiredField">
                Required field
              </label>
            </div>

            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="allowMultipleSelections1">
              <label class="form-check-label" for="allowMultipleSelections">
                Allow multiple selections
              </label>
            </div>

            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="allowMultipleQuantities1">
              <label class="form-check-label" for="allowMultipleQuantities">
                Allow multiple quantities
              </label>
            </div>
          </div>

          <div class="modal-footer border-top">
            <button id="confirmOptionServices" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
          </div>
        </div>
      </div>
    </div>

  </body>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://malsup.github.io/jquery.blockUI.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/grapesjs@0.20.4/dist/grapes.min.js"></script>
  <script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2"></script>
  <script src="https://unpkg.com/grapesjs-plugin-forms@2.0.5"></script>
  <script src="https://unpkg.com/grapesjs-blocks-basic@1.0.1"></script>
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

  <script>
      // Initialize GrapesJS editor
      var editor = grapesjs.init({
          container: '#gjs',
          width: 'auto',
          canvas: {
              styles: [
                  'https://use.fontawesome.com/releases/v5.8.2/css/all.css',
                  'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap',
                  'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css',
                  'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/css/mdb.min.css',
              ],
              scripts: [
                  'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js',
                  'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js',
                  'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/js/bootstrap.min.js',
                  'https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.19.1/js/mdb.min.js',
              ],
          },
      });

      // Add a Save button to the top panel
      editor.Panels.addButton('options', {
          id: 'save-button',
          className: 'fa fa-save',
          command: 'save-content',
          attributes: { title: 'Save' },
      });

      editor.Commands.add('save-content', {
          run: function (editor) {
              var html = editor.getHtml();
              var css = editor.getCss();
              var json_data = JSON.stringify(editor.getComponents());
              var slug = '{{$slug}}';

              fetch('/landing-page/save', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                  },
                  body: JSON.stringify({
                      html: html,
                      css: css,
                      json_data: json_data,
                      is_published: false,
                      slug: slug,
                  })
              })
              .then(response => response.json())
              .then(data => {
                  if (data.status=='success') {
                      alert('Content Saved Successfully!');
                  } else {
                      alert('Error saving content.');
                  }
              })
              .catch(error => console.error('Error:', error));
          }
      });

      // Function to load the saved page data
      window.onload = function () {
          var slug = '{{$slug}}';
          fetch(`/landing-page/load/${slug}`)
              .then(response => response.json())
              .then(data => {
                  if (data.status === 'success') {
                      editor.setComponents(data.html);
                      editor.setStyle(data.css);
                  }
              })
              .catch(error => console.error('Error loading:', error));
      };

      editor.on('load', () => {
          const components = editor.getComponents();
          //components.reset();
      });

      // Initialize Block Manager
      const blockManager = editor.BlockManager;

      // Define reusable function to create blocks
      const createBlock = (id, icon, label, category, content) => {
        blockManager.add(id, {
          label: `
            <div style="text-align: center;">
              <i class="${icon}" style="font-size: 22px; display: block; margin-bottom: 5px;"></i>
              <span style="font-size: 10px; font-weight: bold;">${label}</span>
            </div>
          `,
          category: category,
          content: content,
        });
      };

      // Get services data from the Blade file
      const services = {!! isset($services) ? json_encode($services) : '[]' !!}; // Pass services safely to JS
      //console.log(services,'services');

      // Create dynamic block content with passed services
      let servicesContent = `
        <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
          <h3 style="font-size: 18px; font-weight: bold; color: #333;">Select Services</h3>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px;">
      `;

      services.forEach((service) => {
            let serviceType = service.service_type === 'recurring' ? 'Recurring' : 'One-Time';
            let price = '';

            if (service.service_type === 'recurring') {
                // Check if the service has a trial period
                if (service.trial_for && service.trial_price) {
                    let trialCurrency = service.trial_currency ?? '';
                    let trialPeriod = service.trial_for;
                    let trialPeriodType = service.trial_period ?? 'day';
                    
                    price = `${trialCurrency} $${service.trial_price} for ${trialPeriod} ${trialPeriod > 1 ? trialPeriodType + 's' : trialPeriodType}, `;
                }

                let recurringValue = service.recurring_service_currency_value ?? 0; // Default to 0 if null
                let recurringCurrency = service.recurring_service_currency ?? ''; // Default to empty string
                let recurringPeriod = service.recurring_service_currency_value_two ?? 1; // Default to 1
                let recurringPeriodType = service.recurring_service_currency_value_two_type ?? 'month';

                price += `${recurringCurrency} $${recurringValue} / ${recurringPeriod} ${
                    recurringPeriod > 1 ? recurringPeriodType + 's' : recurringPeriodType
                }`;
            } else {
                // Handle one-time service pricing
                let oneTimeValue = service.one_time_service_currency_value ?? 0; // Default to 0
                let oneTimeCurrency = service.one_time_service_currency ?? ''; // Default to empty string

                price = `${oneTimeCurrency} $${oneTimeValue}`;
            }

            // Now display the service with the calculated price
            servicesContent += `
              <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; background: #fff; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <input type="checkbox" id="service-${service.id}" style="margin-bottom: 10px;">
                <label for="service-${service.id}" style="font-size: 16px; font-weight: bold;">${service.service_name} (${serviceType})</label>
                <p style="font-size: 14px; color: #555;">${price}</p>
              </div>
            `;
      });

      servicesContent += `
          </div>
        </div>
      `;

      let dropdownCounter = 1; // Move this outside so it doesn't reset on every call
      function createServiceDropdownBlock() {
        const uniqueId = `serviceDropdownSelect-${dropdownCounter++}`;

        createBlock(
          `select-services-${uniqueId}`, // Also make block ID unique
          'fa-solid fa-chevron-down',
          'Service Dropdown',
          { id: 'service-selection', label: 'Service Selection', open: true },
          `
            <div class="trigger-service-modal">
              <h3 style="font-size: 18px; font-weight: bold; color: #333;">Select Services</h3>
              <select class="form-control mt-2" id="${uniqueId}">
                <option value="">Select a service</option>
              </select>
            </div>
          `
        );
      }

      createServiceDropdownBlock();

      document.addEventListener('DOMContentLoaded', function () {
        const openBlocksButton = document.querySelector('.gjs-pn-btn.fa.fa-th-large');
        if (openBlocksButton) {
          openBlocksButton.click();
        }
      });

      function generateServiceOptions(services) {
          return services.map(service => {
              let price = "";
              let duration = "";

              if (service.service_type === "recurring") {
                  // Recurring service pricing
                  let recurringValue = service.recurring_service_currency_value ?? 0;
                  let recurringPeriod = service.recurring_service_currency_value_two ?? 1;
                  let recurringPeriodType = service.recurring_service_currency_value_two_type ?? "month";

                  price = `$${recurringValue}`;
                  duration = `${recurringPeriod} ${recurringPeriod > 1 ? recurringPeriodType + "s" : recurringPeriodType}`;
              } else {
                  // One-time service pricing
                  let oneTimeValue = service.one_time_service_currency_value ?? 0;
                  let oneTimeCurrency = service.one_time_service_currency ?? "";

                  price = `${oneTimeCurrency} $${oneTimeValue}`;
                  duration = "One-Time Payment";
              }

              return `<option value="${service.id}">${service.service_name} - ${price} / ${duration}</option>`;
          }).join('');
      }

      // Creating Service Selection Block
      /*
      createBlock(
          'select-services-dropdown',
          'fa-solid fa-caret-down',
          'Dropdown',
          'service-selection',
          `
            <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
              <h3 style="font-size: 18px; font-weight: bold; color: #333;">Select Services</h3>
              <div style="margin-top: 15px; text-align: left;">
                <label for="service-dropdown" style="font-size: 16px; font-weight: bold; display: block; margin-bottom: 5px;">Available Services</label>
                <select id="service-dropdown" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                  ${generateServiceOptions(services)}
                </select>
              </div>
            </div>
          `
      );
      */

      // Client Section
      ['name', 'email', 'password', 'address', 'phone', 'email-optin'].forEach((field) => {
        const labels = {
          name: 'Name',
          email: 'Email',
          password: 'Password',
          address: 'Address',
          phone: 'Phone',
          'email-optin': 'Email Opt-in',
        };
        const placeholders = {
          name: 'Enter your name',
          email: 'Enter your email',
          password: 'Enter your password',
          address: 'Enter your address',
          phone: 'Enter your phone number',
          'email-optin': 'Opt-in for emails',
        };

        // Determine the input type based on the field
        const inputType = field === 'password' ? 'password' : field === 'email' ? 'email' : 'text';

        createBlock(
          `client-${field}`,
          'fa fa-user', // Replace with appropriate icons if required
          labels[field],
          { id: 'client-section', label: 'Client Section', open: true },
          `
            <div class="form-group">
              <label for="${field}" class="form-label">${labels[field]}:</label>
              <input type="${inputType}" class="form-control" id="${field}" placeholder="${placeholders[field]}" name="${field}">
            </div>
          `
        );
      });

     // Billing Details Block
    createBlock(
      "payment",
      "fa fa-credit-card", // Icon for Payment Block
      "Payment",
      { id: "billing-details", label: "Billing Details", open: true },
      `
        <div class="form-group" style="padding: 8px; border: 1px solid #e0e0e0; border-radius: 8px; background: #f9f9f9;">
          <label class="form-label" style="font-size: 16px; font-weight: bold; margin-bottom: 10px; display: block;">
            <i class="fa fa-credit-card" style="margin-right: 8px;"></i> Payment Method:
          </label>
          
          <div class="form-check d-flex align-items-center" style="padding: 10px; border: 1px solid #ccc; border-radius: 6px; background: white; margin-bottom: 8px;">
            <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="paypal" checked>
            <label class="form-check-label d-flex align-items-center" for="paypal" style="margin-left: 10px;">
              <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" width="80">
            </label>
          </div>

          <div class="form-check d-flex align-items-center" style="padding: 10px; border: 1px solid #ccc; border-radius: 6px; background: white;">
            <input class="form-check-input" type="radio" name="paymentMethod" id="stripe" value="stripe">
            <label class="form-check-label d-flex align-items-center" for="stripe" style="margin-left: 10px;">
              <img src="https://cdn.brandfetch.io/idxAg10C0L/theme/dark/logo.svg?c=1dxbfHSJFAPEGdCLU4o5B" alt="Stripe" width="80">
            </label>
          </div>
        </div>
      `
    );


     // Reusable function to create blocks
    const createProjectDataBlock = (id, icon, label, type = 'text', attributes = {}) => {
      blockManager.add(`project-data-${id}`, {
        label: `
          <div style="text-align: center;">
            <i class="bi ${icon}" style="font-size: 22px; display: block; margin-bottom: 5px;"></i>
            <span style="font-size: 12px; font-weight: bold;">${label}</span>
          </div>
        `,
        category: {
          id: 'project-data',
          label: 'Project Data',
          open: true,
        },
        content: `
          <div class="form-group">
            ${
              type === 'checkbox'
                ? `
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input">
                    <label class="form-check-label">${label}</label>
                  </div>
                `
                : type === 'textarea' && id === 'formatted-text'
                ? `
                  <label class="form-label">${label}:</label>
                  <textarea class="form-control" rows="4" placeholder="Enter ${label.toLowerCase()}"></textarea>
                `
                : type === 'textarea'
                ? `
                  <label class="form-label">${label}:</label>
                  <textarea class="form-control" rows="4" placeholder="Enter ${label.toLowerCase()}"></textarea>
                `
                : type === 'file'
                ? `
                  <label class="form-label">${label}:</label>
                  <input 
                    type="file" 
                    class="form-control" 
                    ${attributes.multiple ? 'multiple' : ''} 
                    ${attributes.array ? `name="${id}[]"` : `name="${id}"`}
                  >
                `
                : `
                  <label class="form-label">${label}:</label>
                  <input type="${type}" class="form-control" placeholder="Enter ${label.toLowerCase()}">
                `
            }
          </div>
        `,
      });
    };

    // Create blocks for Project Data
    createProjectDataBlock('order-title', 'bi-h-circle', 'Order Title');
    createProjectDataBlock('text', 'bi bi-file-text', 'Text');
    createProjectDataBlock('long-text', 'bi-textarea-t', 'Long Text', 'textarea');
    //createProjectDataBlock('formatted-text', 'bi-type-bold', 'Formatted Text', 'textarea'); // Quill editor
    createProjectDataBlock('date', 'bi-calendar', 'Date', 'date');
    createProjectDataBlock('checkbox', 'bi-check-square', 'Checkbox', 'checkbox');
    createProjectDataBlock('option-group', 'bi-ui-radios', 'Option Group', 'radio');
    createProjectDataBlock('dropdown', 'bi-chevron-down', 'Dropdown', 'select');
    createProjectDataBlock('file', 'bi-file-earmark', 'File', 'file', { multiple: true, array: true }); // File block with multiple and array attributes
    createProjectDataBlock('spreadsheet', 'bi-table', 'Spreadsheet', 'textarea');
    createProjectDataBlock('secret-text', 'bi-key', 'Secret Text', 'password');
    createProjectDataBlock('hidden', 'bi-eye-slash', 'Hidden', 'hidden');
    createProjectDataBlock('signature', 'bi-brush', 'Signature', 'text');
    createProjectDataBlock('calendly', 'bi-calendar3', 'Calendly', 'text');


      // Add a new category: Utilities
      const utilitiesCategory = {
        id: 'utilities',
        label: 'Utilities',
        open: true, // This keeps the category open by default
      };

      // Reusable function to create utility blocks
      const createUtilityBlock = (id, icon, label, content) => {
        blockManager.add(id, {
          label: `
            <div style="text-align: center;">
              <i class="${icon}" style="font-size: 22px; display: block; margin-bottom: 5px;"></i>
              <span style="font-size: 10px; font-weight: bold;">${label}</span>
            </div>
          `,
          category: utilitiesCategory,
          content: content,
        });
      };

      // Add "Section Break" block
      createUtilityBlock(
        'section-break',
        'bi bi-dash',
        'Section Break',
        `
          <hr style="border: 1px solid #ddd; margin: 20px 0;">
        `
      );

      // Add "Page Break" block
      createUtilityBlock(
        'page-break',
        'bi bi-layout-split',
        'Page Break',
        `
          <div style="padding: 10px; text-align: center; border: 1px dashed #ddd;">
            <span style="font-size: 14px; color: #555;">Page Break</span>
          </div>
        `
      );

      // Add "Custom HTML" block
      createUtilityBlock(
        'custom-html',
        'bi bi-code-slash',
        'Custom HTML',
        `
          <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
            <textarea class="form-control" rows="5" placeholder="Enter custom HTML here"></textarea>
          </div>
        `
      );

      // Add "Captcha" block
      /*
      createUtilityBlock(
        'captcha',
        'bi bi-shield-lock',
        'Captcha',
        `
          <div style="padding: 20px; text-align: center; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
            <span style="font-size: 14px; font-weight: bold; color: #555;">Captcha Placeholder</span>
          </div>
        `
      );
      */

      // Function to add blocks under "Others" category
    const addOthersBlock = (id, icon, label, content) => {
      editor.BlockManager.add(id, {
        label: `
          <div style="text-align: center;">
            <i class="${icon}" style="font-size: 22px; display: block; margin-bottom: 5px;"></i>
            <span style="font-size: 12px; font-weight: bold;">${label}</span>
          </div>
        `,
        category: 'others',
        content: content,
      });
    };

    // Add blocks
    addOthersBlock(
      'header-block',
      'fas fa-heading',
      'Header',
      `
        <header style="padding: 20px; background-color: #f8f9fa; display: flex; justify-content: space-between; align-items: center;">
          <div style="font-size: 24px; font-weight: bold;">LOGO</div>
          <nav>
            <ul style="display: flex; list-style: none; gap: 15px; margin: 0; padding: 0;">
              <li><a href="#" style="text-decoration: none; color: #333;">Home</a></li>
              <li><a href="#" style="text-decoration: none; color: #333;">About</a></li>
              <li><a href="#" style="text-decoration: none; color: #333;">Services</a></li>
              <li><a href="#" style="text-decoration: none; color: #333;">Contact</a></li>
            </ul>
          </nav>
        </header>
      `
    );

    addOthersBlock(
      'footer-block',
      'fas fa-shoe-prints',
      'Footer',
      `
        <footer style="padding: 40px; background-color: #333; color: #fff;">
          <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
              <h3 style="margin: 0;">LOGO</h3>
              <p style="margin: 0;">© 2025 All Rights Reserved</p>
            </div>
            <nav>
              <ul style="display: flex; list-style: none; gap: 15px; margin: 0; padding: 0;">
                <li><a href="#" style="text-decoration: none; color: #fff;">Privacy Policy</a></li>
                <li><a href="#" style="text-decoration: none; color: #fff;">Terms of Service</a></li>
              </ul>
            </nav>
          </div>
        </footer>
      `
    );

    addOthersBlock(
      '1-column-block',
      'fas fa-columns',
      '1 Column Div',
      `
        <div style="display: flex; flex-direction: column; gap: 10px;">
          <div style="flex: 1; padding: 10px; border: 1px solid #ddd; height: 150px; background-color: #f8f9fa;">Column 1</div>
        </div>
      `
    );

    addOthersBlock(
      '2-column-block',
      'fas fa-columns',
      '2 Column Div',
      `
        <div style="display: flex; gap: 10px;">
          <div style="flex: 1; padding: 10px; border: 1px solid #ddd; height: 150px; background-color: #f8f9fa;">Column 1</div>
          <div style="flex: 1; padding: 10px; border: 1px solid #ddd; height: 150px; background-color: #f8f9fa;">Column 2</div>
        </div>
      `
    );

    addOthersBlock(
      '3-column-block',
      'fas fa-columns',
      '3 Column Div',
      `
        <div style="display: flex; gap: 10px;">
          <div style="flex: 1; padding: 10px; border: 1px solid #ddd; height: 150px; background-color: #f8f9fa;">Column 1</div>
          <div style="flex: 1; padding: 10px; border: 1px solid #ddd; height: 150px; background-color: #f8f9fa;">Column 2</div>
          <div style="flex: 1; padding: 10px; border: 1px solid #ddd; height: 150px; background-color: #f8f9fa;">Column 3</div>
        </div>
      `
    );

    addOthersBlock(
      'form-block',
      'fas fa-clipboard',
      'Form',
      `<form>
        <div class="form-group">
          <label for="exampleInputEmail1">Email address</label>
          <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
        </div>
        <div class="form-group">
          <label for="exampleInputPassword1">Password</label>
          <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
      </form>`
    );

    // Add blocks
    addOthersBlock(
      'audio-block',
      'fas fa-music',
      'Audio',
      `<iframe
          src="http://webaudioapi.com/samples/audio-tag/chrono.mp3"
          style="width: 100%; height: 80px; border: none;"
          allow="autoplay"
          title="Audio Player">
      </iframe>`
    );

    addOthersBlock(
      'video-block',
      'fas fa-video',
      'Video',
      `<iframe
          src="https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4"
          style="width: 100%; height: 315px; border: none;"
          allow="autoplay; fullscreen"
          allowfullscreen
          title="Video Player">
      </iframe>`
    );

    addOthersBlock(
      'image-block',
      'fas fa-image',
      'Image',
      `<img src="https://fastly.picsum.photos/id/774/200/200.jpg?hmac=kHZuEL0Tzh_9wUk4BnU9zxodilE2mGBdAAor2hKpA_w" alt="Placeholder Image" style="width: 100%;">`
    );

    document.addEventListener('DOMContentLoaded', function () {
      const openBlocksButton = document.querySelector('.gjs-pn-btn.fa.fa-th-large');
      if (openBlocksButton) {
        openBlocksButton.click();
      }
    });

    let selectedComponent = null;
    let choicesInstance = null; // Separate Choices instance

    function initChoices(selectedTexts = []) {
      const select = document.getElementById('serviceListModal');
      select.innerHTML = ''; // Clear old options

      services.forEach(service => {
        const option = document.createElement('option');
        option.value = service.id;
        option.textContent = service.service_name;
        if (selectedTexts.includes(service.service_name)) {
          option.selected = true;
        }
        select.appendChild(option);
      });

      if (choicesInstance) {
        choicesInstance.destroy();
      }

      choicesInstance = new Choices(select, {
        removeItemButton: true,
        shouldSort: false,
      });

      const defaultTextInput = document.getElementById('serviceDefaultListModal');
      const selectElement = choicesInstance.passedElement.element;

      // Utility function to sync values
      function updateDefaultSelect(values) {
        defaultTextInput.innerHTML = ''; // Clear existing options

        // Add empty option for optional default selection
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = '-- Select default (optional) --';
        defaultTextInput.appendChild(emptyOption);

        // Add options from selected services
        values.forEach(id => {
          const matchedService = services.find(service => service.id == id);
          if (matchedService) {
            const option = document.createElement('option');
            option.value = matchedService.id;
            option.textContent = matchedService.service_name;
            defaultTextInput.appendChild(option);
          }
        });
      }

      selectElement.addEventListener('addItem', function () {
        const currentValues = choicesInstance.getValue(true); // returns array of ids
        updateDefaultSelect(currentValues);
      });

      selectElement.addEventListener('removeItem', function () {
        const currentValues = choicesInstance.getValue(true);
        updateDefaultSelect(currentValues);
      });
    }

    function openServiceModal(component, isNew = false) {
      const modal = new bootstrap.Modal(document.getElementById('serviceDropdownModal'));

      const fieldNameInput = document.getElementById('fieldNameInput');
      const defaultTextInput = document.getElementById('defaultTextInput');
      const requiredFieldCheckbox = document.getElementById('requiredField');
      const allowMultipleSelectionsCheckbox = document.getElementById('allowMultipleSelections');
      const allowMultipleQuantitiesCheckbox = document.getElementById('allowMultipleQuantities');

      const attrs = component.get('attributes') || {};
      console.log(attrs,'c');

      const el = component.getEl(); // Could be the <div> or <select>
      const selectEl2 = el.tagName === 'SELECT' ? el : el.querySelector('select');

      // Determine source of attributes
      const attrSource = el.hasAttribute('data-field-name') ? el : (selectEl2?.hasAttribute('data-field-name') ? selectEl2 : null);

      if (attrSource) {
        fieldNameInput.value = attrSource.getAttribute('data-field-name') || '';
        defaultTextInput.value = attrSource.getAttribute('data-default-text') || '';
        requiredFieldCheckbox.checked = attrSource.getAttribute('data-required') === 'true';
        allowMultipleSelectionsCheckbox.checked = attrSource.getAttribute('data-allow-multiple') === 'true';
        allowMultipleQuantitiesCheckbox.checked = attrSource.getAttribute('data-allow-quantities') === 'true';
      }

      let selectedServices = [];
      let selectEl = null;
      console.log(el,'eling');

      if (el.tagName === 'SELECT' && el.id.startsWith('serviceDropdownSelect-')) {
        selectEl = el;
      } else if (el.querySelector) {
        selectEl = el.querySelector('select[id^="serviceDropdownSelect-"]');
      }

      if (selectEl) {
        selectedServices = Array.from(selectEl.options)
          .filter(option => option.textContent.trim() !== '')
          .map(option => option.textContent.trim());
      }

      setTimeout(() => {
        initChoices(selectedServices);
        modal.show();
      }, 100);
    }

    // When a new component is added for dropdown
    editor.on('component:add', (component) => {
      setTimeout(() => {
        const el = component.getEl();
        console.log('element cccccc', el);
        if (el && el.classList && el.classList.contains('trigger-service-modal')) {
          selectedComponent = component;
          openServiceModal(selectedComponent, true);
        }
      }, 500);
    });

    // When a component is selected
    editor.on('component:selected', (component) => {
      setTimeout(() => {
        const el = component.getEl();
        if (!el) return;

        const isServiceDiv = el.classList.contains('trigger-service-modal');
        const isServiceSelect = el.id && el.id.startsWith('serviceDropdownSelect-');

        if (isServiceDiv || isServiceSelect) {
          selectedComponent = component;
          openServiceModal(selectedComponent, false);
        }
      }, 300);
    });

    // When confirm button clicked
    document.getElementById('confirmServices')?.addEventListener('click', () => {
        if (!selectedComponent) return;

        const fieldName = document.getElementById('fieldNameInput').value;
        const defaultText = document.getElementById('serviceDefaultListModal').value;
        const required = document.getElementById('requiredField').checked;
        const allowMultiple = document.getElementById('allowMultipleSelections').checked;
        const allowQuantities = document.getElementById('allowMultipleQuantities').checked;

        const selectedOptions = choicesInstance.getValue().map(opt => opt.value);
        const selectedLabels = choicesInstance.getValue().map(opt => opt.label);

        selectedComponent.setAttributes({
          'data-field-name': fieldName,
          'data-default-text': defaultText,
          'data-required': required,
          'data-allow-multiple': allowMultiple,
          'data-allow-quantities': allowQuantities,
          'data-selected-services': JSON.stringify(selectedOptions),
        });

        const el = selectedComponent.getEl();
        const select = el.tagName === 'SELECT' ? el : el.querySelector('select');

        if (select) {
          select.innerHTML = '';

          selectedLabels.forEach(serviceName => {
            const opt = document.createElement('option');
            opt.textContent = serviceName;

            // ✅ Mark this option as selected if it matches the default text
            if (serviceName === defaultText) {
              opt.selected = true;
            }

            select.appendChild(opt);
          });

          if (required) {
            select.setAttribute('required', 'required');
          } else {
            select.removeAttribute('required');
          }

          if (allowMultiple) {
            //select.setAttribute('multiple', 'multiple');
          } else {
            //select.removeAttribute('multiple');
          }
        }

        // 👇 Set the heading text to match the field name
        const heading = el.querySelector('h3');
        if (heading) {
          heading.textContent = fieldName || 'Select Services';
        }
    });


    // for service options code
    /*
    let servicesContent1 = `
        <div class="trigger-service-options-modal" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
          <div class="service-options-container">
            <h3 style="font-size: 18px; font-weight: bold; color: #333;">Select Services</h3>
            <div class="service-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px;">
              <!-- Will be dynamically populated -->
            </div>
          </div>
        </div>
      `;

    createBlock(
      'select-services',
      'fa-solid fa-th-large',
      'Service Options',
      { id: 'service-selection', label: 'Service Selection', open: true },
      servicesContent1
    );
    */

    let dropdownCounteroptionservice = 1; // Move this outside so it doesn't reset on every call
    function createOptionServiceDropdownBlock() {
      const uniqueId = `serviceOptionDropdownSelect-${dropdownCounteroptionservice++}`;

      createBlock(
        `select-option-services-${uniqueId}`, // Also make block ID unique
        'fa-solid fa-th-large',
        'Service Options',
        { id: 'service-selection', label: 'Service Selection', open: true },
        `
          <div class="trigger-service-options-modal" style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
            <div class="service-options-container" id="${uniqueId}">
              <h3 style="font-size: 18px; font-weight: bold; color: #333;">Select Services</h3>
              <div class="service-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px;">
                <!-- Will be dynamically populated -->
              </div>
            </div>
          </div>
        `
      );
    }
    
    createOptionServiceDropdownBlock();
    
    editor.on('component:add', (component) => {
      setTimeout(() => {
        const el = component.getEl();
        console.log('element', el);
        if (el && el.classList && el.classList.contains('service-options-container')) {
          selectedComponent = component;
          openServiceModal1(selectedComponent, true);
        }
      }, 500);
    });

    let choicesInstance1 = null; // Separate Choices instance
    function initChoices1(selectedTexts = []) {
      const select = document.getElementById('serviceListModal1');
      select.innerHTML = ''; // Clear old options

      services.forEach(service => {
        const option = document.createElement('option');
        option.value = service.id;
        option.textContent = service.service_name;
        if (selectedTexts.includes(service.id.toString())) {
          option.selected = true;
        }
        select.appendChild(option);
      });

      if (choicesInstance1) {
        choicesInstance1.destroy();
      }

      console.log(selectedTexts,'selectedTexts');
      console.log(select,'select');

      choicesInstance1 = new Choices(select, {
        removeItemButton: true,
        shouldSort: false,
      });

      const defaultTextInput = document.getElementById('serviceDefaultListModal1');
      const selectElement = choicesInstance1.passedElement.element;

      // Utility function to sync values
      function updateDefaultSelect(values) {
        defaultTextInput.innerHTML = ''; // Clear existing options

        // Add empty option for optional default selection
        const emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = '-- Select default (optional) --';
        defaultTextInput.appendChild(emptyOption);

        // Add options from selected services
        values.forEach(id => {
          const matchedService = services.find(service => service.id == id);
          if (matchedService) {
            const option = document.createElement('option');
            option.value = matchedService.id;
            option.textContent = matchedService.service_name;
            defaultTextInput.appendChild(option);
          }
        });
      }

      selectElement.addEventListener('addItem', function () {
        const currentValues = choicesInstance1.getValue(true); // returns array of ids
        updateDefaultSelect(currentValues);
      });

      selectElement.addEventListener('removeItem', function () {
        const currentValues = choicesInstance1.getValue(true);
        updateDefaultSelect(currentValues);
      });
    }

    function openServiceModal1(component, isNew = false) {
      const modal = new bootstrap.Modal(document.getElementById('serviceOptionsDropdownModal'));

      const fieldNameInput = document.getElementById('fieldNameInput1');
      const defaultTextInput = document.getElementById('serviceDefaultListModal1');
      const requiredFieldCheckbox = document.getElementById('requiredField1');
      const allowMultipleSelectionsCheckbox = document.getElementById('allowMultipleSelections1');
      const allowMultipleQuantitiesCheckbox = document.getElementById('allowMultipleQuantities1');

      const attrs = component.get('attributes') || {};
      console.log(attrs,component);


      fieldNameInput.value = attrs['data-field-name'] || '';
      requiredFieldCheckbox.checked = attrs['data-required'];
      allowMultipleSelectionsCheckbox.checked = attrs['data-allow-multiple'];
      allowMultipleQuantitiesCheckbox.checked = attrs['data-allow-quantities'];

      const selectedServiceIds = JSON.parse(attrs['data-selected-services'] || '[]');
      const defaultText = attrs['data-default-text'] || '';

      console.log(selectedServiceIds,'selectedServiceIds');

      setTimeout(() => {
        initChoices1(selectedServiceIds); // Preload multi-select
        // Set default selected value (optional)
        defaultTextInput.value = defaultText;
        modal.show();
      }, 100);
    }

    // When confirm button clicked
    document.getElementById('confirmOptionServices')?.addEventListener('click', () => {
      if (!selectedComponent) return;

      const fieldName = document.getElementById('fieldNameInput1').value;
      const defaultText = document.getElementById('serviceDefaultListModal1').value;
      const required = document.getElementById('requiredField1').checked;
      const allowMultiple = document.getElementById('allowMultipleSelections1').checked;
      const allowQuantities = document.getElementById('allowMultipleQuantities1').checked;

      const selectedOptions = choicesInstance1.getValue().map(opt => opt.value);
      const selectedLabels = choicesInstance1.getValue().map(opt => opt.label);

      selectedComponent.setAttributes({
        'data-field-name': fieldName,
        'data-default-text': defaultText,
        'data-required': required,
        'data-allow-multiple': allowMultiple,
        'data-allow-quantities': allowQuantities,
        'data-selected-services': JSON.stringify(selectedOptions),
      });

      const el = selectedComponent.getEl();
      const container = el.querySelector('.service-grid');

      // 👇 Set the heading text to match the field name
      const heading = el.querySelector('h3');
      if (heading) {
        heading.textContent = fieldName || 'Select Services';
      }

      if (container) {
        container.innerHTML = '';

        selectedOptions.forEach((serviceId) => {
          const service = services.find(s => s.id == serviceId);
          if (!service) return;

          // Format pricing info
          let price = '';
          if (service.service_type === 'recurring') {
            if (service.trial_for && service.trial_price) {
              price = `$${service.trial_price} for ${service.trial_for} ${service.trial_period || 'day'}${service.trial_for > 1 ? 's' : ''}, `;
            }
            price += `$${service.recurring_service_currency_value} / ${service.recurring_service_currency_value_two} ${service.recurring_service_currency_value_two_type}`;
          } else {
            price = `$${service.one_time_service_currency_value}`;
          }

          const box = document.createElement('div');
          box.classList.add('service-box');
          box.style.cssText = `
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: left;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
          `;

          box.innerHTML = `
            <label style="display: block; cursor: pointer;">
              <input 
                type="${allowMultiple ? 'checkbox' : 'radio'}" 
                name="service_option_${fieldName}" 
                value="${service.id}" 
                ${defaultText == service.id ? 'checked' : ''} 
                style="margin-right: 10px;" 
              />
              <strong>${service.service_name}</strong>
              <p style="margin: 5px 0 0; font-size: 14px; color: #555;">${price}</p>
            </label>
            ${
              allowQuantities
                ? `<select style="margin-top: 10px;" class="form-select">
                    ${[...Array(10)].map((_, i) => `<option value="${i + 1}">${i + 1}</option>`).join('')}
                  </select>`
                : ''
            }
          `;
          container.appendChild(box);
        });
      }
    });

    editor.on('component:selected', (component) => {
      setTimeout(() => {
        const el = component.getEl();
        if (!el) return;

        const isServiceDiv = el.classList.contains('service-options-container');
        const isServiceSelect = el.id && el.id.startsWith('serviceOptionDropdownSelect-');

        if (isServiceDiv || isServiceSelect) {
          selectedComponent = component;
          openServiceModal1(selectedComponent, false);
        }
      }, 300);
    });

  </script>
</html>
