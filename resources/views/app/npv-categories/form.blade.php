@extends('app.layout.app')
@section('page_title') {{ isset($category) ? 'Edit Category' : 'Add Category' }} @endsection

@section('header-script')
<link rel="stylesheet" href="{{ asset('app/npv-categories/form.css') }}">
@endsection

@section('content-body')

<div class="breadcrumb-nav mb-3">
    <a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a>
    <span class="mx-2">/</span>
    <a href="{{ route('npv-categories.index') }}">Description of Creditors</a>
    <span class="mx-2">/</span>
    <span>{{ isset($category) ? 'Edit Category' : 'Add Description of Creditors' }}</span>
</div>

<div class="card form-card">

    <div class="form-header d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center">
            <div class="icon-wrap mr-3">
                <i class="fas fa-{{ isset($category) ? 'edit' : 'plus' }} text-white fa-lg"></i>
            </div>
            <div>
                <h4 class="text-white font-weight-bold mb-0">{{ isset($category) ? 'Edit Category' : 'Add Description of Creditors' }}</h4>
                <small class="text-white-50">{{ isset($category) ? 'Update Description of Creditors' : '' }}</small>
            </div>
        </div>
        <a href="{{ route('npv-categories.index') }}" class="btn btn-light btn-sm mt-2 mt-sm-0" style="border-radius:20px;">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <form action="{{ isset($category) ? route('npv-categories.update', $category) : route('npv-categories.store') }}" method="POST">
        @csrf
        @if(isset($category)) @method('PUT') @endif

        <div class="card-body px-4 py-4">

            <p class="form-section-title"><i class="fas fa-info-circle mr-1"></i> Description of Creditors Details</p>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Name <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-tag input-icon"></i>
                            <input type="text" name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $category->name ?? '') }}"
                                placeholder="Enter Description of Creditors name">
                        </div>
                        @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Status</label>
                        <div class="d-flex mt-1" style="gap:16px;">
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="is_active" id="active" value="1"
                                    {{ old('is_active', $category->is_active ?? 1) == 1 ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-normal" for="active">
                                    <span class="text-success"><i class="fas fa-check-circle mr-1"></i>Active</span>
                                </label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input class="custom-control-input" type="radio" name="is_active" id="inactive" value="0"
                                    {{ old('is_active', $category->is_active ?? 1) == 0 ? 'checked' : '' }}>
                                <label class="custom-control-label font-weight-normal" for="inactive">
                                    <span class="text-danger"><i class="fas fa-times-circle mr-1"></i>Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Enter Description of Creditors description (optional)">{{ old('description', $category->description ?? '') }}</textarea>
                        @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

        </div>

        <div class="card-footer bg-white border-top px-4 py-3 d-flex flex-wrap align-items-center" style="border-radius:0 0 16px 16px;">
            <button type="submit" class="btn btn-submit">
                <i class="fas fa-{{ isset($category) ? 'sync-alt' : 'save' }} mr-2"></i>{{ isset($category) ? 'Update Description of Creditors' : 'Create Description of Creditors' }}
            </button>
            <a href="{{ route('npv-categories.index') }}" class="btn btn-light btn-cancel ml-3">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
        </div>
    </form>
</div>

@endsection
