@extends('app.layout.app')
@section('page_title') Challenge Mechanism Portal — {{ $auction->corporate_debtor_name }} @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/ra/dashboard.css') }}">
@endsection

@section('content-body')

<div class="ra-portal-header">
    <div>
        <h4><i class="fas fa-gavel mr-2"></i> Challenge Mechanism Portal</h4>
        <small>{{ $auction->corporate_debtor_name }}</small>
    </div>
    <a href="{{ route('ra.dashboard') }}" class="btn btn-light btn-sm font-weight-600" style="border-radius:20px; font-size:13px;">
        <i class="fas fa-arrow-left mr-1"></i> Back to Auctions
    </a>
</div>

{{-- Stat Cards --}}
<div class="row mb-4">
    <div class="col-md-6 col-12 mb-3 mb-md-0">
        <div class="ra-stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Current Base Value</div>
                <div class="stat-value">₹ {{ number_format($auction->base_price) }}</div>
                <div class="stat-note">Dynamic — updates on each bid</div>
            </div>
            <div class="stat-icon"><i class="fas fa-rupee-sign text-white fa-lg"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="ra-stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Minimum Next Bid</div>
                <div class="stat-value">₹ {{ number_format($auction->base_price + $auction->increment_amount) }}</div>
                <div class="stat-note">Base Value + Minimum Increment</div>
            </div>
            <div class="stat-icon"><i class="fas fa-arrow-up text-white fa-lg"></i></div>
        </div>
    </div>
</div>

{{-- NPV Distribution Table --}}
<div class="ra-table-card card">
    <div class="card-header">
        <i class="fas fa-table mr-1"></i> NPV Distribution by Category &amp; Period
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>Category</th>
                    @foreach($auction->npvpConfigurations as $npvp)
                        <th>{{ $npvp->period }} Days</th>
                    @endforeach
                    <th>NPV Total</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($auction->npvCategories as $category)
                <tr data-category-id="{{ $category->id }}">
                    <td class="font-weight-600">{{ $category->name }}</td>
                    @foreach($auction->npvpConfigurations as $npvp)
                    <td>
                        <input type="number" min="0" step="0.01"
                            class="form-control form-control-sm npv-amount-input"
                            data-category-id="{{ $category->id }}"
                            data-config-id="{{ $npvp->id }}"
                            data-period="{{ $npvp->period }}"
                            placeholder="0.00"
                            style="min-width:110px;">
                    </td>
                    @endforeach
                    <td class="font-weight-700 row-npv-total" style="white-space:nowrap;">0.00</td>
                    <td class="font-weight-700 row-total" style="white-space:nowrap;">0.00</td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ $auction->npvpConfigurations->count() + 3 }}" class="text-center text-muted">
                        No categories assigned to this auction.
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td class="font-weight-700">Total Amount</td>
                    @foreach($auction->npvpConfigurations as $npvp)
                        <td class="font-weight-700 col-total" data-period="{{ $npvp->period }}">0.00</td>
                    @endforeach
                    <td class="font-weight-700" id="grand-npv-total">0.00</td>
                    <td class="font-weight-700" id="grand-total">0.00</td>
                </tr>
                <tr class="total-row" style="background:#e0f7f5;">
                    <td class="font-weight-700">NPV Total</td>
                    @foreach($auction->npvpConfigurations as $npvp)
                        <td class="font-weight-700 col-npv-total" data-period="{{ $npvp->period }}" data-pct="{{ $npvp->percentage_value }}">0.00</td>
                    @endforeach
                    <td class="font-weight-700" id="grand-npv-total-footer">0.00</td>
                    <td>—</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Place Bid --}}
<div class="ra-bid-card">
    <div class="row align-items-end">
        <div class="col-md-8 col-12 mb-3 mb-md-0">
            <label>Enter Resolution Amount</label>
            <div class="d-flex align-items-center" style="gap:10px;">
                <input type="text" id="bid-amount" class="ra-bid-input" placeholder="e.g. ₹ 1,40,00,00,000" style="flex:1;">
                <button class="btn btn-place-bid" id="btn-place-bid" disabled>
                    <i class="fas fa-gavel mr-2"></i> Place Bid
                </button>
            </div>
            <div id="bid-error" style="display:none;" class="mt-1"><i class="fas fa-exclamation-circle mr-1"></i><span id="bid-error-msg"></span></div>
            <div id="distribution-error" style="display:none;color:#e74c3c;" class="mt-1"><i class="fas fa-exclamation-circle mr-1"></i><span id="distribution-error-msg"></span></div>
            <div class="ra-bid-notes mt-2">
                <div class="note"><i class="fas fa-info-circle"></i> Must be greater than Current Base Value (₹ {{ number_format($auction->base_price) }})</div>
                <div class="note"><i class="fas fa-info-circle"></i> Must be a multiple of ₹ {{ number_format($auction->increment_amount) }}</div>
                <div class="note" id="note-unlock" style="color:#11998e;"><i class="fas fa-lock"></i> Enter a valid resolution amount to unlock category inputs</div>
                <div class="note" id="note-distribute" style="color:#e67e22; display:none;"><i class="fas fa-info-circle"></i> Distribute the full bid amount across categories before placing bid. Remaining: <strong id="remaining-amount">0.00</strong></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-script')
<script>
$(function () {
    var baseValue    = {{ $auction->base_price }};
    var increment    = {{ $auction->increment_amount }};
    var bidAmount    = 0;
    var csrfToken    = '{{ csrf_token() }}';
    var bidUrl       = '{{ route('ra.auction.bid', $auction) }}';

    function formatINR(num) {
        return '₹ ' + Number(num).toLocaleString('en-IN');
    }

    // ── Live totals ──
    function recalculate() {
        var grandTotal    = 0;
        var grandNpvTotal = 0;

        $('.col-total').each(function () { $(this).text('0.00'); });
        $('.col-npv-total').each(function () { $(this).text('0.00'); });

        $('tbody tr[data-category-id]').each(function () {
            var rowTotal = 0, rowNpvTotal = 0;
            $(this).find('.npv-amount-input').each(function () {
                var val = parseFloat($(this).val()) || 0;
                var pct = parseFloat($('.col-npv-total[data-period="' + $(this).data('period') + '"]').data('pct')) || 0;
                rowTotal    += val;
                rowNpvTotal += val * pct;
            });
            $(this).find('.row-total').text(rowTotal.toFixed(2));
            $(this).find('.row-npv-total').text(rowNpvTotal.toFixed(2));
        });

        $('.npv-amount-input').each(function () {
            var period = $(this).data('period');
            var val    = parseFloat($(this).val()) || 0;
            var $ct    = $('.col-total[data-period="' + period + '"]');
            $ct.text((parseFloat($ct.text()) + val).toFixed(2));
            var $nt  = $('.col-npv-total[data-period="' + period + '"]');
            var pct  = parseFloat($nt.data('pct')) || 0;
            $nt.text((parseFloat($nt.text()) + val * pct).toFixed(2));
        });

        $('.col-total').each(function () { grandTotal    += parseFloat($(this).text()) || 0; });
        $('.col-npv-total').each(function () { grandNpvTotal += parseFloat($(this).text()) || 0; });

        $('#grand-total').text(grandTotal.toFixed(2));
        $('#grand-npv-total').text(grandNpvTotal.toFixed(2));
        $('#grand-npv-total-footer').text(grandNpvTotal.toFixed(2));

        // Remaining amount & enable/disable Place Bid
        if (bidAmount > 0) {
            var remaining = bidAmount - grandTotal;
            $('#remaining-amount').text(formatINR(remaining.toFixed(2)));
            var matched = Math.abs(remaining) < 0.01;
            $('#btn-place-bid').prop('disabled', !matched);
            if (matched) {
                $('#note-distribute').hide();
            } else {
                $('#note-distribute').show();
            }
        }
    }

    $(document).on('input', '.npv-amount-input', recalculate);
    recalculate();

    // ── Disable inputs initially ──
    $('.npv-amount-input').prop('disabled', true);
    $('#btn-place-bid').prop('disabled', true);

    // ── Unlock on valid resolution amount ──
    $('#bid-amount').on('input', function () {
        var raw   = $(this).val().replace(/[₹,\s]/g, '');
        var value = parseFloat(raw);
        var valid = !isNaN(value) && value > baseValue && (increment <= 0 || value % increment === 0);

        $('#bid-error').hide();
        $(this).removeClass('is-invalid');

        if (valid) {
            bidAmount = value;
            $('.npv-amount-input').prop('disabled', false);
            $('#note-unlock').hide();
            $('#note-distribute').show();
            $('#remaining-amount').text(formatINR(value.toFixed(2)));
            recalculate();
        } else {
            bidAmount = 0;
            $('.npv-amount-input').prop('disabled', true).val('');
            $('#btn-place-bid').prop('disabled', true);
            $('#note-unlock').show();
            $('#note-distribute').hide();
            recalculate();
        }
    });

    // ── Place Bid ──
    $('#btn-place-bid').on('click', function () {
        var distributions = [];
        $('.npv-amount-input').each(function () {
            var val = parseFloat($(this).val()) || 0;
            if (val > 0) {
                distributions.push({
                    npv_category_id : $(this).data('category-id'),
                    npvp_config_id  : $(this).data('config-id'),
                    amount          : val
                });
            }
        });

        Swal.fire({
            title: 'Confirm Bid',
            html: 'You are placing a bid of <strong>' + formatINR(bidAmount) + '</strong>.<br>This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#11998e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Place Bid!',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (!result.isConfirmed) return;

            $.ajax({
                url: bidUrl,
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken },
                contentType: 'application/json',
                data: JSON.stringify({ bid_amount: bidAmount, distributions: distributions }),
                success: function (res) {
                    toastr.success(res.message);
                    $('#btn-place-bid').prop('disabled', true);
                    $('#bid-amount').prop('disabled', true);
                    $('.npv-amount-input').prop('disabled', true);
                },
                error: function (xhr) {
                    var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                    $('#distribution-error-msg').text(msg);
                    $('#distribution-error').show();
                }
            });
        });
    });
});
</script>
@endsection
