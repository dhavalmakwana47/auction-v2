@extends('app.layout.app')
@section('page_title') Users @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('app/users/list.css') }}">
@endsection

@section('content-body')
<div class="card shadow" style="border-radius:12px; border:none;">

    <div class="card-header page-header-card d-flex align-items-center justify-content-between flex-wrap py-3 px-4">
        <div class="d-flex align-items-center">
            <div class="icon-circle mr-3">
                <i class="fas fa-users text-white"></i>
            </div>
            <div>
                <h3 class="card-title text-white font-weight-bold mb-0" style="font-size:18px;">User Management</h3>
                <small class="text-white-50">Manage all system users</small>
            </div>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-light btn-sm font-weight-bold mt-2 mt-sm-0" style="border-radius:20px;">
            <i class="fas fa-plus mr-1"></i> Add User
        </a>
    </div>

    <div class="card-body p-0 p-md-3">
        <div id="users-loader" class="text-center py-3" style="display:none;">
            <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
            <p class="text-muted mt-2 mb-0" style="font-size:13px;">Loading...</p>
        </div>
        <div class="table-responsive">
            <table id="users-table" class="table table-hover w-100" style="font-size:14px;">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Name</th>
                        <th>Email ID</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created Date</th>
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
<script>
    var usersConfig = {
        datatableUrl: '{{ route('users.datatable') }}',
        csrfToken:    '{{ csrf_token() }}'
    };
</script>
<script src="{{ asset('app/users/list.js') }}"></script>
@endsection
