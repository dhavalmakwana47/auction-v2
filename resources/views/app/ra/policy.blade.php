@extends('app.layout.app')
@section('page_title') Process Declaration — {{ $auction->corporate_debtor_name }} @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/ra/dashboard.css') }}">
<style>
    .policy-wrapper {
        max-width: 780px;
        margin: 0 auto;
    }
    .policy-header {
        background: linear-gradient(135deg, #0d6efd, #38b6ff);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        color: #fff;
    }
    .policy-header h4 { font-size: 18px; font-weight: 700; margin: 0 0 4px; }
    .policy-header small { opacity: 0.85; font-size: 13px; }
    .policy-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .policy-card-header {
        background: #eaf4fd;
        border-bottom: 2px solid #d0e8f8;
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 700;
        color: #0d6efd;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .policy-body {
        padding: 24px;
        font-size: 13px;
        color: #444;
        line-height: 1.8;
        max-height: 400px;
        overflow-y: auto;
        border-bottom: 1px solid #eee;
    }
    .policy-body::-webkit-scrollbar { width: 6px; }
    .policy-body::-webkit-scrollbar-thumb { background: #c0d8f0; border-radius: 3px; }
    .sign-section {
        padding: 20px 24px;
        background: #f8fbff;
    }
    .sign-checkbox-label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        cursor: pointer;
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    .sign-checkbox-label input[type="checkbox"] {
        margin-top: 2px;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        cursor: pointer;
    }
    .btn-sign {
        background: linear-gradient(135deg, #0d6efd, #38b6ff);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 10px 28px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-sign:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-sign:not(:disabled):hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(13,110,253,0.4); }
</style>
@endsection

@section('content-body')
<div class="policy-wrapper">

    <div class="policy-header">
        <h4><i class="fas fa-file-signature mr-2"></i> Process Declaration</h4>
        <small>{{ $auction->corporate_debtor_name }} — Please read and accept the declaration before proceeding.</small>
    </div>

    <div class="policy-card">
        <div class="policy-card-header">
            <i class="fas fa-scroll mr-1"></i> Declaration / Process Policy
        </div>
        <div class="policy-body">
            @if($auction->process_decleration)
                {!! nl2br(e($auction->process_decleration)) !!}
            @else
                <p class="text-muted text-center py-3">No declaration content has been configured for this auction.</p>
            @endif
        </div>
        <div class="sign-section">
            <form method="POST" action="{{ route('ra.auction.policy.sign', $auction) }}">
                @csrf
                <label class="sign-checkbox-label">
                    <input type="checkbox" id="agree-checkbox">
                    <span>I have read and understood the above Process Declaration. I agree to abide by the rules and conditions of the Challenge Mechanism for <strong>{{ $auction->corporate_debtor_name }}</strong>.</span>
                </label>
                <br>
                <button type="submit" class="btn-sign" id="btn-sign" disabled>
                    <i class="fas fa-signature"></i> Accept &amp; Proceed to Auction
                </button>
            </form>
        </div>
    </div>

</div>
@endsection

@section('footer-script')
<script>
$(function () {
    $('#agree-checkbox').on('change', function () {
        $('#btn-sign').prop('disabled', !this.checked);
    });
});
</script>
@endsection
