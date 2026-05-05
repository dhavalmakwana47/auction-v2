@extends('app.layout.app')
@section('page_title') Logs @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/logs/list.css') }}">
@endsection

@section('content-body')
<div class="card shadow" style="border-radius:12px; border:none;">
    <div class="card-header page-header-card d-flex align-items-center justify-content-between flex-wrap py-3 px-4">
        <div class="d-flex align-items-center">
            <div class="icon-circle mr-3">
                <i class="fas fa-history text-white"></i>
            </div>
            <div>
                <h3 class="card-title text-white font-weight-bold mb-0" style="font-size:18px;">Activity Logs</h3>
                <small class="text-white-50">Track all user and system events</small>
            </div>
        </div>
        <a id="logs-export-btn" href="{{ route('logs.export') }}" class="btn btn-light btn-sm font-weight-bold mt-2 mt-sm-0" style="border-radius:20px;">
            <i class="fas fa-file-csv mr-1"></i> Export CSV
        </a>
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-2 mb-2">
                <input type="text" id="filter-event" class="form-control" placeholder="Event">
            </div>
            <div class="col-md-2 mb-2">
                <input type="text" id="filter-action" class="form-control" placeholder="Action">
            </div>
            <div class="col-md-2 mb-2">
                <input type="number" id="filter-status-code" class="form-control" placeholder="Status code">
            </div>
            <div class="col-md-2 mb-2">
                <input type="date" id="filter-date-from" class="form-control">
            </div>
            <div class="col-md-2 mb-2">
                <input type="date" id="filter-date-to" class="form-control">
            </div>
            @if(auth()->user()->getRoleNames()->contains('admin'))
            <div class="col-md-2 mb-2">
                <select id="filter-user-id" class="form-control">
                    <option value="">All users</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <div id="logs-loader" class="text-center py-3" style="display:none;">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="text-muted mt-2 mb-0" style="font-size:13px;">Loading logs...</p>
        </div>

        <div class="table-responsive">
            <table id="logs-table" class="table table-hover w-100" style="font-size:14px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Event / Action</th>
                        <th>Description</th>
                        <th>Route</th>
                        <th>Status</th>
                        <th>Time</th>
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
<script>
    var logsConfig = {
        datatableUrl: "{{ route('logs.datatable') }}",
        exportUrl: "{{ route('logs.export') }}"
    };
</script>
<script src="{{ asset('app/logs/list.js') }}"></script>
@endsection
