@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="login-box">
                <div class="login-logo">
                    <img src="{{ asset('homepage/assets/img/logo.png') }}" alt="" width="100px">
                    <span><b>Reset Password</b></span>
                </div>
                <div class="card">
                    <div class="card-body login-card-body">
                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <div class="input-group mb-3">
                                <input id="email" type="email" placeholder="Email Address"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $request->email) }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="input-group mb-3">
                                <input id="password" type="password" placeholder="New Password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="input-group mb-3">
                                <input id="password_confirmation" type="password" placeholder="Confirm Password"
                                    class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>

                            <div class="row mb-0">
                                <div class="">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
