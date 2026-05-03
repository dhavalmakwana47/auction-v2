@extends('layouts.app')
@section('body-attribute ')
    class="hold-transition login-page"
@endsection

@section('content')
<style>
    .login-page { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .login-box { width: 380px; }
    .ra-login-card { border: none; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.15); overflow: hidden; }
    .ra-login-header { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); padding: 32px 24px 24px; text-align: center; }
    .ra-login-header .icon-wrap { width: 64px; height: 64px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; }
    .ra-login-header h4 { color: #fff; font-weight: 700; font-size: 20px; margin: 0 0 4px; }
    .ra-login-header p { color: rgba(255,255,255,0.8); font-size: 13px; margin: 0; }
    .ra-login-body { padding: 28px 28px 20px; }
    .ra-login-body label { font-weight: 600; font-size: 13px; color: #555; margin-bottom: 6px; }
    .otp-input { border-radius: 10px; border: 1.5px solid #e0e0e0; height: 56px; font-size: 24px; font-weight: 700; letter-spacing: 12px; text-align: center; width: 100%; transition: all 0.2s; padding: 0 16px; }
    .otp-input:focus { border-color: #11998e; box-shadow: 0 0 0 3px rgba(17,153,142,0.15); outline: none; }
    .otp-input.is-invalid { border-color: #e74c3c; }
    .btn-ra { background: linear-gradient(135deg, #11998e, #38ef7d); border: none; border-radius: 10px; color: #fff; font-weight: 600; font-size: 14px; height: 44px; width: 100%; transition: all 0.2s; }
    .btn-ra:hover { opacity: 0.9; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(17,153,142,0.4); color: #fff; }
    .ra-footer-link { text-align: center; font-size: 12px; color: #aaa; margin-top: 16px; }
    .ra-footer-link a { color: #11998e; font-weight: 600; text-decoration: none; }
    .ra-footer-link a:hover { text-decoration: underline; }
    .ra-step-badge { display: inline-block; background: rgba(255,255,255,0.25); color: #fff; font-size: 11px; font-weight: 600; border-radius: 20px; padding: 3px 12px; margin-bottom: 10px; letter-spacing: 1px; text-transform: uppercase; }
    .otp-hint { background: #f0fff8; border: 1px solid #c3f0e0; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #11998e; margin-bottom: 20px; }
    .timer { font-size: 12px; color: #aaa; text-align: center; margin-top: 8px; }
    .timer span { font-weight: 700; color: #e74c3c; }
</style>

<div class="login-box">
    <div class="ra-login-card card">
        <div class="ra-login-header">
            <div class="icon-wrap">
                <i class="fas fa-shield-alt fa-lg text-white"></i>
            </div>
            <span class="ra-step-badge">Step 2 of 2</span>
            <h4>Verify OTP</h4>
            <p>Check your email for the 6-digit code</p>
        </div>

        <div class="ra-login-body">
            <div class="otp-hint">
                <i class="fas fa-info-circle mr-1"></i>
                OTP sent to <strong>{{ session('ra_otp_email') }}</strong>. Valid for <strong>10 minutes</strong>.
            </div>

            <form method="POST" action="{{ route('ra.verify-otp') }}">
                @csrf
                <div class="form-group mb-3">
                    <label>Enter OTP</label>
                    <input type="text" name="otp" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                        class="otp-input @error('otp') is-invalid @enderror"
                        placeholder="——————" autofocus autocomplete="one-time-code">
                    @error('otp')
                        <div class="text-danger mt-1" style="font-size:12px;"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</div>
                    @enderror
                </div>

                <div class="timer mb-3">OTP expires in <span id="countdown">10:00</span></div>

                <button type="submit" class="btn btn-ra">
                    <i class="fas fa-check-circle mr-2"></i> Verify & Login
                </button>
            </form>

            <div class="ra-footer-link mt-3">
                <a href="{{ route('ra.login') }}"><i class="fas fa-redo mr-1"></i> Resend OTP</a>
                &nbsp;·&nbsp;
                <a href="{{ route('login') }}"><i class="fas fa-arrow-left mr-1"></i> Admin Login</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-script')
<script>
    // Countdown timer
    var seconds = 600;
    var timer = setInterval(function () {
        seconds--;
        var m = Math.floor(seconds / 60);
        var s = seconds % 60;
        document.getElementById('countdown').textContent = m + ':' + (s < 10 ? '0' : '') + s;
        if (seconds <= 0) {
            clearInterval(timer);
            document.getElementById('countdown').textContent = 'Expired';
            document.querySelector('.btn-ra').disabled = true;
            document.querySelector('.btn-ra').textContent = 'OTP Expired';
        }
    }, 1000);

    // Allow only digits in OTP input
    document.querySelector('[name="otp"]').addEventListener('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection
