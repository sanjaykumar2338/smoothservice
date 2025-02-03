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

    <link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet" />
  </head>

  @include('client.custom_settings')

  <body>
    @yield('content')
  </body>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://malsup.github.io/jquery.blockUI.js"></script>
  <script src="https://unpkg.com/grapesjs"></script>
  <script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2"></script>
  <script src="https://unpkg.com/grapesjs-plugin-forms@2.0.5"></script>
  <script src="https://unpkg.com/grapesjs-blocks-basic@1.0.1"></script>
  <script>
      var editor = grapesjs.init({
        container: '#gjs',
        width: 'auto',
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

      // Service Selection Category
      createBlock(
        'select-services',
        'fa-solid fa-bars',
        'Options',
        { id: 'service-selection', label: 'Service Selection', open: true },
        `
          <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
            <h3 style="font-size: 18px; font-weight: bold; color: #333;">Select Services</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px;">
              <div style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; text-align: center; background: #fff; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                <input type="checkbox" id="service1" style="margin-bottom: 10px;">
                <label for="service1" style="font-size: 16px; font-weight: bold;">Service No Trail</label>
                <p style="font-size: 14px; color: #555;">$60.00 / 2 weeks</p>
                <select style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
              </div>
            </div>
          </div>
        `
      );

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
                <option value="service1">Service No Trail - $60.00 / 2 weeks</option>
                <option value="service2">Test Service 2 - $20.00 / 2 months</option>
                <option value="service3">Test Service 1 - $5.00 / 2 weeks</option>
              </select>
            </div>
          </div>
        `
      );

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
        const icons = {
          name: 'bi bi-person',
          email: 'bi bi-envelope',
          password: 'bi bi-lock',
          address: 'bi bi-geo-alt',
          phone: 'bi bi-telephone',
          'email-optin': 'bi bi-check-circle',
        };
        createBlock(
          `client-${field}`,
          icons[field],
          labels[field],
          { id: 'client-section', label: 'Client Section', open: true },
          `
            <div class="d-flex align-items-center gap-2 p-3 border rounded bg-light">
              <i class="${icons[field]}" style="font-size: 22px;"></i>
              <span>${labels[field]}</span>
            </div>
          `
        );
      });

      // Billing Details
      createBlock(
        'payment',
        'bi bi-credit-card',
        'Payment',
        { id: 'billing-details', label: 'Billing Details', open: true },
        `
          <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
            <h3 style="font-size: 18px; font-weight: bold;">Payment</h3>
            <div style="margin-top: 20px;">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="paypal" checked>
                <label class="form-check-label" for="paypal">PayPal</label>
              </div>
            </div>
          </div>
        `
      );


        // Reusable function to create blocks
        const createProjectDataBlock = (id, icon, label, type = 'text') => {
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
              <div class="p-3 border rounded bg-light">
                <h3 style="font-size: 18px; font-weight: bold;">${label}</h3>
                ${
                  type === 'textarea'
                    ? `<textarea class="form-control" rows="4" placeholder="Enter ${label.toLowerCase()}"></textarea>`
                    : `<input type="${type}" class="form-control" placeholder="Enter ${label.toLowerCase()}">`
                }
              </div>
            `,
          });
        };

        // Create blocks for Project Data
        createProjectDataBlock('order-title', 'bi-h-circle', 'Order Title');
        createProjectDataBlock('text', 'bi bi-file-text', 'Text');
        createProjectDataBlock('long-text', 'bi-textarea-t', 'Long Text', 'textarea');
        createProjectDataBlock('formatted-text', 'bi-type-bold', 'Formatted Text', 'textarea');
        createProjectDataBlock('date', 'bi-calendar', 'Date', 'date');
        createProjectDataBlock('checkbox', 'bi-check-square', 'Checkbox', 'checkbox');
        createProjectDataBlock('option-group', 'bi-ui-radios', 'Option Group', 'radio');
        createProjectDataBlock('dropdown', 'bi-chevron-down', 'Dropdown', 'select');
        createProjectDataBlock('file', 'bi-file-earmark', 'File', 'file');
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


        document.addEventListener('DOMContentLoaded', function () {
          const openBlocksButton = document.querySelector('.gjs-pn-btn.fa.fa-th-large');
          if (openBlocksButton) {
            openBlocksButton.click();
          }
        });

  </script>
</html>
