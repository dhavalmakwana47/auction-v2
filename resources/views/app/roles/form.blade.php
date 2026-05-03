@extends('app.layout.app')
@section('page_title') {{ isset($role) ? 'Edit Role' : 'Add Role' }} @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/roles/form.css') }}">
@endsection

@section('content-body')

<div class="breadcrumb-nav mb-3">
    <a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a>
    <span class="mx-2">/</span>
    <a href="{{ route('roles.index') }}">Roles</a>
    <span class="mx-2">/</span>
    <span>{{ isset($role) ? 'Edit Role' : 'Add Role' }}</span>
</div>

<div class="card form-card">

    <div class="form-header d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center">
            <div class="icon-wrap mr-3">
                <i class="fas fa-{{ isset($role) ? 'user-cog' : 'user-tag' }} text-white fa-lg"></i>
            </div>
            <div>
                <h4 class="text-white font-weight-bold mb-0">{{ isset($role) ? 'Edit Role' : 'Add New Role' }}</h4>
                <small class="text-white-50">{{ isset($role) ? 'Update role & permissions' : 'Define a new role with permissions' }}</small>
            </div>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm mt-2 mt-sm-0" style="border-radius:20px;">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <form action="{{ isset($role) ? route('roles.update', $role) : route('roles.store') }}" method="POST">
        @csrf
        @if(isset($role)) @method('PUT') @endif

        <div class="card-body px-4 py-4">

            <p class="form-section-title"><i class="fas fa-tag mr-1"></i> Role Details</p>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Role Name <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-user-tag input-icon"></i>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $role->name ?? '') }}"
                                placeholder="e.g. manager, editor">
                        </div>
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            @if($permissions->count())
            <p class="form-section-title mt-3"><i class="fas fa-key mr-1"></i> Assign Permissions</p>

            <div class="select-all-wrap mb-3">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" type="checkbox" id="select_all">
                    <label class="custom-control-label" for="select_all" style="font-size:13px; cursor:pointer; font-weight:600;">
                        Select / Deselect All
                    </label>
                </div>
            </div>

            <div class="row">
                @foreach($permissions as $id => $perm)
                <div class="col-xl-3 col-md-4 col-sm-6 col-12 mb-2">
                    <label class="perm-card d-flex align-items-center mb-0 w-100" for="perm_{{ $id }}">
                        <div class="custom-control custom-checkbox mr-2 mb-0">
                            <input class="custom-control-input perm-checkbox" type="checkbox"
                                name="permissions[]" value="{{ $perm }}"
                                id="perm_{{ $id }}"
                                {{ in_array($perm, old('permissions', isset($role) ? $role->permissions->pluck('name')->toArray() : [])) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="perm_{{ $id }}"></label>
                        </div>
                        <span class="perm-label">
                            <i class="fas fa-shield-alt mr-1 text-muted" style="font-size:11px;"></i>
                            {{ ucwords($perm) }}
                        </span>
                    </label>
                </div>
                @endforeach
            </div>
            @endif

        </div>

        <div class="card-footer bg-white border-top px-4 py-3 d-flex flex-wrap align-items-center" style="border-radius:0 0 16px 16px;">
            <button type="submit" class="btn btn-primary btn-submit text-white">
                <i class="fas fa-{{ isset($role) ? 'sync-alt' : 'save' }} mr-2"></i>{{ isset($role) ? 'Update Role' : 'Create Role' }}
            </button>
            <a href="{{ route('roles.index') }}" class="btn btn-light btn-cancel ml-3">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@section('footer-script')
<script src="{{ asset('app/roles/form.js') }}"></script>
@endsection
