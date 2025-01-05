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
                    <input type="text" class="form-control" id="custom_domain" name="custom_domain" placeholder="e.g. yourcompany.com" value="{{ $companySettings->custom_domain ?? old('custom_domain') }}">
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
                    <input type="color" class="form-control form-control-color" id="sidebar_color" name="sidebar_color" value="{{ $companySettings->sidebar_color ?? '#002750' }}">
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

                @foreach ([
                    'logo' => 'Logo',
                    'favicon' => 'Favicon',
                    'application_icon' => 'Application Icon',
                    'sidebar_logo' => 'Sidebar Logo'
                ] as $field => $label)
                <div class="mb-3">
                    <label class="form-label" for="{{ $field }}">Upload {{ $label }}</label>
                    <input type="file" class="form-control" id="{{ $field }}" name="{{ $field }}">
                    @if (isset($companySettings->$field) && $companySettings->$field)
                        <div class="mt-2">
                            <p>Current {{ $label }}:</p>
                            <a href="{{ asset('storage/' . $companySettings->$field) }}" target="_blank">
                                <img src="{{ asset('storage/' . $companySettings->$field) }}" alt="{{ $label }}" style="width: {{ $field === 'favicon' ? '32px' : '100px' }}; height: auto;">
                            </a>
                            <a href="{{ route('company.image.remove', ['type' => $field]) }}" style="display: inline;">
                                <i class="fas fa-trash-alt"></i> Remove {{ $label }}
                            </a>
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

@endsection
