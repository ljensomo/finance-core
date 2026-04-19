@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <h2 class="fw-bold">{{ __('Welcome Back') }}</h2>
                <p class="text-muted">{{ __('Please enter your details to sign in') }}</p>
            </div>

            <div class="card shadow-lg border-1 rounded-4 p-3">
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-floating mb-3">
                            <input id="email" type="email" class="form-control rounded-3 @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required placeholder="name@example.com" autofocus>
                            <label for="email">{{ __('Email Address') }}</label>
                            @error('email')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-floating mb-3">
                            <input id="password" type="password" class="form-control rounded-3 @error('password') is-invalid @enderror" 
                                   name="password" required placeholder="Password">
                            <label for="password">{{ __('Password') }}</label>
                            @error('password')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">{{ __('Remember Me') }}</label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none small" href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                            @endif
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold">
                                {{ __('Sign In') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
/* Adjust floating labels to look cleaner */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: var(--bs-primary);
}

/* Ensure the card stands out slightly more */
.card {
    background-color: rgba(255, 255, 255, 0.9);
}
</style>
@endsection
