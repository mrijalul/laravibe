@extends('layouts.app')

@section('content')
<div class="img-holder">
    <div class="bg"></div>
    <div class="info-holder">
        <img src="{{ asset('assets/images/graphic9.svg') }}" alt="">
    </div>
</div>
<div class="form-holder">
    <div class="form-content">
        <div class="form-items">
            <div class="website-logo-inside less-margin">
                <a href="index.html">
                    <div class="logo">
                        <img class="logo-size" src="{{ asset('assets/img/logo.png') }}" alt="">
                    </div>
                </a>
            </div>
            <h3 class="font-md my-5">Register new account</h3>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <div class="mb-3">
                    <input class="form-control @error('name') is-invalid @enderror" type="text" name="name"
                        placeholder="Full Name" value="{{ old('name') }}" autocomplete="name" required>
                    @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <input class="form-control @error('email') is-invalid @enderror" type="email" name="email"
                        value="{{ old('email') }}" placeholder="E-mail Address" autocomplete="email" required>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <input class="form-control @error('password') is-invalid @enderror"" type=" password"
                        name="password" placeholder="Password" autocomplete="new-password" required>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="mb-3">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                        placeholder="Password Confirmation" required autocomplete="new-password">
                </div>
                <div class="mb-3">
                    <div class="form-button">
                        <button id="submit" type="submit" class="ibtn">{{ __('Register') }}</button>
                    </div>

                </div>
            </form>
            {{-- <div class="other-links social-with-title">
                <div class="text">Or register with</div>
                <a href="#"><i class="fab fa-facebook-f"></i>Facebook</a><a href="#"><i
                        class="fab fa-google"></i>Google</a><a href="#"><i class="fab fa-linkedin-in"></i>Linkedin</a>
            </div> --}}
            <div class="page-links">
                <a href="{{ route('login') }}">Login to account</a>
            </div>
        </div>
    </div>
</div>
@endsection