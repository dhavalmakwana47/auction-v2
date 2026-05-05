@extends('app.layout.app')
@section('page_title') Dashboard @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/ra/dashboard.css') }}">
<style>
    .status-filter-wrap { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
    .btn-status-filter {
        border-radius: 20px; font-size: 12px; font-weight: 600;
        padding: 5px 16px; border: 2px solid rgba(255,255,255,0.5);
        background: rgba(255,255,255,0.15); color: #fff; transition: all 0.2s;
    }
    .btn-status-filter:hover, .btn-status-filter.active {
        background: #fff; color: #11998e;
    }
    .badge-status {
        display: inline-block; border-radius: 20px;
        padding: 3px 12px; font-size: 11px; font-weight: 600;
    }
    .badge-status-pending    { background:#fef9e7; color:#d68910; }
    .badge-status-in_progress { background:#e8f4fd; color:#2980b9; }
    .badge-status-completed   { background:#eafaf1; color:#1e8449; }
</style>
@endsection

@section('content-body')

<div class="ra-portal-header">
    <div>
        <h4><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</h4>
        <small>Overview of all Challenge Mechanism</small>
    </div>
    <div class="status-filter-wrap mt-2 mt-sm-0">
        <button class="btn-status-filter" data-status="">All</button>
        <button class="btn-status-filter active" data-status="pending">Pending</button>
        <button class="btn-status-filter" data-status="in_progress">In Progress</button>
        <button class="btn-status-filter" data-status="completed">Completed</button>
    </div>
</div>

@if($auctions->isEmpty())
    <div class="alert alert-warning" style="border-radius:12px;">
        <i class="fas fa-exclamation-triangle mr-2"></i> No Challenge Mechanism found.
    </div>
@else
    <div class="row" id="auctions-grid">
        @foreach($auctions as $auction)
        <div class="col-md-6 col-12 mb-4 auction-item" data-status="{{ $auction->status }}">
            <div class="ra-auction-card card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div style="flex:1; min-width:0;">
                            {{-- Current Values at top --}}
                            <div class="d-flex mb-2" style="gap:12px; flex-wrap:wrap;">
                                <div style="background:#e8f8f0; border-radius:8px; padding:6px 12px; font-size:12px;">
                                    <div style="color:#888; font-size:10px; font-weight:600; text-transform:uppercase;">Current Base Value</div>
                                    <div style="color:#27ae60; font-weight:700;"><i class="fas fa-rupee-sign mr-1"></i>{{ number_format($auction->base_price) }}</div>
                                </div>
                                <div style="background:#e8f4fd; border-radius:8px; padding:6px 12px; font-size:12px;">
                                    <div style="color:#888; font-size:10px; font-weight:600; text-transform:uppercase;">Current NPV Value</div>
                                    <div style="color:#2980b9; font-weight:700;"><i class="fas fa-rupee-sign mr-1"></i>{{ number_format($auction->initial_npv_value, 2) }}</div>
                                </div>
                            </div>
                            {{-- Title & Status --}}
                            <div class="d-flex align-items-center mb-1" style="gap:8px;">
                                <div class="ra-auction-title mb-0">{{ $auction->corporate_debtor_name }}</div>
                                <span class="badge-status badge-status-{{ $auction->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $auction->status)) }}
                                </span>
                            </div>
                            <div class="ra-auction-meta">
                                <span><i class="fas fa-calendar-alt mr-1"></i> {{ $auction->meeting_date }}</span>
                            </div>
                            <div class="ra-auction-meta mt-1">
                                <i class="fas fa-tags mr-1"></i>
                                @foreach($auction->npvCategories as $cat)
                                    <span class="badge-npv">{{ $cat->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <a href="{{ route('auctions.show', $auction) }}" class="btn btn-view-auction ml-3">
                            <i class="fas fa-eye mr-1"></i> View
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div id="no-results" class="alert alert-info" style="border-radius:12px; display:none;">
        <i class="fas fa-info-circle mr-2"></i> No Challenge Mechanism found for the selected status.
    </div>
@endif

@endsection

@section('footer-script')
<script>
$(function () {

    function applyFilter(status) {
        var visible = 0;
        $('.auction-item').each(function () {
            var match = !status || $(this).data('status') === status;
            $(this).toggle(match);
            if (match) visible++;
        });
        $('#no-results').toggle(visible === 0);
    }

    $('.btn-status-filter').on('click', function () {
        $('.btn-status-filter').removeClass('active');
        $(this).addClass('active');
        applyFilter($(this).data('status'));
    });

    // default: show pending on load
    applyFilter('pending');
});
</script>
@endsection
