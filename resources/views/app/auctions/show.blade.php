@extends('app.layout.app')
@section('page_title') Challenge Mechanism Control Panel — {{ $auction->corporate_debtor_name }} @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/ra/dashboard.css') }}">
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<style>
    /* ── Control Panel Styles ── */
    .cp-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }
    .cp-header h4 {
        color: #fff; font-weight: 700; font-size: 20px;
        margin: 0 0 4px; letter-spacing: 0.5px;
    }
    .cp-header .subtitle { color: rgba(255,255,255,0.9); font-size: 14px; font-weight: 600; margin-bottom: 8px; }
    .cp-header .meta { color: rgba(255,255,255,0.75); font-size: 12px; display: flex; flex-wrap: wrap; gap: 16px; }
    .cp-header .meta span { display: flex; align-items: center; gap: 5px; }
    .badge-active {
        background: #fff; color: #11998e;
        border-radius: 20px; padding: 6px 16px;
        font-size: 12px; font-weight: 700;
        display: inline-flex; align-items: center; gap: 6px;
        white-space: nowrap; box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .badge-active .dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: #11998e; animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; } 50% { opacity: 0.3; }
    }

    /* Summary Cards */
    .summary-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 4px 20px rgba(17,153,142,0.10);
        padding: 20px 24px; height: 100%;
        border-left: 4px solid #11998e;
    }
    .summary-card .s-label {
        font-size: 11px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: #11998e; margin-bottom: 8px;
    }
    .summary-card .s-value {
        font-size: 22px; font-weight: 700; color: #1a1a2e; margin-bottom: 4px;
    }
    .summary-card .s-sub { font-size: 12px; color: #999; }
    .summary-card .s-icon {
        width: 44px; height: 44px; border-radius: 12px;
        background: linear-gradient(135deg, #11998e, #38ef7d);
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    /* Section Cards */
    .cp-card {
        background: #fff; border-radius: 14px;
        box-shadow: 0 4px 20px rgba(17,153,142,0.08);
        overflow: hidden; margin-bottom: 24px;
    }
    .cp-card-header {
        background: #f8fffe; border-bottom: 2px solid #e0f7f5;
        padding: 14px 20px; font-size: 13px; font-weight: 700;
        color: #11998e; text-transform: uppercase; letter-spacing: 1px;
        display: flex; align-items: center; gap: 8px;
    }
    .cp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .cp-table thead th {
        background: linear-gradient(135deg, #11998e, #38ef7d);
        color: #fff; font-size: 12px; font-weight: 600;
        padding: 11px 16px; border: none; white-space: nowrap;
    }
    .cp-table tbody td { padding: 11px 16px; border-bottom: 1px solid #f0f0f0; color: #444; }
    .cp-table tbody tr:last-child td { border-bottom: none; }
    .cp-table tbody tr:hover td { background: #f8fffe; }

    .badge-eligible {
        background: #eafaf1; color: #1e8449;
        border-radius: 20px; padding: 3px 12px;
        font-size: 11px; font-weight: 600;
    }
    .badge-invalid {
        background: #fdf2f2; color: #c0392b;
        border-radius: 20px; padding: 3px 12px;
        font-size: 11px; font-weight: 600;
    }
    .text-valid   { color: #1e8449; font-weight: 600; }
    .text-invalid { color: #c0392b; font-weight: 600; }

    .note-box {
        background: #fffbea; border-left: 4px solid #f39c12;
        border-radius: 8px; padding: 10px 16px;
        font-size: 12px; color: #7d6608; margin-bottom: 12px;
        display: flex; align-items: center; gap: 8px;
    }

    /* Footer Buttons */
    .cp-footer {
        background: #fff; border-radius: 14px;
        box-shadow: 0 4px 20px rgba(17,153,142,0.08);
        padding: 20px 24px;
        display: flex; flex-wrap: wrap; gap: 12px; align-items: center;
    }
    .btn-cp {
        border: none; border-radius: 12px; font-size: 13px;
        font-weight: 600; padding: 10px 20px 10px 12px;
        display: inline-flex; align-items: center; gap: 10px;
        cursor: pointer; transition: all 0.22s; color: #fff;
        box-shadow: 0 3px 10px rgba(0,0,0,0.12); letter-spacing: 0.3px;
        text-decoration: none;
    }
    .btn-cp:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.18); color: #fff; text-decoration: none; }
    .btn-cp:active { transform: translateY(0); }
    .btn-cp .btn-icon {
        width: 28px; height: 28px; border-radius: 8px;
        background: rgba(255,255,255,0.22);
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 12px; flex-shrink: 0;
    }
    .btn-cp-start    { background: linear-gradient(135deg, #11998e, #38ef7d); }
    .btn-cp-start:hover { box-shadow: 0 6px 20px rgba(17,153,142,0.45); }
    .btn-cp-edit     { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
    .btn-cp-edit:hover { box-shadow: 0 6px 20px rgba(79,70,229,0.40); }
    .btn-cp-notify   { background: linear-gradient(135deg, #0ea5e9, #38bdf8); }
    .btn-cp-notify:hover { box-shadow: 0 6px 20px rgba(14,165,233,0.40); }
    .btn-cp-end      { background: linear-gradient(135deg, #dc2626, #f87171); }
    .btn-cp-end:hover { box-shadow: 0 6px 20px rgba(220,38,38,0.40); }
    .btn-cp-download { background: linear-gradient(135deg, #059669, #34d399); }
    .btn-cp-download:hover { box-shadow: 0 6px 20px rgba(5,150,105,0.40); }

    @media (max-width: 576px) {
        .cp-header { padding: 16px; }
        .cp-header h4 { font-size: 16px; }
        .summary-card .s-value { font-size: 18px; }
        .cp-footer { justify-content: center; }
    }
</style>
@endsection

@section('content-body')

{{-- Breadcrumb --}}
<div class="breadcrumb-nav mb-3">
    <a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a>
    <span class="mx-2">/</span>
    <span>Challenge Mechanism Control Panel</span>
</div>

{{-- ── Header ── --}}
<div class="cp-header">
    <div>
        <h4><i class="fas fa-gavel mr-2"></i> Challenge Mechanism Control Panel</h4>
        <div class="subtitle">Corporate Debtor: {{ $auction->corporate_debtor_name }} (In CIRP)</div>
        <div class="meta">
            <span><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($auction->meeting_date)->format('d M Y') }}</span>
            <span><i class="fas fa-clock"></i> <span id="live-time">--:--:--</span></span>
            <span><i class="fas fa-desktop"></i> Electronic Platform</span>
        </div>
    </div>
    <div class="d-flex flex-column align-items-end" style="gap:10px;">
        <span class="badge-active">
            <span class="dot"></span> Challenge Round ACTIVE
        </span>
        <a href="{{ route('dashboard') }}" class="btn btn-light btn-sm" style="border-radius:20px; font-size:12px;">
            <i class="fas fa-arrow-left mr-1"></i> Back
        </a>
    </div>
</div>

{{-- ── Summary Cards Row 1: Configured Values ── --}}
@php
    $highestBid = $auction->bids->where('status', 'confirmed')->sortByDesc('bid_amount')->first();
@endphp
<div class="row mb-3">
    <div class="col-md-3 col-6 mb-3">
        <div class="summary-card d-flex align-items-center justify-content-between">
            <div>
                <div class="s-label">Base Value</div>
                <div class="s-value">&#8377; {{ number_format($auction->base_price) }}</div>
                <div class="s-sub">(Configured)</div>
            </div>
            <div class="s-icon"><i class="fas fa-rupee-sign text-white fa-lg"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="summary-card d-flex align-items-center justify-content-between">
            <div>
                <div class="s-label">Initial NPV Value</div>
                <div class="s-value">&#8377; {{ number_format($auction->initial_npv_value, 2) }}</div>
                <div class="s-sub">(Configured)</div>
            </div>
            <div class="s-icon"><i class="fas fa-chart-line text-white fa-lg"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="summary-card d-flex align-items-center justify-content-between">
            <div>
                <div class="s-label">Minimum Increment</div>
                <div class="s-value">&#8377; {{ number_format($auction->increment_amount) }}</div>
                <div class="s-sub">(Configured by RP)</div>
            </div>
            <div class="s-icon"><i class="fas fa-plus text-white fa-lg"></i></div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="summary-card d-flex align-items-center justify-content-between">
            <div>
                <div class="s-label">Total Bids</div>
                <div class="s-value" id="cp-total-bids">{{ $auction->bids->count() }}</div>
                <div class="s-sub">(Valid: <span id="cp-valid-bids">{{ $auction->bids->where('status', 'confirmed')->count() }}</span>)</div>
            </div>
            <div class="s-icon"><i class="fas fa-gavel text-white fa-lg"></i></div>
        </div>
    </div>
</div>

{{-- ── Summary Cards Row 2: Current Live Values ── --}}
<div class="row mb-4">
    <div class="col-md-6 col-12 mb-3 mb-md-0">
        <div class="summary-card d-flex align-items-center justify-content-between" style="border-left-color:#2980b9;">
            <div>
                <div class="s-label" style="color:#2980b9;">Current Base Value</div>
                <div class="s-value" id="cp-current-base">&#8377; {{ $highestBid ? number_format($highestBid->bid_amount) : number_format($auction->base_price) }}</div>
                <div class="s-sub" id="cp-current-base-sub">{{ $highestBid ? '(From highest valid bid)' : '(No bids yet — showing configured)' }}</div>
            </div>
            <div class="s-icon" style="background:linear-gradient(135deg,#2980b9,#6dd5fa);"><i class="fas fa-arrow-trend-up text-white fa-lg"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        <div class="summary-card d-flex align-items-center justify-content-between" style="border-left-color:#8e44ad;">
            <div>
                <div class="s-label" style="color:#8e44ad;">Current NPV Value</div>
                <div class="s-value" id="cp-current-npv">&#8377; {{ $highestBid ? number_format($highestBid->total_npv, 2) : number_format($auction->initial_npv_value, 2) }}</div>
                <div class="s-sub" id="cp-current-npv-sub">{{ $highestBid ? '(From highest valid bid)' : '(No bids yet — showing configured)' }}</div>
            </div>
            <div class="s-icon" style="background:linear-gradient(135deg,#8e44ad,#c39bd3);"><i class="fas fa-chart-bar text-white fa-lg"></i></div>
        </div>
    </div>
</div>

{{-- ── Live Bid Timeline ── --}}
<div class="row">

    <div class="col-12">
        <div class="cp-card">
            <div class="cp-card-header">
                <i class="fas fa-stream"></i> Live Bid Timeline
            </div>
            <div class="px-3 pt-3">
                <div class="note-box">
                    <i class="fas fa-info-circle"></i>
                    Recommended Incremental Bid Value: INR {{ number_format($auction->increment_amount) }}/-
                </div>
            </div>
            <div class="table-responsive">
                <table class="cp-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date / Time</th>
                            <th>RA Name</th>
                            <th>Resolution Amount</th>
                            <th>NPV of RA Amount</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody id="cp-bid-tbody">
                        @forelse($auction->bids->sortByDesc('created_at') as $i => $bid)
                        @php $isValid = $bid->status === 'confirmed'; @endphp
                        <tr>
                            <td class="bid-row-num">{{ $i + 1 }}</td>
                            <td style="white-space:nowrap;">{{ $bid->created_at->format('d M Y, h:i A') }}</td>
                            <td class="font-weight-600">{{ $bid->user->name ?? '—' }}</td>
                            <td>₹ {{ number_format($bid->bid_amount) }}</td>
                            <td>₹ {{ number_format($bid->total_npv, 2) }}</td>
                            <td class="{{ $isValid ? 'text-valid' : 'text-invalid' }}">
                                <i class="fas fa-{{ $isValid ? 'check-circle' : 'times-circle' }} mr-1"></i>
                                {{ $isValid ? 'Valid Bid' : 'Invalid Bid' }}
                            </td>
                        </tr>
                        @empty
                        <tr id="cp-no-bids-row">
                            <td colspan="6" class="text-center text-muted py-3">
                                <i class="fas fa-gavel fa-2x d-block mb-2 text-muted"></i> No bids placed yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- ── Footer Action Buttons ── --}}
<div class="cp-footer">
    @if($auction->status === 'pending')
    <button class="btn-cp btn-cp-start" id="btn-start-challenge">
        <span class="btn-icon"><i class="fas fa-flag-checkered"></i></span> Start Challenge Round
    </button>
    <button class="btn-cp btn-cp-edit" id="btn-edit-values">
        <span class="btn-icon"><i class="fas fa-sliders-h"></i></span> Edit Values
    </button>
    @endif
    @if($auction->status === 'in_progress')
    <button class="btn-cp btn-cp-end" id="btn-end-challenge">
        <span class="btn-icon"><i class="fas fa-ban"></i></span> End Challenge Process
    </button>
    @endif
    @if($auction->status === 'completed')
    <button class="btn-cp btn-cp-notify">
        <span class="btn-icon"><i class="fas fa-paper-plane"></i></span> Send Notifications
    </button>
    <a href="{{ route('auctions.download-report', $auction) }}" class="btn-cp btn-cp-download">
        <span class="btn-icon"><i class="fas fa-file-download"></i></span> Download Report
    </a>
    @endif
</div>

{{-- ── Start Challenge Modal ── --}}
<div class="modal fade" id="startChallengeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#11998e,#38ef7d); border:none; padding:20px 24px;">
                <h5 class="modal-title text-white font-weight-bold mb-0">
                    <i class="fas fa-flag-checkered mr-2"></i> Start Challenge Round
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p class="text-muted mb-4" style="font-size:13px;">
                    <i class="fas fa-info-circle mr-1 text-warning"></i>
                    Review and confirm the values below before starting the challenge round. Status will change to <strong>In Progress</strong>.
                </p>
                <div class="form-group">
                    <label style="font-size:13px; font-weight:600; color:#444;">Base Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-radius:10px 0 0 10px; background:#f0fff8; border-color:#c3f0e0; color:#11998e; font-weight:700;">₹</span>
                        </div>
                        <input type="number" id="sc_base_price" class="form-control" value="{{ $auction->base_price }}" min="0.01" step="0.01"
                            style="border-radius:0 10px 10px 0; border-color:#c3f0e0;">
                    </div>
                    <div class="text-danger mt-1" id="sc_base_price_err" style="font-size:12px; display:none;"></div>
                </div>
                <div class="form-group mb-0">
                    <label style="font-size:13px; font-weight:600; color:#444;">Initial NPV Value <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-radius:10px 0 0 10px; background:#f0fff8; border-color:#c3f0e0; color:#11998e; font-weight:700;">₹</span>
                        </div>
                        <input type="number" id="sc_initial_npv" class="form-control" value="{{ $auction->initial_npv_value }}" min="0.01" step="0.01"
                            style="border-radius:0 10px 10px 0; border-color:#c3f0e0;">
                    </div>
                    <div class="text-danger mt-1" id="sc_npv_err" style="font-size:12px; display:none;"></div>
                </div>
            </div>
            <div class="modal-footer" style="border:none; padding:16px 24px; background:#f8fffe;">
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:10px;">Cancel</button>
                <button type="button" class="btn-cp btn-cp-start" id="btn-sc-submit" style="padding:9px 22px 9px 14px;">
                    <span class="btn-icon"><i class="fas fa-flag-checkered"></i></span>
                    <span id="btn-sc-text">Start Challenge</span>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ── Edit Values Modal ── --}}
<div class="modal fade" id="editValuesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#4f46e5,#7c3aed); border:none; padding:20px 24px;">
                <h5 class="modal-title text-white font-weight-bold mb-0">
                    <i class="fas fa-sliders-h mr-2"></i> Edit Values
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p class="text-muted mb-4" style="font-size:13px;">
                    <i class="fas fa-info-circle mr-1 text-warning"></i>
                    Update the Base Price and Initial NPV Value before starting the challenge round.
                </p>
                <div class="form-group">
                    <label style="font-size:13px; font-weight:600; color:#444;">Base Price <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-radius:10px 0 0 10px; background:#f3f0ff; border-color:#c4b5fd; color:#7c3aed; font-weight:700;">&#8377;</span>
                        </div>
                        <input type="number" id="ev_base_price" class="form-control" value="{{ $auction->base_price }}" min="0.01" step="0.01"
                            style="border-radius:0 10px 10px 0; border-color:#c4b5fd;">
                    </div>
                    <div class="text-danger mt-1" id="ev_base_price_err" style="font-size:12px; display:none;"></div>
                </div>
                <div class="form-group mb-0">
                    <label style="font-size:13px; font-weight:600; color:#444;">Initial NPV Value <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" style="border-radius:10px 0 0 10px; background:#f3f0ff; border-color:#c4b5fd; color:#7c3aed; font-weight:700;">&#8377;</span>
                        </div>
                        <input type="number" id="ev_initial_npv" class="form-control" value="{{ $auction->initial_npv_value }}" min="0.01" step="0.01"
                            style="border-radius:0 10px 10px 0; border-color:#c4b5fd;">
                    </div>
                    <div class="text-danger mt-1" id="ev_npv_err" style="font-size:12px; display:none;"></div>
                </div>
            </div>
            <div class="modal-footer" style="border:none; padding:16px 24px; background:#f8f7ff;">
                <button type="button" class="btn btn-light" data-dismiss="modal" style="border-radius:10px;">Cancel</button>
                <button type="button" class="btn-cp btn-cp-edit" id="btn-ev-submit" style="padding:9px 22px 9px 14px;">
                    <span class="btn-icon"><i class="fas fa-save"></i></span>
                    <span id="btn-ev-text">Save Changes</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-script')
<script>
$(function () {
    function updateTime() {
        var now = new Date();
        $('#live-time').text(now.toLocaleTimeString('en-IN'));
    }
    updateTime();
    setInterval(updateTime, 1000);

    // ── Pusher: live bid updates ──
    @if($auction->status === 'in_progress')
    var pusher  = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
        encrypted: true
    });
    pusher.subscribe('auction.{{ $auction->id }}').bind('bid.placed', function (data) {
        var highestBid = parseFloat(data.highest_bid);
        var totalNpv   = parseFloat(data.total_npv);
        // Summary cards
        $('#cp-total-bids').text(data.total_bids);
        $('#cp-valid-bids').text(data.valid_bids);
        $('#cp-current-base').html('&#8377; ' + highestBid.toLocaleString('en-IN'));
        $('#cp-current-base-sub').text('(From highest valid bid)');
        $('#cp-current-npv').html('&#8377; ' + totalNpv.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#cp-current-npv-sub').text('(From highest valid bid)');

        // Prepend new row to timeline table
        $('#cp-no-bids-row').remove();
        $('#cp-bid-tbody tr').each(function () {
            var $num = $(this).find('.bid-row-num');
            $num.text(parseInt($num.text()) + 1);
        });
        var newRow = '<tr style="background:#f0fff8;">' +
            '<td class="bid-row-num">1</td>' +
            '<td style="white-space:nowrap;">' + data.placed_at + '</td>' +
            '<td class="font-weight-600">' + data.ra_name + '</td>' +
            '<td>&#8377; ' + parseFloat(data.bid_amount).toLocaleString('en-IN') + '</td>' +
            '<td>&#8377; ' + totalNpv.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
            '<td class="text-valid"><i class="fas fa-check-circle mr-1"></i> Valid Bid</td>' +
            '</tr>';
        $('#cp-bid-tbody').prepend(newRow);
        setTimeout(function () { $('#cp-bid-tbody tr:first').css('background', ''); }, 2000);

        toastr.info('New bid received. Dashboard updated.', 'Live Update', { timeOut: 4000, progressBar: true });
    });
    @endif

    $('#btn-start-challenge').on('click', function () {
        $('#startChallengeModal').modal('show');
    });

    $('#btn-edit-values').on('click', function () {
        $('#editValuesModal').modal('show');
    });

    $('#btn-end-challenge').on('click', function () {
        Swal.fire({
            title: 'End Challenge Process?',
            text: 'This will mark the auction as Completed. This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, End It!',
            cancelButtonText: 'Cancel'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '{{ route('auctions.end-challenge', $auction) }}',
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                success: function (res) {
                    toastr.success(res.message);
                    setTimeout(function () { location.reload(); }, 1200);
                },
                error: function () {
                    toastr.error('Something went wrong. Please try again.');
                }
            });
        });
    });

    $('#btn-sc-submit').on('click', function () {
        var basePrice = parseFloat($('#sc_base_price').val());
        var npvValue  = parseFloat($('#sc_initial_npv').val());
        var valid = true;

        $('#sc_base_price_err').hide().text('');
        $('#sc_npv_err').hide().text('');

        if (isNaN(basePrice) || basePrice <= 0) {
            $('#sc_base_price_err').text('Base price is required and must be greater than 0.').show();
            valid = false;
        }
        if (isNaN(npvValue) || npvValue <= 0) {
            $('#sc_npv_err').text('Initial NPV value is required and must be greater than 0.').show();
            valid = false;
        }
        if (!valid) return;

        var $btn = $(this);
        $btn.prop('disabled', true);
        $('#btn-sc-text').text('Starting...');

        $.ajax({
            url: '{{ route('auctions.start-challenge', $auction) }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { base_price: basePrice, initial_npv_value: npvValue },
            success: function (res) {
                $('#startChallengeModal').modal('hide');
                toastr.success(res.message);
                setTimeout(function () { location.reload(); }, 1200);
            },
            error: function (xhr) {
                var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
                if (errors.base_price)        $('#sc_base_price_err').text(errors.base_price[0]).show();
                if (errors.initial_npv_value) $('#sc_npv_err').text(errors.initial_npv_value[0]).show();
                $btn.prop('disabled', false);
                $('#btn-sc-text').text('Start Challenge');
            }
        });
    });

    $('#btn-ev-submit').on('click', function () {
        var basePrice = parseFloat($('#ev_base_price').val());
        var npvValue  = parseFloat($('#ev_initial_npv').val());
        var valid = true;

        $('#ev_base_price_err').hide().text('');
        $('#ev_npv_err').hide().text('');

        if (isNaN(basePrice) || basePrice <= 0) {
            $('#ev_base_price_err').text('Base price is required and must be greater than 0.').show();
            valid = false;
        }
        if (isNaN(npvValue) || npvValue <= 0) {
            $('#ev_npv_err').text('Initial NPV value is required and must be greater than 0.').show();
            valid = false;
        }
        if (!valid) return;

        var $btn = $(this);
        $btn.prop('disabled', true);
        $('#btn-ev-text').text('Saving...');

        $.ajax({
            url: '{{ route('auctions.edit-values', $auction) }}',
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            data: { base_price: basePrice, initial_npv_value: npvValue },
            success: function (res) {
                $('#editValuesModal').modal('hide');
                toastr.success(res.message);
                setTimeout(function () { location.reload(); }, 1200);
            },
            error: function (xhr) {
                var errors = xhr.responseJSON && xhr.responseJSON.errors ? xhr.responseJSON.errors : {};
                if (errors.base_price)        $('#ev_base_price_err').text(errors.base_price[0]).show();
                if (errors.initial_npv_value) $('#ev_npv_err').text(errors.initial_npv_value[0]).show();
                $btn.prop('disabled', false);
                $('#btn-ev-text').text('Save Changes');
            }
        });
    });
});
</script>
@endsection
