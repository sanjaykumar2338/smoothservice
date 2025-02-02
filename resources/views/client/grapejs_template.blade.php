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
      });

      // Add a block with a Bootstrap icon
      const blockManager = editor.BlockManager;

      // Define the "Service Selection" category
        blockManager.add('select-services', {
          label: `
            <div style="display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-bars" style="font-size: 22px;"></i>
              <span style="font-size: 12px; font-weight: bold;">Options</span>
            </div>
          `,
          category: {
            id: 'service-selection',
            label: 'Service Selection',
            open: true, // This keeps the category open by default
          },
          content: `
            <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
              <h3 style="font-size: 18px; font-weight: bold; color: #333;">Select Services</h3>
              <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px;">
                <!-- Service Card 1 -->
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
          `,
        });

        blockManager.add('select-services-dropdown', {
          label: `
            <div style="display: flex; align-items: center; gap: 8px;">
              <i class="fa-solid fa-caret-down" style="font-size: 22px;"></i>
              <span style="font-size: 10px; font-weight: bold;">Dropdown</span>
            </div>
          `,
          category: {
            id: 'service-selection',
            label: 'Service Selection',
            open: true, // Keeps the category open by default
          },
          content: `
            <div style="padding: 20px; border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;">
              <h3 style="font-size: 18px; font-weight: bold; color: #333;">Select Services</h3>
              
              <!-- Dropdown Section -->
              <div style="margin-top: 15px; text-align: left;">
                <label for="service-dropdown" style="font-size: 16px; font-weight: bold; display: block; margin-bottom: 5px;">Available Services</label>
                <select id="service-dropdown" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                  <option value="service1">Service No Trail - $60.00 / 2 weeks</option>
                  <option value="service2">Test Service 2 - $20.00 / 2 months</option>
                  <option value="service3">Test Service 1 - $5.00 / 2 weeks</option>
                </select>
              </div>
            </div>
          `,
        });

        document.addEventListener('DOMContentLoaded', function () {
          const openBlocksButton = document.querySelector('.gjs-pn-btn.fa.fa-th-large');
          if (openBlocksButton) {
            openBlocksButton.click();
          }
        });

  </script>
</html>
