@extends('app.layout.app')
@section('page_title') RA Dashboard @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/ra/dashboard.css') }}">
@endsection

@section('content-body')

<div class="ra-portal-header">
    <div>
        <h4><i class="fas fa-list mr-2"></i> My Assigned Auctions</h4>
        <small>Select an auction to enter the Challenge Mechanism Portal</small>
    </div>
</div>

@if($auctions->isEmpty())
    <div class="alert alert-warning" style="border-radius:12px;">
        <i class="fas fa-exclamation-triangle mr-2"></i> No in-progress auctions have been assigned to you.
    </div>
@else
    <div class="row">
        @foreach($auctions as $auction)
        <div class="col-md-6 col-12 mb-4">
            <div class="ra-auction-card card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <div class="ra-auction-title">{{ $auction->corporate_debtor_name }}</div>
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
                        <a href="{{ route('ra.auction.portal', $auction) }}" class="btn btn-view-auction">
                            <i class="fas fa-gavel mr-1"></i> View
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
