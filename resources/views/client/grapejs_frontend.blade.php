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

    <link
      href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css"
      rel="stylesheet"
    />

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .gjs-pn-panels, .gjs-off-prv{
            display: none !important;
        }
    </style>
  </head>

  <body>
      <div id="gjs" style=""></div>
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
        fromElement: false,
        storageManager: false, // Disable storage
        panels: { defaults: [] }, // Hide all UI panels
        blockManager: null, // Hide block manager
        styleManager: null, // Hide styles
        layerManager: null, // Hide layers
        selectorManager: null, // Hide selectors
        traitManager: null, // Hide traits
        deviceManager: null, // Hide device manager
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

    // Load saved data and switch to preview mode
    editor.on('load', function () {
        editor.Commands.run('preview'); // Enable preview mode automatically
        console.log('GrapesJS loaded in preview mode');
    });

    // Function to load saved page content
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

    </script>
</html>
