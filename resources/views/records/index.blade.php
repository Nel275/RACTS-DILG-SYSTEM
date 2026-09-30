@extends('layouts.app')

@section('title', 'Records')

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('records.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-5 col-md-4">
                    <label for="filter-period" class="form-label small mb-1">Period</label>
                    <select name="period" id="filter-period" class="form-select form-select-sm">
                        <option value="">All periods</option>
                        @php
                            $formatPeriod = function (string $p) {
                                return preg_match('/^\d{4}-\d{2}$/', $p)
                                    ? \Carbon\Carbon::createFromFormat('Y-m-d', $p.'-01')->format('F Y')
                                    : $p;
                            };
                        @endphp
                        @foreach ($periods as $p)
                            <option value="{{ $p }}" @selected($selectedPeriod === $p)>{{ $formatPeriod($p) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5 col-md-4">
                    <label for="filter-barangay" class="form-label small mb-1">Barangay</label>
                    <select name="barangay_id" id="filter-barangay" class="form-select form-select-sm">
                        <option value="">All barangays</option>
                        @foreach ($barangays as $b)
                            <option value="{{ $b->id }}" @selected($selectedBarangayId === $b->id)>{{ $b->barangay_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-12 col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary btn-sm">Apply</button>
                    <a href="{{ route('records.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
            <div class="form-text mt-2 mb-0">
                A read-only list of everything recorded on the Barangay/SK Reports grids, across every
                month - use this to check which periods you've already filled in before you go looking
                on the grid. To add, edit, or grade an entry, use the Barangay Reports / SK Reports pages.
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="records-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="barangay-records-tab-btn" data-bs-toggle="tab" data-bs-target="#barangay-records-tab" type="button" role="tab" aria-controls="barangay-records-tab" aria-selected="true">
                Barangay Council
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sk-records-tab-btn" data-bs-toggle="tab" data-bs-target="#sk-records-tab" type="button" role="tab" aria-controls="sk-records-tab" aria-selected="false">
                SK Council
            </button>
        </li>
    </ul>

    <div class="tab-content" id="records-tab-content">
        <div class="tab-pane fade show active" id="barangay-records-tab" role="tabpanel" aria-labelledby="barangay-records-tab-btn">
            @include('records._council-table', ['data' => $barangay, 'councilLabel' => 'Barangay'])
        </div>
        <div class="tab-pane fade" id="sk-records-tab" role="tabpanel" aria-labelledby="sk-records-tab-btn">
            @include('records._council-table', ['data' => $sk, 'councilLabel' => 'SK'])
        </div>
    </div>
@endsection
