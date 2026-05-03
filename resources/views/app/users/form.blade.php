@extends('app.layout.app')
@section('page_title') {{ isset($user) ? 'Edit User' : 'Add User' }} @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/users/form.css') }}">
@endsection

@section('content-body')

<div class="breadcrumb-nav mb-3">
    <a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a>
    <span class="mx-2">/</span>
    <a href="{{ route('users.index') }}">Users</a>
    <span class="mx-2">/</span>
    <span>{{ isset($user) ? 'Edit User' : 'Add User' }}</span>
</div>

<div class="card form-card">

    <div class="form-header d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center">
            <div class="icon-wrap mr-3">
                <i class="fas fa-{{ isset($user) ? 'user-edit' : 'user-plus' }} text-white fa-lg"></i>
            </div>
            <div>
                <h4 class="text-white font-weight-bold mb-0">{{ isset($user) ? 'Edit User' : 'Add New User' }}</h4>
                <small class="text-white-50">{{ isset($user) ? 'Update user information' : 'Fill in the details to create a new user' }}</small>
            </div>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-light btn-sm mt-2 mt-sm-0" style="border-radius:20px;">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST" autocomplete="off">
        @csrf
        @if(isset($user)) @method('PUT') @endif

        <div class="card-body px-4 py-4">

            <p class="form-section-title"><i class="fas fa-info-circle mr-1"></i> Basic Information</p>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Full Name <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-user input-icon"></i>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name ?? '') }}"
                                placeholder="Enter full name">
                        </div>
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Email Address <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" name="email"
                                class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email', $user->email ?? '') }}"
                                placeholder="Enter email address">
                        </div>
                        @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <p class="form-section-title mt-3"><i class="fas fa-lock mr-1"></i> Password</p>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>
                            Password @if(!isset($user))<span class="text-danger">*</span>@endif
                            @if(isset($user))<small class="text-muted font-weight-normal">(leave blank to keep current)</small>@endif
                        </label>
                        <div class="input-icon-wrap" style="position:relative;">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password" id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="{{ isset($user) ? 'Leave blank to keep current' : 'Enter password' }}"
                                style="padding-right:40px;">
                            <span class="password-toggle" onclick="togglePass('password', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                        @error('password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Confirm Password @if(!isset($user))<span class="text-danger">*</span>@endif</label>
                        <div class="input-icon-wrap" style="position:relative;">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="form-control"
                                placeholder="Confirm password"
                                style="padding-right:40px;">
                            <span class="password-toggle" onclick="togglePass('password_confirmation', this)">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <p class="form-section-title mt-3"><i class="fas fa-sliders-h mr-1"></i> Role & Status</p>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Assign Role</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-user-tag input-icon"></i>
                            <select name="role" class="form-control">
                                <option value="">-- Select Role --</option>
                                @foreach($roles as $id => $name)
                                    <option value="{{ $name }}"
                                        {{ old('role', isset($user) && $user->hasRole($name) ? $name : '') == $name ? 'selected' : '' }}>
                                        {{ ucfirst($name) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Account Status</label>
                        <div class="d-flex mt-1" style="gap:16px;">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="is_active" id="active" value="1"
                                    {{ old('is_active', $user->is_active ?? 1) == 1 ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-normal" for="active">
                                    <span class="text-success"><i class="fas fa-check-circle mr-1"></i>Active</span>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="is_active" id="inactive" value="0"
                                    {{ old('is_active', $user->is_active ?? 1) == 0 ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-normal" for="inactive">
                                    <span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-footer bg-white border-top px-4 py-3 d-flex flex-wrap align-items-center" style="border-radius:0 0 16px 16px;">
            <button type="submit" class="btn btn-primary btn-submit text-white">
                <i class="fas fa-{{ isset($user) ? 'sync-alt' : 'save' }} mr-2"></i>{{ isset($user) ? 'Update User' : 'Create User' }}
            </button>
            <a href="{{ route('users.index') }}" class="btn btn-light btn-cancel ml-3">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@section('footer-script')
<script src="{{ asset('app/users/form.js') }}"></script>
@endsection
