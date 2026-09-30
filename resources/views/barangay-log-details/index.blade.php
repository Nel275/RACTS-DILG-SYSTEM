@extends('layouts.app')

@section('title', 'Report Filing')

@section('content')
    @include('report-grid._reports-tabs', ['active' => 'log-details'])

    <p class="text-muted mb-3">
        Track and file individual report documents per barangay - separate from the Compliance Grid's monthly
        grading. Each row is one barangay's entry for one report; pick a specific report below to narrow the
        list to just that one. The report-type catalog itself is managed below, under "Manage Reports."
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
            <form method="GET" action="{{ route('barangay-log-details.index') }}" class="row g-2 align-items-end">
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
                    <a href="{{ route('barangay-log-details.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Barangay</th>
                        <th>Report</th>
                        <th>Date</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $entry)
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('barangay-log-details.show', $entry) }}" class="text-decoration-none">
                                    {{ $entry->barangay->barangay_name }}
                                </a>
                            </td>
                            <td>{{ $entry->item_description }}</td>
                            <td>{{ $entry->date_submitted?->format('M j, Y') ?? '—' }}</td>
                            <td>{{ $entry->deadline_date->format('M j, Y') }}</td>
                            <td>
                                <span class="badge {{ $entry->badgeClass() }}">{{ $entry->statusLabel() }}</span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
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
                                                        <i class="bi bi-download me-1"></i> Download
                                                    </button>
                                                </form>
                                            @else
                                                <span class="dropdown-item disabled" title="No report file uploaded yet - open this entry to attach one.">
                                                    <i class="bi bi-download me-1"></i> Download
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
                            <td colspan="6" class="p-0">
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
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#add-log-detail">
                    <i class="bi bi-plus-lg"></i> Add Log Entry
                </button>

                <div class="collapse mt-3" id="add-log-detail">
                    @if ($activeLogItems->isEmpty())
                        <p class="text-muted mb-0">
                            No active items in the Barangay Logs catalog yet - add one below, under "Manage Reports".
                        </p>
                    @else
                        <form method="POST" action="{{ route('barangay-log-details.store') }}" class="row g-2 align-items-end">
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
                                <select name="barangay_log_id" class="form-select form-select-sm" required>
                                    <option value="">Select report...</option>
                                    @foreach ($activeLogItems as $option)
                                        <option value="{{ $option->id }}" data-deadline="{{ $option->deadline_date->format('Y-m-d') }}">
                                            {{ $option->item_description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small mb-1">Deadline</label>
                                <input type="date" name="deadline_date" id="add-log-detail-deadline" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Follow-up round: the Barangay Logs catalog used to be its own
             top-level page. It's now managed right here instead, mirroring
             the "Manage Report Slots" panel on the Barangay/SK Reports
             grids - one less page to navigate to for something that's
             only ever edited alongside these entries. $reportOptions is
             the full, unfiltered catalog list already built by
             BarangayLogDetailController::index() for the filter dropdown
             above, so no extra controller work was needed for this. --}}
        <div class="card shadow-sm mt-3">
            <div class="card-body">
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#report-catalog-manager">
                    <i class="bi bi-gear"></i> Manage Reports
                </button>

                <div class="collapse mt-3" id="report-catalog-manager">
                    <div class="table-responsive mb-3">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Report Name</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reportOptions as $item)
                                    <tr>
                                        <td style="min-width: 220px;">
                                            <input type="text" form="report-item-edit-{{ $item->id }}" name="item_description" value="{{ $item->item_description }}" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <input type="date" form="report-item-edit-{{ $item->id }}" name="deadline_date" value="{{ $item->deadline_date->format('Y-m-d') }}" class="form-control form-control-sm">
                                        </td>
                                        <td>
                                            <span class="badge {{ $item->isActive() ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $item->statusLabel() }}
                                            </span>
                                            <input type="hidden" form="report-item-edit-{{ $item->id }}" name="status" value="{{ $item->status }}">
                                        </td>
                                        <td class="text-end">
                                            <form id="report-item-edit-{{ $item->id }}" method="POST" action="{{ route('barangay-logs.update', $item) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Save</button>
                                            </form>
                                            <form method="POST" action="{{ route('barangay-logs.toggle', $item) }}" class="d-inline"
                                                  onsubmit="return confirm('{{ $item->isActive() ? 'Deactivate' : 'Reactivate' }} &quot;{{ $item->item_description }}&quot;?');">
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
                                        <td colspan="4" class="text-muted">No report items yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h2 class="h6">Add New Report Item</h2>
                    <form method="POST" action="{{ route('barangay-logs.store') }}" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Report Name</label>
                            <input type="text" name="item_description" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Deadline</label>
                            <input type="date" name="deadline_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        // Small convenience only: pre-fills the deadline field from the
        // selected report's catalog deadline. The field stays a plain
        // editable date input either way - this never blocks a manual
        // override.
        document.addEventListener('DOMContentLoaded', function () {
            const reportSelect = document.querySelector('select[name="barangay_log_id"]');
            const deadlineInput = document.getElementById('add-log-detail-deadline');
            if (!reportSelect || !deadlineInput) return;

            reportSelect.addEventListener('change', function () {
                const selected = reportSelect.options[reportSelect.selectedIndex];
                const deadline = selected ? selected.getAttribute('data-deadline') : null;
                if (deadline) {
                    deadlineInput.value = deadline;
                }
            });
        });
    </script>
@endsection
