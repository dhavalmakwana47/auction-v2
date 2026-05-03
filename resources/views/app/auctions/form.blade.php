@extends('app.layout.app')
@section('page_title') {{ isset($auction) ? 'Edit Auction' : 'Add Auction' }} @endsection

@section('header-script')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<link rel="stylesheet" href="{{ asset('app/auctions/form.css') }}">
@endsection

@section('content-body')

<div class="breadcrumb-nav mb-3">
    <a href="{{ route('dashboard') }}"><i class="fas fa-home mr-1"></i>Dashboard</a>
    <span class="mx-2">/</span>
    <a href="{{ route('auctions.index') }}">Auctions</a>
    <span class="mx-2">/</span>
    <span>{{ isset($auction) ? 'Edit Auction' : 'Add Auction' }}</span>
</div>

<div class="card form-card">

    <div class="form-header d-flex align-items-center justify-content-between flex-wrap">
        <div class="d-flex align-items-center">
            <div class="icon-wrap mr-3">
                <i class="fas fa-gavel text-white fa-lg"></i>
            </div>
            <div>
                <h4 class="text-white font-weight-bold mb-0">{{ isset($auction) ? 'Edit Auction' : 'Add New Auction' }}</h4>
                <small class="text-white-50">{{ isset($auction) ? 'Update auction details' : 'Fill in the details to create a new auction' }}</small>
            </div>
        </div>
        <a href="{{ route('auctions.index') }}" class="btn btn-light btn-sm mt-2 mt-sm-0" style="border-radius:20px;">
            <i class="fas fa-arrow-left mr-1"></i> Back to List
        </a>
    </div>

    <form action="{{ isset($auction) ? route('auctions.update', $auction) : route('auctions.store') }}" method="POST">
        @csrf
        @if(isset($auction)) @method('PUT') @endif

        <div class="card-body px-4 py-4">

            {{-- Basic Info --}}
            <p class="form-section-title"><i class="fas fa-info-circle mr-1"></i> Auction Details</p>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Corporate Debtor Name <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-building input-icon"></i>
                            <input type="text" name="corporate_debtor_name"
                                class="form-control @error('corporate_debtor_name') is-invalid @enderror"
                                value="{{ old('corporate_debtor_name', $auction->corporate_debtor_name ?? '') }}"
                                placeholder="Enter corporate debtor name">
                        </div>
                        @error('corporate_debtor_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group">
                        <label>Meeting Date <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-calendar-alt input-icon"></i>
                            <input type="date" name="meeting_date"
                                class="form-control @error('meeting_date') is-invalid @enderror"
                                value="{{ old('meeting_date', $auction->meeting_date ?? '') }}">
                        </div>
                        @error('meeting_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Base Price <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-rupee-sign input-icon"></i>
                            <input type="text" name="base_price"
                                class="form-control @error('base_price') is-invalid @enderror"
                                value="{{ old('base_price', $auction->base_price ?? '') }}"
                                placeholder="e.g. 5000000">
                        </div>
                        @error('base_price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Increment Amount <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-plus-circle input-icon"></i>
                            <input type="text" name="increment_amount"
                                class="form-control @error('increment_amount') is-invalid @enderror"
                                value="{{ old('increment_amount', $auction->increment_amount ?? '') }}"
                                placeholder="e.g. 100000">
                        </div>
                        @error('increment_amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Increment Amount Type <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-tag input-icon"></i>
                            <select name="increment_amount_type" class="form-control @error('increment_amount_type') is-invalid @enderror">
                                <option value="">-- Select Type --</option>
                                <option value="recommend" {{ old('increment_amount_type', $auction->increment_amount_type ?? '') == 'recommend' ? 'selected' : '' }}>Recommend</option>
                                <option value="mandatory" {{ old('increment_amount_type', $auction->increment_amount_type ?? '') == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
                            </select>
                        </div>
                        @error('increment_amount_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @unless($errors->has('increment_amount_type'))<div class="invalid-feedback"></div>@endunless
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Increment Type <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-sliders-h input-icon"></i>
                            <select name="increment_type" class="form-control @error('increment_type') is-invalid @enderror">
                                <option value="">-- Select Type --</option>
                                <option value="fixed"    {{ old('increment_type', $auction->increment_type ?? '') == 'fixed'    ? 'selected' : '' }}>Fixed</option>
                                <option value="multiple" {{ old('increment_type', $auction->increment_type ?? '') == 'multiple' ? 'selected' : '' }}>Multiple</option>
                            </select>
                        </div>
                        @error('increment_type')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label>Process Declaration</label>
                        <textarea name="process_decleration" rows="4"
                            class="form-control @error('process_decleration') is-invalid @enderror"
                            placeholder="Enter process declaration details...">{{ old('process_decleration', $auction->process_decleration ?? '') }}</textarea>
                        @error('process_decleration')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Ending Period (days) <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-hourglass-end input-icon"></i>
                            <input type="number" name="ending_period"
                                class="form-control @error('ending_period') is-invalid @enderror"
                                value="{{ old('ending_period', $auction->ending_period ?? '') }}"
                                placeholder="e.g. 90">
                        </div>
                        @error('ending_period')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div id="ending-period-error" class="text-danger" style="font-size:13px; display:none;"></div>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <div class="form-group">
                        <label>Initial NPV Value <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-rupee-sign input-icon"></i>
                            <input type="number" step="0.01" name="initial_npv_value"
                                class="form-control @error('initial_npv_value') is-invalid @enderror"
                                value="{{ old('initial_npv_value', $auction->initial_npv_value ?? '') }}"
                                placeholder="e.g. 1000000.00">
                        </div>
                        @error('initial_npv_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <div class="npv-value-error text-danger" style="font-size:13px; display:none;"></div>
                    </div>
                </div>
            </div>

            {{-- Participants --}}
            <p class="form-section-title mt-3"><i class="fas fa-users mr-1"></i> Auction Participants <small class="text-muted font-weight-normal text-lowercase">(RA role users)</small></p>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        @if($participants->count())
                            @php
                                $selectedParticipants = old('participants',
                                    isset($auction)
                                        ? $auction->participants->pluck('user_id')->toArray()
                                        : []
                                );
                            @endphp
                            <select id="participants" name="participants[]" multiple>
                                @foreach($participants as $p)
                                    <option value="{{ $p->id }}" {{ in_array($p->id, $selectedParticipants) ? 'selected' : '' }}>
                                        {{ $p->name }} — {{ $p->email }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <div class="alert alert-warning mb-0" style="border-radius:10px;">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                No users with <strong>RA</strong> role found. Please assign the RA role to users first.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- NPV Categories --}}
            <p class="form-section-title mt-3"><i class="fas fa-tags mr-1"></i> NPV Categories <span class="text-danger">*</span></p>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        @php
                            $selectedCategories = old('npv_categories',
                                isset($auction)
                                    ? $auction->npvCategories->pluck('id')->toArray()
                                    : []
                            );
                        @endphp
                        <select id="npv_categories" name="npv_categories[]" multiple>
                            @foreach($npvCategories as $id => $name)
                                <option value="{{ $id }}" {{ in_array($id, $selectedCategories) ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('npv_categories')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            {{-- NPVP Configurations --}}
            <p class="form-section-title mt-3"><i class="fas fa-cogs mr-1"></i> NPVP Configurations</p>

            @php
                $npvpRows = old('npvp');
                if ($npvpRows === null) {
                    $npvpRows = isset($auction) ? $auction->npvpConfigurations->map(fn($n) => [
                        'period'           => $n->period,
                        'percentage_value' => $n->percentage_value,
                    ])->toArray() : [];
                }
            @endphp

            <div id="npvp-wrapper">
                @foreach($npvpRows as $i => $npvp)
                <div class="npvp-row" id="npvp-row-{{ $i }}">
                    <button type="button" class="btn btn-danger btn-sm btn-remove-npvp" onclick="removeNpvp({{ $i }})">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-2">
                                <label class="mb-1">Period (days)</label>
                                <input type="number" min="1" name="npvp[{{ $i }}][period]"
                                    class="form-control form-control-sm npvp-period @error('npvp.'.$i.'.period') is-invalid @enderror"
                                    value="{{ $npvp['period'] ?? '' }}" placeholder="e.g. 30">
                                @error('npvp.'.$i.'.period')
                                    <div class="npvp-error text-danger" style="font-size:12px;">{{ $message }}</div>
                                @else
                                    <div class="npvp-error text-danger" style="font-size:12px; display:none;"></div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group mb-2">
                                <label class="mb-1">Percentage Value <span class="text-danger">*</span></label>
                                <input type="number" step="0.0000001" name="npvp[{{ $i }}][percentage_value]"
                                    class="form-control form-control-sm npvp-pct @error('npvp.'.$i.'.percentage_value') is-invalid @enderror"
                                    value="{{ $npvp['percentage_value'] ?? '' }}" placeholder="e.g. 8.5000000">
                                @error('npvp.'.$i.'.percentage_value')
                                    <div class="npvp-pct-error text-danger" style="font-size:12px;">{{ $message }}</div>
                                @else
                                    <div class="npvp-pct-error text-danger" style="font-size:12px; display:none;"></div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div id="npvp-empty" class="text-center py-3 text-muted" style="display:none; font-size:13px;">
                <i class="fas fa-plus-circle mr-1"></i> No NPVP configurations added yet. Click below to add.
            </div>

            <button type="button" id="btn-add-npvp" class="btn btn-add-npvp mt-2">
                <i class="fas fa-plus mr-1"></i> Add NPVP Configuration
            </button>

        </div>

        <div class="card-footer bg-white border-top px-4 py-3 d-flex flex-wrap align-items-center" style="border-radius:0 0 16px 16px;">
            <button type="submit" class="btn btn-submit">
                <i class="fas fa-{{ isset($auction) ? 'sync-alt' : 'save' }} mr-2"></i>{{ isset($auction) ? 'Update Auction' : 'Create Auction' }}
            </button>
            <a href="{{ route('auctions.index') }}" class="btn btn-light btn-cancel ml-3">
                <i class="fas fa-times mr-1"></i> Cancel
            </a>
        </div>
    </form>
</div>

@endsection

@section('footer-script')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script src="{{ asset('app/auctions/form.js') }}"></script>
@endsection
