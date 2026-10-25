{{--
    Shared Report Filing list - included by both barangay-log-details/
    index.blade.php (Barangay) and sk-log-details/index.blade.php (SK),
    which pass in the exact same set of variables from
    BarangayLogDetailController@listFor, mirroring how report-grid/
    _grid.blade.php is shared by the Compliance Grid's two pages.

    $councilType picks the three route names that actually differ
    between the two councils - the filter form's action, the Reset
    link, and the Add Log Entry form's action. Everything else here
    (a report's link to barangay-logs.show, View File, Attach,
    Close, and the Manage Reports panel's Save/Deactivate forms) stays
    the same route regardless of council, since those all act on a
    specific entry or catalog item by its own id - see
    BarangayLogDetailController's and BarangayLogController's doc
    comments on why those aren't duplicated per council.
--}}
@php
    $isSk = $councilType === \App\Models\ReportRequirement::COUNCIL_SK;
    $filingIndexRoute = $isSk ? 'sk-log-details.index' : 'barangay-log-details.index';
    $filingStoreRoute = $isSk ? 'sk-log-details.store' : 'barangay-log-details.store';
    $councilLabel = $isSk ? 'SK' : 'Barangay';
@endphp

<p class="text-muted mb-3">
    Track and file individual {{ $councilLabel }} Council report documents per barangay - separate from the
    Compliance Grid's monthly grading. Each row is one barangay's entry for one report; pick a specific report
    below to narrow the list to just that one. The report-type catalog itself is managed below, under
    "Manage Reports."
</p>

<div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Total Entries</div>
                <div class="h3 mb-0">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Submitted</div>
                <div class="h3 mb-0 text-success">{{ $stats['submitted'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Pending</div>
                <div class="h3 mb-0 text-secondary">{{ $stats['pending'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase">Overdue</div>
                <div class="h3 mb-0 text-danger">{{ $stats['overdue'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route($filingIndexRoute) }}" class="row g-2 align-items-end">
            <div class="col-sm-6 col-md-2">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    @foreach (\App\Models\BarangayLogDetail::STATUSES as $value => $label)
                        <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-3">
                <label class="form-label small mb-1">Report</label>
                <select name="report" class="form-select form-select-sm">
                    <option value="">All Reports</option>
                    @foreach ($reportOptions as $option)
                        <option value="{{ $option->id }}" @selected($reportId === $option->id)>{{ $option->item_description }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label small mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control form-control-sm">
            </div>
            <div class="col-sm-6 col-md-2">
                <label class="form-label small mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Search Barangay</label>
                <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Barangay name...">
            </div>
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary btn-sm">Apply</button>
                <a href="{{ route($filingIndexRoute) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive racts-table-no-clip">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Processed By</th>
                    <th>Report</th>
                    <th>Date</th>
                    <th>Due</th>
                    <th>Report File</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    <tr>
                        <td>{{ $entry->processed_by ?? '—' }}</td>
                        <td class="fw-semibold">
                            <a href="{{ route('barangay-logs.show', $entry->barangay_log_id) }}" class="text-decoration-none">
                                {{ $entry->item_description }}
                            </a>
                        </td>
                        <td>{{ $entry->date_submitted?->format('M j, Y') ?? '—' }}</td>
                        <td>{{ $entry->deadline_date->format('M j, Y') }}</td>
                        <td>
                            @if ($entry->hasReportFile())
                                <a href="{{ route('barangay-log-details.view-file', $entry) }}" target="_blank" rel="noopener">
                                    {{ $entry->report_original_name ?? 'Uploaded file' }}
                                </a>
                                @if ($entry->isFiled())
                                    <span class="badge bg-dark-subtle text-dark-emphasis ms-1">Filed</span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning-emphasis ms-1">Not filed</span>
                                @endif
                            @else
                                <span class="text-muted">No file attached yet.</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $entry->badgeClass() }}">{{ $entry->statusLabel() }}</span>
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        @if ($entry->hasReportFile())
                                            <a class="dropdown-item" href="{{ route('barangay-log-details.view-file', $entry) }}" target="_blank" rel="noopener">
                                                <i class="bi bi-eye me-1"></i> View File
                                            </a>
                                        @else
                                            <span class="dropdown-item disabled" title="No report file uploaded yet - open this entry to attach one.">
                                                <i class="bi bi-eye me-1"></i> View File
                                            </span>
                                        @endif
                                    </li>
                                    <li>
                                        @if ($entry->isFiled())
                                            <span class="dropdown-item disabled">
                                                <i class="bi bi-folder-check me-1"></i> Filed
                                            </span>
                                        @elseif ($entry->hasReportFile())
                                            <form action="{{ route('barangay-log-details.download', $entry) }}" method="POST"
                                                  onsubmit="return confirm('File this report into {{ $entry->barangay->barangay_name }}\'s folder?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-paperclip me-1"></i> Attach
                                                </button>
                                            </form>
                                        @else
                                            <span class="dropdown-item disabled" title="No report file uploaded yet - open this entry to attach one.">
                                                <i class="bi bi-paperclip me-1"></i> Attach
                                            </span>
                                        @endif
                                    </li>
                                    <li>
                                        @if ($entry->status === \App\Models\BarangayLogDetail::STATUS_CLOSED)
                                            <span class="dropdown-item disabled">
                                                <i class="bi bi-lock-fill me-1"></i> Closed
                                            </span>
                                        @else
                                            <form action="{{ route('barangay-log-details.close', $entry) }}" method="POST"
                                                  onsubmit="return confirm('Close this log entry?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="bi bi-x-circle me-1"></i> Close
                                                </button>
                                            </form>
                                        @endif
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0">
                            <div class="racts-empty-state">
                                <i class="bi bi-journal-check"></i>
                                No log entries found.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $entries->links() }}
</div>

@if (auth()->user()->isAdmin())
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#add-log-detail-{{ $councilType }}">
                <i class="bi bi-plus-lg"></i> Add Log Entry
            </button>

            <div class="collapse mt-3" id="add-log-detail-{{ $councilType }}">
                <form method="POST" action="{{ route($filingStoreRoute) }}" class="row g-2 align-items-end">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Barangay</label>
                        <select name="barangay_id" class="form-select form-select-sm" required>
                            <option value="">Select barangay...</option>
                            @foreach ($barangayOptions as $option)
                                <option value="{{ $option->id }}">{{ $option->barangay_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Report</label>
                        <input type="text" name="item_description" id="add-log-detail-report-{{ $councilType }}" class="form-control form-control-sm"
                               list="add-log-detail-report-options-{{ $councilType }}" required
                               value="{{ old('item_description') }}">
                        <datalist id="add-log-detail-report-options-{{ $councilType }}">
                            @foreach ($activeLogItems as $option)
                                <option value="{{ $option->item_description }}" data-deadline="{{ $option->deadline_date?->format('Y-m-d') }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Deadline</label>
                        <input type="date" name="deadline_date" id="add-log-detail-deadline-{{ $councilType }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                    </div>
                    </form>
            </div>
        </div>
    </div>

    {{-- Official report requirements (admin only). Monthly/annual ones
         are turned into every barangay's My Reports entries automatically
         by ReportEntryGenerator (on save and daily via the scheduler);
         one-time ones work as before, through Add Log Entry above.
         Editing a requirement never rewrites entries already generated. --}}
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#report-catalog-manager-{{ $councilType }}">
                <i class="bi bi-gear"></i> Manage Report Requirements
            </button>

            <div class="collapse mt-3 {{ $errors->hasAny(['frequency', 'deadline_day', 'deadline_month', 'effective_from', 'council_type']) ? 'show' : '' }}" id="report-catalog-manager-{{ $councilType }}">
                <h2 class="h6">Add Requirement</h2>
                <form method="POST" action="{{ route('barangay-logs.store') }}" class="row g-2 align-items-end mb-4">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Report Name</label>
                        <input type="text" name="item_description" maxlength="255" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1">For</label>
                        <select name="council_type" class="form-select form-select-sm">
                            <option value="{{ $councilType }}">{{ $councilLabel }} only</option>
                            <option value="both">Barangay and SK</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1">Frequency</label>
                        <select name="frequency" class="form-select form-select-sm">
                            @foreach (\App\Models\BarangayLog::FREQUENCIES as $value => $label)
                                <option value="{{ $value }}" @selected($value === 'monthly')>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-4 col-md-1">
                        <label class="form-label small mb-1" title="Day of the following month (monthly) or of the deadline month (annual)">Due Day</label>
                        <input type="number" name="deadline_day" min="1" max="28" value="5" class="form-control form-control-sm">
                    </div>
                    <div class="col-4 col-md-1">
                        <label class="form-label small mb-1" title="Annual only: month of the following year">Due Month</label>
                        <input type="number" name="deadline_month" min="1" max="12" value="1" class="form-control form-control-sm">
                    </div>
                    <div class="col-4 col-md-2">
                        <label class="form-label small mb-1">One-time Deadline</label>
                        <input type="date" name="deadline_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small mb-1">Effective From</label>
                        <input type="date" name="effective_from" value="{{ now()->toDateString() }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-2">
                        <div class="form-check">
                            <input type="hidden" name="is_mandatory" value="0">
                            <input type="checkbox" name="is_mandatory" value="1" class="form-check-input" id="new-mandatory-{{ $councilType }}" checked>
                            <label class="form-check-label small" for="new-mandatory-{{ $councilType }}">Mandatory</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Add Requirement</button>
                    </div>
                    <div class="col-12 form-text">
                        Monthly: due on "Due Day" of the following month. Annual: due on "Due Month"/"Due Day" of the
                        following year. One-time: due on the date given. Changes apply to new periods only.
                    </div>
                </form>

                <div class="table-responsive mb-3">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Frequency</th>
                                <th>Due Day / Month / Date</th>
                                <th>Effective From</th>
                                <th>Mandatory</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reportOptions as $item)
                                @php($form = 'report-item-edit-'.$item->id)
                                <tr>
                                    <td style="min-width: 200px;">
                                        <input type="text" form="{{ $form }}" name="item_description" value="{{ $item->item_description }}" class="form-control form-control-sm" aria-label="Report name">
                                    </td>
                                    <td>
                                        <select form="{{ $form }}" name="frequency" class="form-select form-select-sm" aria-label="Frequency">
                                            @foreach (\App\Models\BarangayLog::FREQUENCIES as $value => $label)
                                                <option value="{{ $value }}" @selected($item->frequency === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="text-nowrap">
                                        <input type="number" form="{{ $form }}" name="deadline_day" min="1" max="28" value="{{ $item->deadline_day ?? 5 }}" class="form-control form-control-sm d-inline-block" style="width: 4.5rem;" aria-label="Due day">
                                        <input type="number" form="{{ $form }}" name="deadline_month" min="1" max="12" value="{{ $item->deadline_month ?? 1 }}" class="form-control form-control-sm d-inline-block" style="width: 4.5rem;" aria-label="Due month">
                                        <input type="date" form="{{ $form }}" name="deadline_date" value="{{ $item->deadline_date?->format('Y-m-d') }}" class="form-control form-control-sm d-inline-block" style="width: 9.5rem;" aria-label="One-time deadline">
                                        <div class="small text-muted">{{ $item->deadlineRuleLabel() }}</div>
                                    </td>
                                    <td>
                                        <input type="date" form="{{ $form }}" name="effective_from" value="{{ $item->effective_from?->format('Y-m-d') }}" class="form-control form-control-sm" aria-label="Effective from">
                                    </td>
                                    <td class="text-center">
                                        <input type="hidden" form="{{ $form }}" name="is_mandatory" value="0">
                                        <input type="checkbox" form="{{ $form }}" name="is_mandatory" value="1" class="form-check-input" @checked($item->is_mandatory) aria-label="Mandatory">
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->isActive() ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $item->statusLabel() }}
                                        </span>
                                        <input type="hidden" form="{{ $form }}" name="status" value="{{ $item->status }}">
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <form id="{{ $form }}" method="POST" action="{{ route('barangay-logs.update', $item) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-outline-primary btn-sm">Save</button>
                                        </form>
                                        <form method="POST" action="{{ route('barangay-logs.toggle', $item) }}" class="d-inline"
                                              onsubmit="return confirm({{ \Illuminate\Support\Js::from(($item->isActive() ? 'Deactivate' : 'Reactivate').' "'.$item->item_description.'"?') }});">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-secondary btn-sm">
                                                {{ $item->isActive() ? 'Deactivate' : 'Reactivate' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-muted">No report requirements yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <p class="text-muted small mb-0">
                    Click a report's name in the list above to see every barangay's entry and progress for it.
                </p>
            </div>
        </div>
    </div>
@endif

@section('scripts')
    <script>
        // Small convenience only: pre-fills the deadline field when what
        // you've typed into Report matches an existing catalog item
        // exactly (same as before, just driven by the <datalist> options
        // now that Report is a free-text field instead of a <select>).
        // Typing something new that doesn't match just leaves the
        // deadline for you to fill in - that's what sets the new
        // report's catalog deadline when it's created on submit.
        //
        // Follow-up round: now takes the element ids as arguments
        // instead of hardcoding them, since the Barangay and SK Report
        // Filing pages each render this same partial with their own
        // "-barangay" / "-sk" id suffix (see $councilType above) -
        // never both on one page load, but the ids still need to be
        // unique within a page rather than hardcoded.
        document.addEventListener('DOMContentLoaded', function () {
            function wireReportAutofill(inputId, optionsId, deadlineId) {
                const reportInput = document.getElementById(inputId);
                const reportOptions = document.getElementById(optionsId);
                const deadlineInput = document.getElementById(deadlineId);
                if (!reportInput || !reportOptions || !deadlineInput) return;

                reportInput.addEventListener('input', function () {
                    const match = Array.from(reportOptions.options).find(function (option) {
                        return option.value === reportInput.value;
                    });
                    const deadline = match ? match.getAttribute('data-deadline') : null;
                    if (deadline) {
                        deadlineInput.value = deadline;
                    }
                });
            }

            wireReportAutofill(
                'add-log-detail-report-{{ $councilType }}',
                'add-log-detail-report-options-{{ $councilType }}',
                'add-log-detail-deadline-{{ $councilType }}'
            );
        });
    </script>
@endsection
