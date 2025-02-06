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

      editor.on('load', () => {
          const components = editor.getComponents();
          components.reset(); // Clears the added components but keeps the structure intact
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

      // Add block with dynamic content
      createBlock(
        'select-services',
        'fa-solid fa-bars',
        'Service Options',
        { id: 'service-selection', label: 'Service Selection', open: true },
        servicesContent
      );

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
        "bi bi-credit-card",
        "Payment",
        { id: "billing-details", label: "Billing Details", open: true },
        `
          <div class="p-3 border rounded bg-light">
            <h3 style="font-size: 18px; font-weight: bold;">Payment</h3>
            <div style="margin-top: 20px;">
              <label class="form-label" style="font-size: 16px; font-weight: bold; display: block; margin-bottom: 5px;">
                <i class="bi bi-credit-card" style="margin-right: 5px;"></i> Select Payment Method
              </label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="paymentMethod" id="paypal" value="paypal" checked>
                <label class="form-check-label" for="paypal">PayPal</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="paymentMethod" id="credit-card" value="credit-card">
                <label class="form-check-label" for="credit-card">Credit Card</label>
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
            <div class="form-group">
              ${
                type === 'checkbox'
                  ? `
                    <div class="form-check">
                      <input id="${id}" type="checkbox" class="form-check-input">
                      <label for="${id}" class="form-check-label">${label}</label>
                    </div>
                  `
                  : type === 'textarea'
                  ? `<textarea id="${id}" class="form-control" rows="4" placeholder="Enter ${label.toLowerCase()}"></textarea>`
                  : `<label for="${id}" class="form-label">${label}:</label>
                    <input id="${id}" type="${type}" class="form-control" placeholder="Enter ${label.toLowerCase()}">`
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

  </script>
</html>
