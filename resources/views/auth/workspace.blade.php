@extends('auth.auth_template')
@section('content')

<div class="container mt-5 d-flex justify-content-center align-items-center flex-column" style="height: 100vh;">
    <h2 class="mb-4 text-center">Sign In</h2>
    <p class="mb-3 text-center">You can always log in directly from your workspace, or use the form here.</p>
    
    @if(session('status'))
        <div class="alert alert-success w-100">
            {{ session('status') }}
        </div>
    @endif

    <form id="workspaceForm" action="{{ route('validate.workspace') }}" method="POST" class="w-100" style="max-width: 400px;">
        @csrf
        <div class="mb-4">
            <label for="workspace" class="form-label">Your workspace URL</label>
            <div class="input-group">
                <span class="input-group-text">https://</span>
                <input 
                    type="text" 
                    id="workspace" 
                    class="form-control @error('workspace') is-invalid @enderror" 
                    name="workspace" 
                    placeholder="company" 
                    value="{{ old('workspace') }}"
                    required />
                <span class="input-group-text">.smoothservice.net</span>
            </div>
            @error('workspace')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary w-100">Continue</button>
    </form>

    <div class="mt-3 text-center">
        <a href="{{ route('login') }}" class="d-block">Forgot your URL?</a>
        <a href="{{ route('register') }}">Don't have a workspace yet?</a>
        <a href="{{ route('home') }}" class="d-block mt-2">← Return</a>
    </div>
</div>

@endsection
