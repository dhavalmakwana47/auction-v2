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
        <small>Overview of all auctions</small>
    </div>
    <div class="status-filter-wrap mt-2 mt-sm-0">
        <button class="btn-status-filter active" data-status="">All</button>
        <button class="btn-status-filter" data-status="pending">Pending</button>
        <button class="btn-status-filter" data-status="in_progress">In Progress</button>
        <button class="btn-status-filter" data-status="completed">Completed</button>
    </div>
</div>

@if($auctions->isEmpty())
    <div class="alert alert-warning" style="border-radius:12px;">
        <i class="fas fa-exclamation-triangle mr-2"></i> No auctions found.
    </div>
@else
    <div class="row" id="auctions-grid">
        @foreach($auctions as $auction)
        <div class="col-md-6 col-12 mb-4 auction-item" data-status="{{ $auction->status }}">
            <div class="ra-auction-card card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div style="flex:1; min-width:0;">
                            <div class="d-flex align-items-center mb-1" style="gap:8px;">
                                <div class="ra-auction-title mb-0">{{ $auction->corporate_debtor_name }}</div>
                                <span class="badge-status badge-status-{{ $auction->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $auction->status)) }}
                                </span>
                            </div>
                            <div class="ra-auction-meta">
                                <span><i class="fas fa-calendar-alt mr-1"></i> {{ $auction->meeting_date }}</span>
                                <span class="ml-3"><i class="fas fa-rupee-sign mr-1"></i> Base: {{ number_format($auction->base_price) }}</span>
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
        <i class="fas fa-info-circle mr-2"></i> No auctions found for the selected status.
    </div>
@endif

@endsection

@section('footer-script')
<script>
$(function () {
    $('.btn-status-filter').on('click', function () {
        var status = $(this).data('status');

        $('.btn-status-filter').removeClass('active');
        $(this).addClass('active');

        var visible = 0;
        $('.auction-item').each(function () {
            var match = !status || $(this).data('status') === status;
            $(this).toggle(match);
            if (match) visible++;
        });

        $('#no-results').toggle(visible === 0);
    });
});
</script>
@endsection
