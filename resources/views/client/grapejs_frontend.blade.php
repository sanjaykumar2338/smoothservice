<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ env('APP_NAME') }}</title>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <!-- GrapesJS -->
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    
    <style>
        /* Full-screen container */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'IBM Plex Sans', sans-serif;
            background-color: #121212;
        }

        /* Layout Wrapper */
        .editor-wrapper {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 30%;
            background-color: #1a1a2e; /* Dark Theme */
            padding: 20px;
            color: white;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .sidebar h2 {
            color: #ff4757;
            margin-bottom: 30px;
        }

        .summary-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            width: 100%;
            text-align: left;
        }

        /* GrapesJS Canvas */
        .editor-container {
            flex-grow: 1;
            background: white;
            padding: 40px;
            overflow-y: auto;
        }

        /* Hide GrapesJS UI Panels */
        .gjs-pn-panels, .gjs-off-prv {
            display: none !important;
        }

        /* Custom styling for loaded content */
        #gjs {
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="editor-wrapper">
        <!-- Left Sidebar -->
        <div class="sidebar">
            <h2>{{env('APP_NAME')}}</h2>
            <div class="summary-box">
                <h4>Summary</h4>
                <p>Test Service 2 - <strong>$20.00</strong></p>
                <hr>
                <p><strong>Total:</strong> $20.00 CAD</p>
            </div>
        </div>

        <!-- GrapesJS Editor (Form Area) -->
        <div class="editor-container">
            <div id="gjs"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/grapesjs"></script>

    <script>
        var editor = grapesjs.init({
            container: '#gjs',
            width: 'auto',
            height: '100%',
            canvas: {
                styles: [
                    'https://fonts.googleapis.com/css?family=IBM+Plex+Sans:300,400,500,600,700',
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css'
                ],
                scripts: [
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
                ]
            }
        });

        // Fetch content dynamically
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
        
        editor.on('load', () => editor.runCommand('preview'));

        document.addEventListener('DOMContentLoaded', function () {
          // Function to track checked checkboxes
          function getCheckedServices() {
              let checkedServices = [];
              
              // Get all checked checkboxes inside the GrapesJS editor
              const checkboxes = editor.Canvas.getDocument().querySelectorAll('input[type="checkbox"]:checked');
              
              checkboxes.forEach((checkbox) => {
                  checkedServices.push(checkbox.id); // Push checkbox ID to array
              });

              console.log("Selected Service IDs:", checkedServices);
              return checkedServices;
          }

          // Attach event listener to dynamically detect checkbox changes
          editor.on('load', function () {
              const iframeDocument = editor.Canvas.getDocument();

              iframeDocument.addEventListener('change', function (event) {
                  if (event.target.type === 'checkbox') {
                      getCheckedServices(); // Call function when checkbox state changes
                  }
              });
          });
      });
    </script>
</body>
</html>
