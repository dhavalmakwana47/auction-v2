{{-- @extends('layouts.app')
@section('body-attribute ')
    class="hold-transition register-page"
@endsection
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}








@extends('layouts.app')
@section('body-attribute ')
    class="hold-transition login-page"
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="login-box">
                <div class="login-logo">
                    <img src="{{ asset('homepage/assets/img/logo.png') }}" alt="" width="100px">
                    <span><b>Reset Password</b></span>
                </div>
                <!-- /.login-logo -->
                <div class="card">
                    <div class="card-body login-card-body">

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="input-group mb-3">
                                <input id="email" type="email" placeholder="Email Address"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>

                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope"></span>
                                    </div>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>



                            <div class="row mb-0">
                                <div class="">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Send Password Reset Link') }}
                                    </button>
                                </div>
                            </div>
                        </form>


                    </div>
                    <!-- /.login-card-body -->
                </div>
            </div>
        </div>
    </div>
@endsection
