@extends('layouts.app')
@section('body-attribute ')
    class="hold-transition login-page"
@endsection

@section('content')
<style>
    .login-page { background: linear-gradient(135deg, #0d6efd 0%, #38b6ff 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-box { width: 380px; }
    .ra-login-card { border: none; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.15); overflow: hidden; }
    .ra-login-header { background: linear-gradient(135deg, #0d6efd 0%, #38b6ff 100%); padding: 32px 24px 24px; text-align: center; }
    .ra-login-header .icon-wrap { width: 64px; height: 64px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
    .ra-login-header h4 { color: #fff; font-weight: 700; font-size: 20px; margin: 0 0 4px; }
    .ra-login-header p { color: rgba(255,255,255,0.8); font-size: 13px; margin: 0; }
    .ra-login-body { padding: 28px 28px 20px; }
    .ra-login-body label { font-weight: 600; font-size: 13px; color: #555; margin-bottom: 6px; }
    .ra-input-wrap { position: relative; }
    .ra-input-wrap .ra-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #aaa; font-size: 14px; }
    .ra-input-wrap .form-control { padding-left: 36px; border-radius: 10px; border: 1.5px solid #e0e0e0; height: 44px; font-size: 14px; transition: all 0.2s; }
    .ra-input-wrap .form-control:focus { border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,0.15); }
    .ra-input-wrap .form-control.is-invalid { border-color: #e74c3c; }
    .btn-ra { background: linear-gradient(135deg, #0d6efd, #38b6ff); border: none; border-radius: 10px; color: #fff; font-weight: 600; font-size: 14px; height: 44px; width: 100%; transition: all 0.2s; }
    .btn-ra:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(13,110,253,0.4); color: #fff; }
    .ra-footer-link { text-align: center; font-size: 12px; color: #aaa; margin-top: 16px; }
    .ra-footer-link a { color: #0d6efd; font-weight: 600; text-decoration: none; }
    .ra-footer-link a:hover { text-decoration: underline; }
    .ra-step-badge { display: inline-block; background: rgba(255,255,255,0.25); color: #fff; font-size: 11px; font-weight: 600; border-radius: 20px; padding: 3px 12px; margin-bottom: 10px; letter-spacing: 1px; text-transform: uppercase; }
</style>

<div class="login-box">
    <div class="ra-login-card card">
        <div class="ra-login-header">
            <div class="icon-wrap">
                <i class="fas fa-user-tie fa-lg text-white"></i>
            </div>
            <span class="ra-step-badge">Step 1 of 2</span>
            <h4>RA Portal Login</h4>
            <p>Enter your email to receive a one-time password</p>
        </div>

        <div class="ra-login-body">
            @if(session('status'))
                <div class="alert alert-success py-2" style="font-size:13px; border-radius:8px;">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('ra.send-otp') }}">
                @csrf
                <div class="form-group mb-4">
                    <label>Email Address</label>
                    <div class="ra-input-wrap">
                        <i class="fas fa-envelope ra-icon"></i>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter your registered email" autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger mt-1" style="font-size:12px;"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-ra">
                    <i class="fas fa-paper-plane mr-2"></i> Send OTP
                </button>
            </form>

            <div class="ra-footer-link">
                <a href="{{ route('login') }}"><i class="fas fa-arrow-left mr-1"></i> Back to Admin Login</a>
            </div>
        </div>
    </div>
</div>
@endsection
