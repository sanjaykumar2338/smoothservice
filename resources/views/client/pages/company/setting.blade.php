@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
    .remove-btn {
        text-decoration: none;
        color: #dc3545;
    }

    .form-label span.text-muted {
        display: block;
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 4px;
    }

    img {
        transition: transform 0.2s ease;
    }

    img:hover {
        transform: scale(1.05);
    }

</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Settings /</span> Company Settings
    </h4>

    <form method="POST" action="{{ route('company.update') }}" enctype="multipart/form-data">
        @csrf
        @if ($companySettings)
            @method('PUT')
        @endif

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Company Settings</h5>
            </div>
            <div class="card-body">

                <!-- Company Name -->
                <div class="mb-3">
                    <label class="form-label" for="company_name">Company Name</label>
                    <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $companySettings->company_name ?? old('company_name') }}" required>
                </div>

                <!-- Custom Domain -->
                <div class="mb-3">
                    <label class="form-label" for="custom_domain">Custom Domain</label>
                    <input type="text" 
                        class="form-control" 
                        id="custom_domain" 
                        name="custom_domain" 
                        placeholder="e.g. sub.domain.com" 
                        value="{{ $companySettings->custom_domain ?? old('custom_domain') }}"
                        {{ isset($companySettings->domain_verified) && $companySettings->domain_verified ? 'disabled' : '' }}>
                    <small class="form-text text-muted">
                        Please add the following IP address to your DNS settings as an `A Record`: 
                        <code>18.209.182.185</code>
                    </small>
                    <button type="button" class="btn btn-primary mt-2" id="verify_domain" {{ isset($companySettings->domain_verified) && $companySettings->domain_verified ? 'disabled' : '' }}>Verify</button>
                    <button type="button" class="btn btn-danger mt-2" id="remove_verification" {{ isset($companySettings->domain_verified) && $companySettings->domain_verified ? '' : 'disabled' }}>Remove</button>
                    <div id="verification_result" class="mt-2"></div>
                    <input type="hidden" id="domain_verified" name="domain_verified" value="{{ $companySettings->domain_verified ?? 0 }}">
                </div>

                <!-- Timezone -->
                <div class="mb-3">
                    <label class="form-label" for="timezone">Timezone</label>
                    <select class="form-control" id="timezone" name="timezone">
                        @foreach (timezone_identifiers_list() as $timezone)
                            <option value="{{ $timezone }}" {{ ($companySettings->timezone ?? old('timezone')) == $timezone ? 'selected' : '' }}>
                                {{ $timezone }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sidebar Color -->
                <div class="mb-3">
                    <label class="form-label" for="sidebar_color">Sidebar Color</label>
                    <input type="color" class="form-control form-control-color" id="sidebar_color" name="sidebar_color" value="{{ $companySettings->sidebar_color ?? '#f3f4f4' }}">
                </div>

                <!-- Accent Color -->
                <div class="mb-3">
                    <label class="form-label" for="accent_color">Accent Color</label>
                    <input type="color" class="form-control form-control-color" id="accent_color" name="accent_color" value="{{ $companySettings->accent_color ?? '#f50004' }}">
                </div>

                <!-- Contact Link -->
                <div class="mb-3">
                    <label class="form-label" for="contact_link">Contact Link</label>
                    <input type="url" class="form-control" id="contact_link" name="contact_link" placeholder="e.g. https://yourcompany.com/contact" value="{{ $companySettings->contact_link ?? old('contact_link') }}">
                </div>

                <!-- Image Upload Sections -->

                @php
                    $logoDescriptions = [
                        'logo' => 'Shown in the login screen. Transparent PNG recommended, 480px wide.',
                        'favicon' => 'Shown in browser tabs and collapsed sidebar. 144×144px PNG recommended.',
                        'application_icon' => 'Mobile app icon. Shown on the home screen. 1024×1024px PNG recommended.',
                        'sidebar_logo' => 'Shown in the sidebar. Transparent PNG recommended, 480px wide.',
                    ];
                @endphp

                @foreach ([
                    'logo' => 'Logo',
                    'favicon' => 'Favicon',
                    'application_icon' => 'Application Icon',
                    'sidebar_logo' => 'Sidebar Logo'
                ] as $field => $label)
                    <div class="mb-3">
                        <label class="form-label" for="{{ $field }}">
                            Upload {{ $label }} 
                            @if (isset($logoDescriptions[$field]))
                                : {{ $logoDescriptions[$field] }}
                            @endif
                        </label>
                        <input type="file" class="form-control" id="{{ $field }}" name="{{ $field }}">

                        @if (isset($companySettings->$field) && $companySettings->$field)
                            <div class="mt-2 d-flex align-items-center">
                                <div>
                                    <p class="mb-1" style="font-size: large;">Current {{ $label }}:</p>
                                    <a href="{{ asset('storage/' . $companySettings->$field) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $companySettings->$field) }}" alt="{{ $label }}"
                                            style="width: {{ $field === 'favicon' ? '32px' : '100px' }}; height: auto; border: 1px solid #ddd; border-radius: 4px;">
                                    </a>
                                </div>
                                <div class="ms-3">
                                    <a href="{{ route('company.image.remove', ['type' => $field]) }}" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i> Remove {{ $label }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach


                <!-- SPP Linkback -->
                <div class="mb-3 form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="spp_linkback" name="spp_linkback" value="1" {{ ($companySettings->spp_linkback ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label" for="spp_linkback">SPP Linkback</label>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>

<script>
    document.querySelector('form').addEventListener('submit', function (e) {
        const domainInput = document.getElementById('custom_domain');

        if (domainInput.disabled) {
            domainInput.disabled = false; // Enable the input field temporarily
            setTimeout(() => {
                domainInput.disabled = true; // Re-disable the input field after submission
            }, 0);
        }
    });

    document.getElementById('verify_domain').addEventListener('click', function () {
        const domainInput = document.getElementById('custom_domain');
        const verificationResult = document.getElementById('verification_result');
        const removeButton = document.getElementById('remove_verification');
        const verifyButton = document.getElementById('verify_domain');
        const domainVerified = document.getElementById('domain_verified');

        if (!domainInput.value.trim()) {
            verificationResult.innerHTML = '<span style="color: red;">Please enter a domain to verify.</span>';
            return;
        }

        // Call your server to verify the domain
        fetch(`/verify-domain?domain=${encodeURIComponent(domainInput.value.trim())}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    verificationResult.innerHTML = '<span style="color: green;">Domain verified successfully!</span>';
                    domainInput.disabled = true; // Disable the input field
                    verifyButton.disabled = true; // Disable the verify button
                    removeButton.disabled = false; // Enable the remove button
                    domainVerified.value = 1; // Set verified to true
                } else {
                    verificationResult.innerHTML = `<span style="color: red;">Verification failed: ${data.message}</span>`;
                }
            })
            .catch(err => {
                verificationResult.innerHTML = `<span style="color: red;">Error: Unable to verify the domain. Please try again.</span>`;
            });
    });

    document.getElementById('remove_verification').addEventListener('click', function () {
        const domainInput = document.getElementById('custom_domain');
        const verificationResult = document.getElementById('verification_result');
        const removeButton = document.getElementById('remove_verification');
        const verifyButton = document.getElementById('verify_domain');
        const domainVerified = document.getElementById('domain_verified');

        // Clear the domain field and reset verification status
        domainInput.disabled = false; // Enable the input field
        domainInput.value = '';
        domainVerified.value = 0; // Set verified to false
        verifyButton.disabled = false; // Enable the verify button
        removeButton.disabled = true; // Disable the remove button

        verificationResult.innerHTML = '<span style="color: red;">Domain verification removed.</span>';
    });

    document.addEventListener("DOMContentLoaded", function () {
        // Get the input fields and theme elements
        const sidebarColorInput = document.getElementById("sidebar_color");
        const accentColorInput = document.getElementById("accent_color");
        
        const themeElements = document.querySelectorAll(".bg-menu-theme");
        const themeInnerActiveElements = document.querySelectorAll(".bg-menu-theme .menu-inner > .menu-item.active");
        const menuInnerShadowElements = document.querySelectorAll(".bg-menu-theme .menu-inner-shadow");

        // Get buttons or other elements you want to change color
        const buttonElements = document.querySelectorAll(".btn"); // Modify according to your button classes

        // Function to update the theme colors with !important
        function updateThemeColor(color, elements, property = 'background-color') {
            elements.forEach(element => {
                element.style.setProperty(property, color, 'important');
            });
        }

        // Function to update button color
        function updateButtonColor(color) {
            updateThemeColor(color, buttonElements, 'background-color');
        }

        // Set the initial colors from the input values
        updateThemeColor(sidebarColorInput.value, themeElements);
        updateThemeColor(sidebarColorInput.value, themeInnerActiveElements);
        updateThemeColor(sidebarColorInput.value, menuInnerShadowElements, 'background');

        updateButtonColor(accentColorInput.value);

        // Listen for changes in the sidebar color input
        sidebarColorInput.addEventListener("input", function () {
            updateThemeColor(sidebarColorInput.value, themeElements);
            updateThemeColor(sidebarColorInput.value, themeInnerActiveElements);
            updateThemeColor(sidebarColorInput.value, menuInnerShadowElements, 'background');
        });

        // Listen for changes in the accent color input
        accentColorInput.addEventListener("input", function () {
            updateButtonColor(accentColorInput.value);
        });
    });
</script>

@endsection
