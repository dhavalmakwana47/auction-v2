@extends('app.layout.app')
@section('page_title') Challenge Mechanism @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/auctions/list.css') }}">
@endsection

@section('content-body')
<div class="card shadow" style="border-radius:12px; border:none;">

    <div class="card-header page-header-card d-flex align-items-center justify-content-between flex-wrap py-3 px-4">
        <div class="d-flex align-items-center">
            <div class="icon-circle mr-3">
                <i class="fas fa-gavel text-white"></i>
            </div>
            <div>
                <h3 class="card-title text-white font-weight-bold mb-0" style="font-size:18px;">Challenge Mechanism</h3>
                <small class="text-white-50">Manage all challenge mechanisms</small>
            </div>
        </div>
        <a href="{{ route('auctions.create') }}" class="btn btn-light btn-sm font-weight-bold mt-2 mt-sm-0" style="border-radius:20px;">
            <i class="fas fa-plus mr-1"></i> Add Challenge Mechanism
        </a>
    </div>

    <div class="card-body p-0 p-md-3">
        <div id="auctions-loader" class="text-center py-3" style="display:none;">
            <i class="fas fa-spinner fa-spin fa-2x text-success"></i>
            <p class="text-muted mt-2 mb-0" style="font-size:13px;">Loading...</p>
        </div>
        <div class="table-responsive">
            <table id="auctions-table" class="table table-hover w-100" style="font-size:14px;">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Corporate Debtor</th>
                        <th class="none">Date of Challenge Process</th>
                        <th class="none">Base Price</th>
                        <th class="none">NPV</th>
                        <th>Increment Type</th>
                        <th class="none">Created Date</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
            </table>
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
    var auctionsConfig = {
        datatableUrl: '{{ route('auctions.datatable') }}',
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script src="{{ asset('app/auctions/list.js') }}"></script>
@endsection
