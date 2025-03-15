<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ env('APP_NAME') }}</title>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <!-- GrapesJS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/grapesjs/dist/css/grapes.min.css">
    
    @php
        $company_settings = App\Models\CompanySetting::where('user_id', auth()->id())->first();
    @endphp

    @if($company_settings && $company_settings->favicon && file_exists(public_path('storage/' . $company_settings->favicon)))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $company_settings->favicon) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('/assets/img/favicon/favicon.ico') }}" />
    @endif
    
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
            <div class="summary-box" style="display:none;">
                
            </div>
        </div>

        <!-- GrapesJS Editor (Form Area) -->
        <div class="editor-container">
            <div id="gjs"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/grapesjs@0.20.4/dist/grapes.min.js"></script>

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
                        editor.on('load', () => editor.runCommand('preview'));
                    }
                })
                .catch(error => console.error('Error loading:', error));
        };
        
        setTimeout(function(){
            editor.runCommand('preview');
        },300);

        document.addEventListener('DOMContentLoaded', function () {

            function getCheckedServices() {
                let checkedServices = [];

                // Get all checked checkboxes inside the GrapesJS editor
                const checkboxes = editor.Canvas.getDocument().querySelectorAll('input[type="checkbox"]:checked');

                checkboxes.forEach((checkbox) => {
                    let serviceId = checkbox.id.replace('service-', ''); // Extract numeric part
                    checkedServices.push(serviceId);
                });

                console.log("Selected Service IDs:", checkedServices);
                updateSummary(checkedServices); // Call update summary on change
            }

            // Attach event listener to detect checkbox changes (both checked and unchecked)
            editor.on('load', function () {
                const iframeDocument = editor.Canvas.getDocument();

                iframeDocument.addEventListener('change', function (event) {
                    if (event.target.type === 'checkbox' || (event.target.tagName === 'SELECT' && event.target.id === 'service-dropdown')) {
                        getSelectedServicesAndUpdateSummary();
                    }
                });
            });

            // Function to get selected services from both dropdown and checkboxes
            var selectedServices = [];
            function getSelectedServicesAndUpdateSummary() {
                const iframeDocument = editor.Canvas.getDocument();
                selectedServices = [];
                
                // Get all checked checkboxes inside the GrapesJS editor
                const checkboxes = iframeDocument.querySelectorAll('input[type="checkbox"]:checked');
                checkboxes.forEach((checkbox) => {
                    let serviceId = checkbox.id.replace('service-', ''); // Extract numeric part
                    selectedServices.push(serviceId);
                });

                // Get selected value from the dropdown (if exists and has a value)
                const serviceDropdown = iframeDocument.querySelector('#service-dropdown');
                if (serviceDropdown && serviceDropdown.value) {
                    selectedServices.push(serviceDropdown.value);
                }

                console.log("Selected Services (Checkbox + Dropdown):", selectedServices);

                // If no service is selected, return early
                if (selectedServices.length === 0) {
                    console.warn("No services selected");
                    return;
                }

                updateSummary(selectedServices);
            }

            // Function to update summary via AJAX (combined for both dropdown & checkbox)
            function updateSummary(selectedServices) {
                const summaryBox = document.querySelector('.summary-box');
                summaryBox.style.display = 'block';

                // Preserve the existing payment method section
                let existingPaymentMethod = summaryBox.querySelector('.payment-method');
                let paymentHTML = existingPaymentMethod ? existingPaymentMethod.outerHTML : '';

                // Show loader while updating summary
                summaryBox.innerHTML = `
                    <div class="text-center">
                        <span class="spinner-border spinner-border-sm"></span> Updating summary...
                    </div>
                    ${paymentHTML} <!-- Keep payment method -->
                `;

                fetch('/services-summary', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ services: selectedServices })
                })
                .then(response => response.json())
                .then(data => {
                    console.log("Updated Summary:", data);

                    let paymentBreakdown = `<p><strong>Total:</strong> $${data.total} CAD</p>`;
                    if (data.next_payment_recurring > 0) {
                        paymentBreakdown += `<p><strong>Recurring Payment:</strong> $${data.next_payment_recurring} per ${data.interval} ${data.interval_type}(s)</p>`;
                    }

                    summaryBox.innerHTML = `
                        <h4>Summary</h4>
                        <p>Total Services: <strong>${selectedServices.length}</strong></p>
                        <p>Trial Amount: <strong>$${data.trial_amount}</strong></p>
                        <p>Total Discount: <strong>$${data.total_discount}</strong></p>
                        <hr>
                        ${paymentBreakdown}
                        <hr>
                        ${paymentHTML} <!-- Restore payment method -->
                    `;
                })
                .catch(error => {
                    console.error('Error fetching summary:', error);
                    summaryBox.innerHTML = `<p class="text-danger">Error updating summary</p>${paymentHTML}`;
                });
            }
            
            function updatePaymentMethod() {
                const iframeDocument = editor.Canvas.getDocument();
                const paypalRadio = iframeDocument.querySelector('input[type="radio"][name="paymentMethod"][value="paypal"]');
                const stripeRadio = iframeDocument.querySelector('input[type="radio"][name="paymentMethod"][value="stripe"]');

                let selectedPayment = "Stripe"; // Default payment method

                if (paypalRadio && stripeRadio) {
                    selectedPayment = paypalRadio.checked ? "PayPal" : "Stripe";
                } else if (paypalRadio) {
                    selectedPayment = "PayPal";
                }

                const summaryBox = document.querySelector('.summary-box');
                summaryBox.style.display = 'block';

                let paymentMethodElement = summaryBox.querySelector('.payment-method');

                if (!paymentMethodElement) {
                    paymentMethodElement = document.createElement('div');
                    paymentMethodElement.classList.add('payment-method');
                    summaryBox.appendChild(paymentMethodElement);
                }

                //paymentMethodElement.innerHTML = `<p><strong>Payment Method:</strong> ${selectedPayment}</p>`;

                // Create Bootstrap buttons for payment
                let buttonContainer = document.createElement('div');
                buttonContainer.style.marginTop = "10px";

                if (paypalRadio && stripeRadio) {
                    buttonContainer.innerHTML = `
                        <button class="btn btn-primary pay-btn" data-method="stripe">
                            <i class="bi bi-credit-card"></i> Pay with Stripe
                        </button>
                        <button class="btn btn-warning pay-btn" data-method="paypal">
                            <i class="bi bi-paypal"></i> Pay with PayPal
                        </button>
                    `;
                } else if (paypalRadio) {
                    buttonContainer.innerHTML = `
                        <button class="btn btn-warning pay-btn" data-method="paypal">
                            <i class="bi bi-paypal"></i> Pay with PayPal
                        </button>
                    `;
                } else {
                    buttonContainer.innerHTML = `
                        <button class="btn btn-primary pay-btn" data-method="stripe">
                            <i class="bi bi-credit-card"></i> Pay with Stripe
                        </button>
                    `;
                }

                paymentMethodElement.appendChild(buttonContainer);

                paymentMethodElement.querySelectorAll('.pay-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        let method = this.getAttribute('data-method');
                        alert(`Processing payment with ${method.toUpperCase()}`);
                        // Implement actual payment logic here...
                    });
                });

                // Check if at least one payment method exists
                if (paypalRadio || stripeRadio) {
                    // Find the most parent div with data-gjs-type="default"
                    let parentDiv = paypalRadio?.closest('div[data-gjs-type="default"]') || 
                                    stripeRadio?.closest('div[data-gjs-type="default"]');
                    console.log(parentDiv,'parentDiv')

                    // Remove only if the parent exists
                    if (parentDiv) {
                        parentDiv.remove();
                        console.log("Removed the most parent div containing the payment methods.");
                    }
                } else {
                    console.log("No PayPal or Stripe radio buttons found, no parent div removed.");
                }
            }

            // Check the payment method when the page loads
            editor.on('load', function () {
                //updatePaymentMethod();
                addCompletePurchaseButton();
            });

            // Detect changes when user selects a different payment method
            editor.on('component:update', function (component) {
                if (component.attributes.tagName === 'input' && component.view.el.name === 'paymentMethod') {
                    updatePaymentMethod();
                }
            });

            function addCompletePurchaseButton() {
                const iframeDocument = editor.Canvas.getDocument();
                const paypalRadio = iframeDocument.querySelector('input[type="radio"][name="paymentMethod"][value="paypal"]');
                const stripeRadio = iframeDocument.querySelector('input[type="radio"][name="paymentMethod"][value="stripe"]');

                const editorContainer = document.querySelector('.editor-container');

                // Create button element
                let completePurchaseButton = document.createElement('button');
                completePurchaseButton.textContent = 'Complete Purchase';
                completePurchaseButton.classList.add('btn', 'btn-primary', 'w-100', 'mt-4');
                completePurchaseButton.style.fontSize = '18px';
                completePurchaseButton.style.padding = '12px';
                completePurchaseButton.style.borderRadius = '5px';

                // Add event listener for the button
                completePurchaseButton.addEventListener('click', function () {
                    // Disable button and change text
                    completePurchaseButton.disabled = true;
                    completePurchaseButton.textContent = 'Processing...';

                    let formData = {};
                    let emailField = editor.Canvas.getDocument().querySelector('input[type="email"], input[name="email"]');
                    const allInputs = editor.Canvas.getDocument().querySelectorAll('input, select, textarea');

                    allInputs.forEach((input) => {
                        if (input.type === "radio") {
                            if (input.checked) {
                                formData[input.name] = input.value; // Store only selected radio buttons
                            }
                        } else if (input.type === "checkbox") {
                            formData[input.name] = input.checked ? input.value : null; // Store checked checkbox values
                        } else {
                            formData[input.name] = input.value; // Store text, email, select, textarea values
                        }
                    });

                    if (!formData.email) {
                        alert('Error: An email field is required to complete the purchase.');
                        completePurchaseButton.textContent = 'Complete Purchase'; // Restore button text
                        completePurchaseButton.disabled = false; // Re-enable button
                        return;
                    }

                    formData['landing_page'] = '{{$slug}}';
                    formData['selectedServices'] = selectedServices;

                    console.log(selectedServices, formData);

                    fetch('/order/landingpage', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Updated Summary:", data);

                        if (data.invoice_id) {
                            //window.location.href = `/order/landingpage/payment/${data.invoice_id}`;
                            if(paypalRadio){
                                window.location.href = `/portal/invoice/payment/paypal/${data.invoice_id}`;
                            }else{
                                window.location.href = `/portal/invoice/payment/${data.invoice_id}`;
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching summary:', error);
                        alert('Failed to process request. Please try again.');
                    })
                    .finally(() => {
                        // Restore button text and re-enable after request completes
                        completePurchaseButton.textContent = 'Complete Purchase';
                        completePurchaseButton.disabled = false;
                    });
                });

                // Append the button at the bottom of the editor container
                editorContainer.appendChild(completePurchaseButton);
            }

        });
    </script>
</body>
</html>
