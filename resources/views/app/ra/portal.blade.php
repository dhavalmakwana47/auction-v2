@extends('app.layout.app')
@section('page_title') Challenge Mechanism Portal â€” {{ $auction->corporate_debtor_name }} @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/ra/dashboard.css') }}">
<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; }
</style>
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
@endsection

@section('content-body')

<div class="ra-portal-header">
    <div>
        <h4><i class="fas fa-gavel mr-2"></i> Challenge Mechanism</h4>
        <small>{{ $auction->corporate_debtor_name }}</small>
        <div class="meta">
            <small><i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($auction->meeting_date)->format('d M Y') }}</small> &nbsp;
            <small><i class="fas fa-desktop"></i> Electronic Platform</small>
        </div>
    </div>
    <div class="d-flex align-items-center" style="gap:10px;">
        <button class="btn btn-sm font-weight-600" id="btn-top-bids"
            style="border-radius:20px; font-size:13px; background:linear-gradient(135deg,#0d6efd,#38b6ff); color:#fff; border:none; padding:6px 16px;">
            <i class="fas fa-trophy mr-1"></i> Top Bids
        </button>
        <button class="btn btn-sm font-weight-600" id="btn-my-bids"
            style="border-radius:20px; font-size:13px; background:linear-gradient(135deg,#0d6efd,#38b6ff); color:#fff; border:none; padding:6px 16px;">
            <i class="fas fa-list-alt mr-1"></i> My Bids
        </button>
        <a href="{{ route('ra.dashboard') }}" class="btn btn-light btn-sm font-weight-600" style="border-radius:20px; font-size:13px;">
            <i class="fas fa-arrow-left mr-1"></i> Back to Auctions
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row mb-3">
    <div class="col-md-6 col-12 mb-3 mb-md-0">
        <div class="ra-stat-card d-flex align-items-center justify-content-between">
            <div>
                <div class="stat-label">Resolution Plan Amount (Current Base Value)</div>
                <div class="stat-value" id="portal-base-value">&#8377; {{ number_format($highestBid) }}</div>
            </div>
            <div class="stat-icon"><i class="fas fa-rupee-sign text-white fa-lg"></i></div>
        </div>
    </div>
    <div class="col-md-6 col-12 mb-3">
        <div class="ra-stat-card d-flex align-items-center justify-content-between" style="border-left:4px solid #8e44ad;">
            <div>
                <div class="stat-label" style="color:#8e44ad;">Current NPV Value</div>
                <div class="stat-value" id="portal-npv-value">&#8377; {{ number_format($currentNpv, 2) }}</div>
            </div>
            <div class="stat-icon" style="background:linear-gradient(135deg,#8e44ad,#c39bd3);"><i class="fas fa-chart-bar text-white fa-lg"></i></div>
        </div>
    </div>
</div>

{{-- Resolution Amount --}}
<div class="ra-bid-card mb-4">
    <label style="font-size:13px; font-weight:600; color:#444; margin-bottom:8px; display:block;">Enter Resolution Amount</label>
    <div class="d-flex align-items-center" style="gap:10px;">
        <input type="text" id="bid-amount" class="ra-bid-input" placeholder="e.g. &#8377; 1,40,00,00,000" style="flex:1;">
        <button class="btn btn-place-bid" id="btn-verify-bid" style="white-space:nowrap;">
            <i class="fas fa-check-circle mr-2"></i> Submit
        </button>
        <button class="btn btn-secondary" id="btn-reset-bid" style="border-radius:10px; display:none; white-space:nowrap;">
            <i class="fas fa-redo mr-2"></i> Reset
        </button>
    </div>
    <div id="bid-error" style="display:none; font-size:12px; color:#e74c3c;" class="mt-1">
        <i class="fas fa-exclamation-circle mr-1"></i><span id="bid-error-msg"></span>
    </div>
    <div id="bid-valid-msg" style="display:none; font-size:12px; color:#0d6efd;" class="mt-1">
        <i class="fas fa-check-circle mr-1"></i> Valid bid amount. Now distribute across categories below.
    </div>
    <div class="ra-bid-notes mt-2">
        <div class="note" id="note-base-value">
            @if($auction->increment_amount_type === 'mandatory')
                <i class="fas fa-info-circle"></i> Must be greater than Current Base Value (&#8377; {{ number_format($highestBid) }})
            @else
                <i class="fas fa-info-circle"></i> Any amount is accepted (Increment is Recommended, not Mandatory)
            @endif
        </div>
        @if($auction->increment_amount_type === 'mandatory')
            @if($auction->increment_type === 'fixed')
            <div class="note" id="note-min-bid"><i class="fas fa-info-circle"></i> Must be at least &#8377; {{ number_format($highestBid + $auction->increment_amount) }} (Base + Increment)</div>
            @else
            <div class="note" id="note-min-bid"><i class="fas fa-info-circle"></i> Must be a multiple of &#8377; {{ number_format($auction->increment_amount) }} (e.g. {{ number_format($highestBid + $auction->increment_amount) }}, {{ number_format($highestBid + $auction->increment_amount * 2) }}, ...)</div>
            @endif
        @else
        <div class="note" id="note-min-bid" style="color:#0d6efd;"><i class="fas fa-info-circle"></i> Recommended increment: &#8377; {{ number_format($auction->increment_amount) }} (not enforced)</div>
        @endif
        <div class="note" id="note-distribute" style="color:#0d6efd; display:none;"><i class="fas fa-info-circle"></i> Distribute the full bid amount across categories. Remaining: <strong id="remaining-amount">0.00</strong></div>
    </div>
</div>

{{-- NPV Distribution Table --}}
<div class="ra-table-card card" id="npv-table-section" style="display:none;">
    <div class="card-header">
        <i class="fas fa-table mr-1"></i> Details of Proposed Resolution Amount
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>Category</th>
                    @foreach($auction->npvpConfigurations as $npvp)
                        <th>{{ $npvp->period }} Days</th>
                    @endforeach
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
                <tr class="total-row" style="background:#edf5ff;">
                    <td class="font-weight-700">Total Amount</td>
                    @foreach($auction->npvpConfigurations as $npvp)
                        <td class="font-weight-700 col-total" data-period="{{ $npvp->period }}">0.00</td>
                    @endforeach
                    <td class="font-weight-700" id="grand-total">0.00</td>
                </tr>
                <tr class="total-row" >
                    <td class="font-weight-700">NPV Total</td>
                    @foreach($auction->npvpConfigurations as $npvp)
                        <td class="font-weight-700 col-npv-total" data-period="{{ $npvp->period }}" data-pct="{{ $npvp->percentage_value }}">0.00</td>
                    @endforeach
                    <td>&mdash;</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Place Bid --}}
<div class="ra-bid-card mt-3 d-flex align-items-center justify-content-between flex-wrap" style="gap:10px;">
    <div id="distribution-error" style="display:none; font-size:12px; color:#e74c3c;">
        <i class="fas fa-exclamation-circle mr-1"></i><span id="distribution-error-msg"></span>
    </div>
    <div></div>
    <button class="btn btn-place-bid" id="btn-place-bid" disabled>
        <i class="fas fa-gavel mr-2"></i> Place Bid
    </button>
</div>

{{-- Bid Submission Loader --}}
<div id="bid-loader-overlay">
    <div class="bid-loader-box">
        <div class="bid-loader-spinner"></div>
        <div class="bid-loader-text">Submitting Bid...</div>
        <div class="bid-loader-sub">Please do not close this window</div>
    </div>
</div>

{{-- Top Bids Modal --}}
<div class="modal fade" id="topBidsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd,#38b6ff); border:none; padding:16px 24px;">
                <div>
                    <h5 class="modal-title text-white font-weight-bold mb-0" style="font-size:15px;">
                        <i class="fas fa-trophy mr-2"></i> Top 10 Bids
                    </h5>
                    <small class="text-white" style="opacity:0.85; font-size:11px;">Highest confirmed bids in this auction</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1;"><span>&times;</span></button>
            </div>

            {{-- Summary strip --}}
            <div class="d-flex" style="background:#edf5ff; border-bottom:1px solid #dbeafe; padding:12px 20px; gap:24px; flex-wrap:wrap;">
                <div style="font-size:12px; color:#555;">
                    <i class="fas fa-crown mr-1" style="color:#0d6efd;"></i>
                    Highest Bid: <strong id="top-bids-highest">&mdash;</strong>
                </div>
                <div style="font-size:12px; color:#555;">
                    <i class="fas fa-chart-line mr-1" style="color:#0d6efd;"></i>
                    Highest NPV: <strong id="top-bids-npv">&mdash;</strong>
                </div>
            </div>

            <div class="modal-body" style="padding:16px 20px;">
                <table id="top-bids-table" class="table table-hover mb-0" style="width:100%; font-size:13px;">
                    <thead>
                        <tr style="background:#edf5ff;">
                            <th style="width:40px; color:#0d6efd;">#</th>
                            <th style="color:#0d6efd;">Bid Amount</th>
                            <th style="color:#0d6efd;">NPV Amount</th>
                            <th style="color:#0d6efd;">Date / Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- My Bids Modal --}}
<div class="modal fade" id="myBidsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border-radius:16px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:linear-gradient(135deg,#0d6efd,#38b6ff); border:none; padding:16px 24px;">
                <div>
                    <h5 class="modal-title text-white font-weight-bold mb-0" style="font-size:15px;">
                        <i class="fas fa-list-alt mr-2"></i> My Bids
                    </h5>
                    <small class="text-white" style="opacity:0.85; font-size:11px;">Your bidding history for this auction</small>
                </div>
                <button type="button" class="close text-white" data-dismiss="modal" style="opacity:1;"><span>&times;</span></button>
            </div>

            {{-- Summary strip --}}
            <div class="d-flex" style="background:#f0f8ff; border-bottom:1px solid #d0e8f8; padding:12px 20px; gap:24px; flex-wrap:wrap;">
                <div style="font-size:12px; color:#555;">
                    <i class="fas fa-gavel mr-1" style="color:#0d6efd;"></i>
                    Total Bids: <strong id="my-bids-total">â€”</strong>
                </div>
                <div style="font-size:12px; color:#555;">
                    <i class="fas fa-check-circle mr-1" style="color:#27ae60;"></i>
                    Valid: <strong id="my-bids-valid">â€”</strong>
                </div>
                <div style="font-size:12px; color:#555;">
                    <i class="fas fa-arrow-up mr-1" style="color:#0d6efd;"></i>
                    Highest: <strong id="my-bids-highest">â€”</strong>
                </div>
            </div>

            <div class="modal-body" style="padding:16px 20px;">
                <table id="my-bids-table" class="table table-hover mb-0" style="width:100%; font-size:13px;">
                    <thead>
                        <tr style="background:#eaf4fd;">
                            <th style="width:50px; color:#0d6efd;">Bid #</th>
                            <th style="color:#0d6efd;">Bid Amount</th>
                            <th style="color:#0d6efd;">NPV Amount</th>
                            <th style="color:#0d6efd;">Status</th>
                            <th style="color:#0d6efd;">Date / Time</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('footer-script')
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script>
$(function () {
    var baseValue          = {{ (float) $highestBid }};
    var increment          = {{ (float) str_replace(',', '', $auction->increment_amount) }};
    var incrementType      = '{{ $auction->increment_type }}';
    var incrementAmountType = '{{ strtolower($auction->increment_amount_type) }}';
    var bidAmount = 0;
    var csrfToken = '{{ csrf_token() }}';
    var bidUrl    = '{{ route('ra.auction.bid', $auction) }}';

    function formatINR(num) {
        return 'â‚¹ ' + Number(num).toLocaleString('en-IN');
    }

    var topBidsUrl = '{{ route('ra.auction.top-bids', $auction) }}';
    var myBidsUrl  = '{{ route('ra.auction.my-bids', $auction) }}';
    var topBidsDT  = null;
    var myBidsDT   = null;

    $('#btn-top-bids').on('click', function () {
        $('#topBidsModal').modal('show');
    });

    $('#btn-my-bids').on('click', function () {
        $('#myBidsModal').modal('show');
    });

    $('#topBidsModal').on('shown.bs.modal', function () {
        if (topBidsDT) {
            topBidsDT.ajax.reload(null, false);
            return;
        }
        topBidsDT = $('#top-bids-table').DataTable({
            processing: true,
            serverSide: false,
            ajax: { url: topBidsUrl, type: 'GET' },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, width: '40px',
                  render: function (data) {
                      if (data == 1) return '<span style="background:#0d6efd; color:#fff; border-radius:50%; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:11px;"><i class="fas fa-crown"></i></span>';
                      if (data == 2) return '<span style="background:#95a5a6; color:#fff; border-radius:50%; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:11px;">' + data + '</span>';
                      if (data == 3) return '<span style="background:#cd7f32; color:#fff; border-radius:50%; width:24px; height:24px; display:inline-flex; align-items:center; justify-content:center; font-weight:700; font-size:11px;">' + data + '</span>';
                      return '<span style="font-weight:600; color:#aaa;">' + data + '</span>';
                  }
                },
                { data: 'bid_amount',
                  render: function (data, type, row, meta) {
                      var style = meta.row === 0 ? 'font-weight:700; color:#0d6efd; font-size:14px;' : 'font-weight:600; color:#1a1a2e;';
                      return '<span style="' + style + '">' + data + '</span>';
                  }
                },
                { data: 'total_npv',
                  render: function (data) {
                      return '<span style="color:#555;">' + data + '</span>';
                  }
                },
                { data: 'created_at',
                  render: function (data) {
                      return '<span style="color:#888; font-size:12px;"><i class="fas fa-clock mr-1"></i>' + data + '</span>';
                  }
                }
            ],
            order: [[1, 'desc']],
            pageLength: 10,
            lengthChange: false,
            searching: false,
            info: false,
            dom: 'rt',
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
                emptyTable: '<div class="text-center py-3 text-muted"><i class="fas fa-trophy fa-2x d-block mb-2"></i>No bids placed yet.</div>'
            },
            drawCallback: function () {
                var rows = this.api().rows().data();
                if (rows.length > 0) {
                    $('#top-bids-highest').text(rows[0].bid_amount);
                    $('#top-bids-npv').text(rows[0].total_npv);
                } else {
                    $('#top-bids-highest').html('&mdash;');
                    $('#top-bids-npv').html('&mdash;');
                }
            }
        });
    });

    $('#myBidsModal').on('shown.bs.modal', function () {
        if (myBidsDT) {
            myBidsDT.ajax.reload(null, false);
            return;
        }
        myBidsDT = $('#my-bids-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: { url: myBidsUrl, type: 'GET' },
            columns: [
                { data: 'bid_number', orderable: false, searchable: false, width: '50px',
                  render: function (data) {
                      return '<span style="font-weight:700; color:#2980b9; font-size:13px;">' + data + '</span>';
                  }
                },
                { data: 'bid_amount',
                  render: function (data) {
                      return '<span style="font-weight:700; color:#1a1a2e;">' + data + '</span>';
                  }
                },
                { data: 'total_npv',
                  render: function (data) {
                      return '<span style="color:#555;">' + data + '</span>';
                  }
                },
                { data: 'status', orderable: false },
                { data: 'created_at',
                  render: function (data) {
                      return '<span style="color:#888; font-size:12px;"><i class="fas fa-clock mr-1"></i>' + data + '</span>';
                  }
                }
            ],
            order: [[4, 'desc']],
            pageLength: 10,
            dom: '<"d-flex justify-content-between align-items-center mb-2"lf>rt<"d-flex justify-content-between align-items-center mt-2"ip>',
            language: {
                processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
                emptyTable: '<div class="text-center py-3 text-muted"><i class="fas fa-gavel fa-2x d-block mb-2"></i>No bids placed yet.</div>',
                search: '<i class="fas fa-search"></i>',
                searchPlaceholder: 'Search bids...'
            },
            drawCallback: function () {
                var info = this.api().page.info();
                var data = this.api().data();
                var total = info.recordsTotal;
                var valid = 0, highest = 0;
                this.api().rows().data().each(function (row) {
                    if (row.status && row.status.indexOf('badge-success') !== -1) valid++;
                    var raw = parseFloat((row.bid_amount || '').replace(/[^0-9.]/g, ''));
                    if (raw > highest) highest = raw;
                });
                $('#my-bids-total').text(total);
                $('#my-bids-valid').text(valid);
                $('#my-bids-highest').text(highest > 0 ? '\u20b9 ' + highest.toLocaleString('en-IN') : '\u2014');
            }
        });
    });

    // â”€â”€ Disable inputs initially â”€â”€
    $('.npv-amount-input').prop('disabled', true);
    $('#btn-place-bid').prop('disabled', true);

    function validateBidAmount(value) {
        if (isNaN(value) || value <= 0) return 'Please enter a valid amount.';
        if (incrementAmountType === 'mandatory') {
            var minBid = baseValue + increment;
            if (value < minBid) return 'Amount must be at least â‚¹ ' + minBid.toLocaleString('en-IN') + ' (Base + Increment).';
            if (incrementType !== 'fixed' && increment > 0 && Math.round(value % increment * 1e6) / 1e6 > 0.001) return 'Amount must be a multiple of â‚¹ ' + increment.toLocaleString('en-IN') + '.';
        }
        return null;
    }

    // â”€â”€ Submit: verify bid amount â”€â”€
    $('#btn-verify-bid').on('click', function () {
        var raw   = $('#bid-amount').val().replace(/[â‚¹,\s]/g, '');
        var value = parseFloat(raw);

        $('#bid-error').hide();
        $('#bid-valid-msg').hide();
        $('#bid-amount').removeClass('is-invalid');

        var err = validateBidAmount(value);
        if (err) {
            $('#bid-error-msg').text(err);
            $('#bid-error').show();
            $('#bid-amount').addClass('is-invalid');
            return;
        }

        bidAmount = value;
        $('#bid-amount').prop('disabled', true);
        $(this).prop('disabled', true);
        $('#btn-reset-bid').show();
        $('.npv-amount-input').prop('disabled', false);
        $('#npv-table-section').show();
        $('#bid-valid-msg').show();
        $('#note-distribute').show();
        $('#remaining-amount').text(formatINR(value.toFixed(2)));
        recalculate();
    });

    // â”€â”€ Reset â”€â”€
    $('#btn-reset-bid').on('click', function () {
        bidAmount = 0;
        $('#bid-amount').val('').prop('disabled', false).removeClass('is-invalid');
        $('#btn-verify-bid').prop('disabled', false);
        $('#btn-reset-bid').hide();
        $('#bid-valid-msg').hide();
        $('#bid-error').hide();
        $('#note-distribute').hide();
        $('#npv-table-section').hide();
        $('#btn-place-bid').prop('disabled', true);
        $('.npv-amount-input').val('').prop('disabled', true);
        $('.col-total').text('0.00');
        $('#grand-total').text('0.00');
        $('.row-total').text('0.00');
        $('#distribution-error').hide();
    });

    // â”€â”€ Live totals â”€â”€
    function recalculate() {
        var grandTotal = 0;

        $('.col-total').each(function () { $(this).text('0.00'); });
        $('.col-npv-total').each(function () { $(this).text('0.00'); });

        $('tbody tr[data-category-id]').each(function () {
            var rowTotal = 0;
            $(this).find('.npv-amount-input').each(function () {
                rowTotal += parseFloat($(this).val()) || 0;
            });
            $(this).find('.row-total').text(rowTotal.toFixed(2));
        });

        $('.npv-amount-input').each(function () {
            var period = $(this).data('period');
            var val    = parseFloat($(this).val()) || 0;
            var $ct    = $('.col-total[data-period="' + period + '"]');
            $ct.text((parseFloat($ct.text()) + val).toFixed(2));
            var $nt = $('.col-npv-total[data-period="' + period + '"]');
            var pct = parseFloat($nt.data('pct')) || 0;
            $nt.text((parseFloat($nt.text()) + val * pct).toFixed(2));
        });

        $('.col-total').each(function () { grandTotal += parseFloat($(this).text()) || 0; });

        $('#grand-total').text(grandTotal.toFixed(2));

        if (bidAmount > 0) {
            var remaining = bidAmount - grandTotal;
            $('#remaining-amount').text(formatINR(remaining.toFixed(2)));
            var matched = Math.abs(remaining) < 0.01;
            $('#btn-place-bid').prop('disabled', !matched);
            $('#note-distribute').toggle(!matched);
        }
    }

    $(document).on('input', '.npv-amount-input', recalculate);

    // â”€â”€ Pusher: real-time bid updates â”€â”€
    var pusher  = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
        encrypted: true
    });
    var channel = pusher.subscribe('auction.{{ $auction->id }}');

    channel.bind('bid.placed', function (data) {
        baseValue = parseFloat(data.highest_bid);
        var minNext = parseFloat(data.minimum_next);

        // Update stat cards
        $('#portal-base-value').html('&#8377; ' + Number(data.highest_bid).toLocaleString('en-IN'));
        if (data.total_npv) {
            $('#portal-npv-value').html('&#8377; ' + Number(data.total_npv).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        }

        // Update ALL hint notes with new values
        $('#note-base-value').html('<i class="fas fa-info-circle"></i> Must be greater than Current Base Value (&#8377; ' + Number(data.highest_bid).toLocaleString('en-IN') + ')');
        if (incrementAmountType === 'mandatory') {
            $('#note-min-bid').html('<i class="fas fa-info-circle"></i> Must be at least &#8377; ' + Number(minNext).toLocaleString('en-IN') + ' (Base + Increment)');
        }

        if (topBidsDT) topBidsDT.ajax.reload(null, false);
        if (myBidsDT) myBidsDT.ajax.reload(null, false);

        var bidLocked = $('#bid-amount').prop('disabled');

        if (bidLocked) {
            // In distribution stage â€” full reset, bidAmount is now stale
            bidAmount = 0;
            $('#bid-amount').val('').prop('disabled', false).removeClass('is-invalid');
            $('#btn-verify-bid').prop('disabled', false);
            $('#btn-reset-bid').hide();
            $('#bid-valid-msg').hide();
            $('#bid-error').hide();
            $('#note-distribute').hide();
            $('#npv-table-section').hide();
            $('#btn-place-bid').prop('disabled', true);
            $('.npv-amount-input').val('').prop('disabled', true);
            $('.col-total').text('0.00');
            $('#grand-total').text('0.00');
            $('.row-total').text('0.00');
            $('#distribution-error').hide();
            toastr.warning('A new bid was placed. Your form has been reset â€” please enter a new bid above â‚¹ ' + Number(minNext).toLocaleString('en-IN') + '.', 'Bid Reset', { timeOut: 8000, progressBar: true });
        } else {
            // Not yet locked â€” re-validate whatever is typed right now
            var raw = $('#bid-amount').val().replace(/[â‚¹,\s]/g, '');
            var typed = parseFloat(raw);
            if (!isNaN(typed) && typed > 0) {
                var err = validateBidAmount(typed);
                if (err) {
                    $('#bid-error-msg').text(err);
                    $('#bid-error').show();
                    $('#bid-amount').addClass('is-invalid');
                } else {
                    $('#bid-error').hide();
                    $('#bid-amount').removeClass('is-invalid');
                }
            }
            toastr.info('A new bid has been placed.<br><small>New minimum: â‚¹ ' + Number(minNext).toLocaleString('en-IN') + '</small>', 'New Bid', { timeOut: 6000, progressBar: true, allowHtml: true });
        }
    });

    // â”€â”€ Place Bid â”€â”€
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

        function submitBid() {
            Swal.fire({
                title: 'Confirm Bid',
                html: 'You are placing a bid of <strong>' + formatINR(bidAmount) + '</strong>.<br>This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
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
                    beforeSend: function () {
                        $('#bid-loader-overlay').addClass('active');
                    },
                    success: function (res) {
                        $('#bid-loader-overlay').removeClass('active');
                        toastr.success(res.message);
                        $('#btn-place-bid').prop('disabled', true);
                        $('.npv-amount-input').prop('disabled', true);
                    },
                    error: function (xhr) {
                        $('#bid-loader-overlay').removeClass('active');
                        var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Something went wrong.';
                        $('#distribution-error-msg').text(msg);
                        $('#distribution-error').show();
                    }
                });
            });
        }

        // For recommend type: warn if bid is below base value
        if (incrementAmountType === 'recommend' && bidAmount < baseValue + increment) {
            Swal.fire({
                title: '&#9888; Challenge Amount Below Current Resolution Amount (Base Value)',
                html: '<div style="text-align:left; font-size:13px; line-height:1.7;">' +
                      '<p>Your bid of <strong style="color:#e74c3c;">' + formatINR(bidAmount) + '</strong> is <strong>below the recommended minimum</strong> of <strong style="color:#0d6efd;">' + formatINR(baseValue + increment) + '</strong>.</p>' +
                      '<p style="margin-top:8px;">once submitted you bid is irreversible. However, you can submit fresh bid, as long as the challenge process is live.  </p>' +
                      '<p style="margin-top:8px; color:#888;">Do you still wish to proceed with this bid?</p>' +
                      '</div>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-exclamation-triangle mr-1"></i> Yes, Submit Anyway',
                cancelButtonText: 'No, Let Me Revise',
                reverseButtons: true
            }).then(function (result) {
                if (result.isConfirmed) submitBid();
            });
        } else {
            submitBid();
        }
    });
});
</script>
@endsection
