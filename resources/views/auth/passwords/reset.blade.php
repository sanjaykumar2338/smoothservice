@extends('auth.auth_template')

@section('content')

<p class="mb-4">Reset Password</p>
@if(session('status'))
    {{session('status')}}
@endif
<form id="formAuthentication" class="mb-3" method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input
            type="email"
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            name="email"
            placeholder="Enter your email"
            value="{{ $email ?? old('email') }}"
            required
            autofocus />
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
    
    <div class="mb-3 form-password-toggle">
        <label for="password" class="form-label">Password</label>
        <div class="input-group input-group-merge">
            <input
                type="password"
                id="password"
                class="form-control @error('password') is-invalid @enderror"
                name="password"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required
                aria-describedby="password" />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    
    <div class="mb-3 form-password-toggle">
        <label for="password-confirm" class="form-label">Confirm Password</label>
        <div class="input-group input-group-merge">
            <input
                type="password"
                id="password-confirm"
                class="form-control"
                name="password_confirmation"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                required />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
    </div>
    
    <button class="btn btn-primary d-grid w-100">{{ __('Reset Password') }}</button>
</form>
       

@endsection
