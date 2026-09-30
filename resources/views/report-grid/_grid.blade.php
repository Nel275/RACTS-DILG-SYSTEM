{{--
    Shared grid partial for both the Barangay Reports and SK Reports
    pages. Included by report-grid/barangay.blade.php and
    report-grid/sk.blade.php, which pass in the exact same set of
    variables from ReportGridController - this file has no idea which
    of the two pages it's rendering except through $councilType.

    Rows = barangays. Columns = that council's active report slots.
    Each cell has a date input and a color select. RACTS never uses
    AJAX/JS frameworks, so each barangay row is its own <form> - saving
    one row reloads the page and never touches any other barangay's
    unsaved edits. Since a <form> element can't legally wrap table rows,
    each row's form is declared once, empty, right before the table
    (holding only the hidden fields), and every visible input in that
    row points at it with HTML5's form="..." attribute instead of being
    nested inside it.
--}}
@php
    $isSk = $councilType === \App\Models\ReportRequirement::COUNCIL_SK;
    $saveRouteName = $isSk ? 'sk-reports.save' : 'barangay-reports.save';
    $indexRouteName = $isSk ? 'sk-reports.index' : 'barangay-reports.index';
    $councilLabel = $isSk ? 'SK' : 'Barangay';
    $periodLabel = \Illuminate\Support\Carbon::createFromFormat('Y-m-d', $periodMonth.'-01')->format('F Y');
@endphp

<div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
    <p class="text-muted mb-0">
        {{ $councilLabel }} Council report compliance grid. Record each barangay's submission date and
        grade (green/yellow/red) per report, per period.
    </p>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route($indexRouteName) }}" class="row g-2 align-items-end">
            <div class="col-sm-4 col-md-3">
                <label for="filter-month" class="form-label small mb-1">Period to Edit</label>
                <input type="month" name="month" id="filter-month" value="{{ $periodMonth }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-4 col-md-3">
                <label for="filter-start" class="form-label small mb-1">Ranking Range: Start Month</label>
                <input type="month" name="start" id="filter-start" value="{{ $rangeStart }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-4 col-md-3">
                <label for="filter-end" class="form-label small mb-1">Ranking Range: End Month</label>
                <input type="month" name="end" id="filter-end" value="{{ $rangeEnd }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-sm">Apply</button>
                <a href="{{ route($indexRouteName) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
        <div class="form-text mt-2 mb-0">
            <strong>Period to Edit</strong> is the month whose cells you're filling in below (currently
            <strong>{{ $periodLabel }}</strong> - yearly reports use its year). <strong>Ranking Range</strong>
            only changes the % column on the right; yearly reports always count toward it regardless of range.
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        @foreach ($barangays as $barangay)
            <form id="row-form-{{ $barangay->id }}" method="POST" action="{{ route($saveRouteName) }}">
                @csrf
                <input type="hidden" name="barangay_id" value="{{ $barangay->id }}">
                <input type="hidden" name="period_month" value="{{ $periodMonth }}">
                <input type="hidden" name="month" value="{{ $periodMonth }}">
                <input type="hidden" name="start" value="{{ $rangeStart }}">
                <input type="hidden" name="end" value="{{ $rangeEnd }}">
            </form>
        @endforeach

        <table class="table table-bordered table-sm align-middle mb-0 racts-grid-table">
            <thead class="table-light">
                <tr>
                    <th class="racts-grid-sticky-col">Barangay</th>
                    @foreach ($columns as $column)
                        <th class="text-center">
                            {{ $column->report_name }}
                            <span class="d-block badge bg-secondary-subtle text-secondary-emphasis fw-normal mt-1">
                                {{ $column->isYearly() ? 'Yearly' : 'Monthly' }}
                            </span>
                        </th>
                    @endforeach
                    <th class="text-center">%</th>
                    <th class="text-center">Save</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangays as $barangay)
                    <tr>
                        <td class="racts-grid-sticky-col fw-semibold">{{ $barangay->barangay_name }}</td>
                        @foreach ($columns as $column)
                            @php $record = $cells[$barangay->id][$column->id] ?? null; @endphp
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <input
                                        type="date"
                                        form="row-form-{{ $barangay->id }}"
                                        name="cells[{{ $column->id }}][date]"
                                        value="{{ $record?->submission_date?->format('Y-m-d') }}"
                                        class="form-control form-control-sm">
                                    <select
                                        form="row-form-{{ $barangay->id }}"
                                        name="cells[{{ $column->id }}][color]"
                                        class="form-select form-select-sm racts-color-select racts-color-{{ $record?->status_color ?? 'none' }}"
                                        onchange="this.className = 'form-select form-select-sm racts-color-select racts-color-' + (this.value || 'none');">
                                        <option value="" @selected(! $record?->status_color)>Not Graded</option>
                                        @foreach (\App\Models\ReportRecord::COLORS as $value => $label)
                                            <option value="{{ $value }}" @selected($record?->status_color === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                        @endforeach
                        <td class="text-center">
                            @php $percentage = $ranking[$barangay->id]['percentage'] ?? null; @endphp
                            @if (is_null($percentage))
                                <span class="text-muted">&mdash;</span>
                            @else
                                <span class="fw-semibold">{{ $percentage }}%</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="submit" form="row-form-{{ $barangay->id }}" class="btn btn-outline-primary btn-sm" title="Save {{ $barangay->barangay_name }}">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $columns->count() + 3 }}" class="p-0">
                            <div class="racts-empty-state">
                                <i class="bi bi-signpost-2"></i>
                                No active barangays found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if (auth()->user()->isAdmin())
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#slot-manager-{{ $councilType }}">
                <i class="bi bi-gear"></i> Manage {{ $councilLabel }} Report Slots
            </button>

            <div class="collapse mt-3" id="slot-manager-{{ $councilType }}">
                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Frequency</th>
                                <th style="width: 100px;">Order</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allSlots as $slot)
                                <tr>
                                    <td style="min-width: 220px;">
                                        <input type="text" form="slot-edit-{{ $slot->id }}" name="report_name" value="{{ $slot->report_name }}" class="form-control form-control-sm">
                                    </td>
                                    <td>{{ $slot->frequencyLabel() }}</td>
                                    <td>
                                        <input type="number" min="0" form="slot-edit-{{ $slot->id }}" name="display_order" value="{{ $slot->display_order }}" class="form-control form-control-sm">
                                    </td>
                                    <td>
                                        <span class="badge {{ $slot->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $slot->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <form id="slot-edit-{{ $slot->id }}" method="POST" action="{{ route('report-slots.update', $slot) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-primary btn-sm">Save</button>
                                        </form>
                                        <form method="POST" action="{{ route('report-slots.toggle', $slot) }}" class="d-inline"
                                              onsubmit="return confirm('{{ $slot->is_active ? 'Deactivate' : 'Reactivate' }} &quot;{{ $slot->report_name }}&quot;?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                {{ $slot->is_active ? 'Deactivate' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted">No report slots yet for {{ $councilLabel }}.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <h2 class="h6">Add New Report Slot</h2>
                <form method="POST" action="{{ route('report-slots.store') }}" class="row g-2 align-items-end">
                    @csrf
                    <input type="hidden" name="council_type" value="{{ $councilType }}">
                    <div class="col-md-5">
                        <label class="form-label small mb-1">Report Name</label>
                        <input type="text" name="report_name" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Frequency</label>
                        <select name="frequency" class="form-select form-select-sm" required>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Order</label>
                        <input type="number" min="0" name="display_order" value="0" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
