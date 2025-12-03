@extends('layout')

@section('content')
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Edit Fine</h1>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.fines.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Validation Errors:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.fines.update', $fine->id) }}" method="POST" novalidate>
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="organisation" class="form-label">Organisation Name *</label>
                        <input type="text" class="form-control @error('organisation') is-invalid @enderror"
                               id="organisation" name="organisation"
                               value="{{ old('organisation', $fine->organisation) }}"
                               required minlength="3">
                        @error('organisation')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="regulator" class="form-label">Regulator</label>
                        <input type="text" class="form-control @error('regulator') is-invalid @enderror"
                               id="regulator" name="regulator"
                               value="{{ old('regulator', $fine->regulator) }}">
                        @error('regulator')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fine_amount" class="form-label">Fine Amount *</label>
                        <input type="number" step="0.01" class="form-control @error('fine_amount') is-invalid @enderror"
                               id="fine_amount" name="fine_amount"
                               value="{{ old('fine_amount', $fine->fine_amount) }}"
                               required min="0">
                        @error('fine_amount')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-2 mb-3">
                        <label for="currency" class="form-label">Currency *</label>
                        <select class="form-select @error('currency') is-invalid @enderror"
                                id="currency" name="currency" required>
                            @foreach(['EUR', 'USD', 'GBP', 'AUD', 'CAD'] as $curr)
                                <option value="{{ $curr }}"
                                    {{ old('currency', $fine->currency) == $curr ? 'selected' : '' }}>
                                    {{ $curr }}
                                </option>
                            @endforeach
                        </select>
                        @error('currency')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="fine_date" class="form-label">Fine Date *</label>
                        <input type="date" class="form-control @error('fine_date') is-invalid @enderror"
                               id="fine_date" name="fine_date"
                               value="{{ old('fine_date', $fine->fine_date?->format('Y-m-d')) }}"
                               required>
                        @error('fine_date')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sector" class="form-label">Sector</label>
                        <input type="text" class="form-control @error('sector') is-invalid @enderror"
                               id="sector" name="sector"
                               value="{{ old('sector', $fine->sector) }}">
                        @error('sector')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="region" class="form-label">Region</label>
                        <input type="text" class="form-control @error('region') is-invalid @enderror"
                               id="region" name="region"
                               value="{{ old('region', $fine->region) }}">
                        @error('region')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="law" class="form-label">Law/Regulation</label>
                    <input type="text" class="form-control @error('law') is-invalid @enderror"
                           id="law" name="law"
                           value="{{ old('law', $fine->law) }}">
                    @error('law')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="articles_breached" class="form-label">Articles Breached</label>
                    <input type="text" class="form-control @error('articles_breached') is-invalid @enderror"
                           id="articles_breached" name="articles_breached"
                           value="{{ old('articles_breached', $fine->articles_breached) }}">
                    @error('articles_breached')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="violation_type" class="form-label">Violation Type</label>
                    <input type="text" class="form-control @error('violation_type') is-invalid @enderror"
                           id="violation_type" name="violation_type"
                           value="{{ old('violation_type', $fine->violation_type) }}">
                    @error('violation_type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="summary" class="form-label">Summary *</label>
                    <textarea class="form-control @error('summary') is-invalid @enderror"
                              id="summary" name="summary" rows="5"
                              required minlength="10" maxlength="5000">{{ old('summary', $fine->summary) }}</textarea>
                    @error('summary')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="badges" class="form-label">Badges (comma-separated)</label>
                        <input type="text" class="form-control @error('badges') is-invalid @enderror"
                               id="badges" name="badges"
                               value="{{ old('badges', $fine->badges) }}"
                               placeholder="e.g., ai,gdpr,privacy">
                        @error('badges')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="link_to_case" class="form-label">Link to Case</label>
                        <input type="url" class="form-control @error('link_to_case') is-invalid @enderror"
                               id="link_to_case" name="link_to_case"
                               value="{{ old('link_to_case', $fine->link_to_case) }}">
                        @error('link_to_case')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Update Fine</button>
                    <a href="{{ route('admin.fines.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
