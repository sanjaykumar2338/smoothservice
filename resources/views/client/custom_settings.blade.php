@php
    $sidebarColor = App\Models\CompanySetting::getSidebarColorForUser(auth()->id());
    $accent_color = App\Models\CompanySetting::getAccentColorUser(auth()->id());
@endphp

@if ($sidebarColor)
    <style>
        .bg-menu-theme {
            background-color: {{ $sidebarColor }} !important;
        }

        .bg-menu-theme .menu-inner-shadow {
            background: none !important;
        }

        .bg-menu-theme .menu-inner > .menu-item.active {
            background-color: {{ $sidebarColor }} !important;
        }
    </style>
@endif

@if ($accent_color)
    <style>
        .btn-primary{
            background-color: {{ $accent_color }} !important;
        }

        .btn-danger, .btn-danger[data-trigger=hover].dropdown-toggle:not(.show){
            border-color: {{ $accent_color }} !important;
        }
    </style>
@endif