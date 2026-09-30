@extends('layouts.app')

@section('title', 'Log Entry Details')

@section('content')
    <a href="{{ route('barangay-log-details.index') }}" class="d-inline-block mb-3">&larr; Back to Report Filing</a>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h1 class="h5 mb-1">{{ $entry->item_description }}</h1>
                    <p class="text-muted mb-0">{{ $entry->barangay->barangay_name }}</p>
                </div>
                <span class="badge {{ $entry->badgeClass() }} fs-6">{{ $entry->statusLabel() }}</span>
            </div>

            <dl class="row mb-0">
                <dt class="col-sm-3">Barangay</dt>
                <dd class="col-sm-9">{{ $entry->barangay->barangay_name }}</dd>

                <dt class="col-sm-3">Report</dt>
                <dd class="col-sm-9">{{ $entry->item_description }}</dd>

                <dt class="col-sm-3">Deadline</dt>
                <dd class="col-sm-9">{{ $entry->deadline_date->format('F j, Y') }}</dd>

                <dt class="col-sm-3">Date Submitted</dt>
                <dd class="col-sm-9">{{ $entry->date_submitted?->format('F j, Y g:i A') ?? '—' }}</dd>

                <dt class="col-sm-3">Last Processed</dt>
                <dd class="col-sm-9">
                    {{ $entry->date_process?->format('F j, Y') ?? '—' }}
                    @if ($entry->processed_by)
                        by {{ $entry->processed_by }}
                    @endif
                </dd>

                <dt class="col-sm-3">Report File</dt>
                <dd class="col-sm-9">
                    @if ($entry->hasReportFile())
                        {{ $entry->report_original_name ?? 'Uploaded file' }}
                        @if ($entry->isFiled())
                            <span class="badge bg-dark-subtle text-dark-emphasis ms-1">Filed to {{ $entry->barangay->barangay_name }}'s folder</span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis ms-1">Not yet filed - use Download below</span>
                        @endif
                    @else
                        <span class="text-muted">No file attached yet.</span>
                    @endif
                </dd>
            </dl>

            <div class="d-flex gap-2 mt-3">
                @if ($entry->hasReportFile() && ! $entry->isFiled())
                    <form action="{{ route('barangay-log-details.download', $entry) }}" method="POST"
                          onsubmit="return confirm('File this report into {{ $entry->barangay->barangay_name }}\'s folder?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-download"></i> Download
                        </button>
                    </form>
                @endif

                @if ($entry->status !== \App\Models\BarangayLogDetail::STATUS_CLOSED)
                    <form action="{{ route('barangay-log-details.close', $entry) }}" method="POST"
                          onsubmit="return confirm('Close this log entry?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-x-circle"></i> Close
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if ($entry->status !== \App\Models\BarangayLogDetail::STATUS_CLOSED)
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6 mb-3">{{ $entry->hasReportFile() ? 'Update Submission' : 'Record Submission' }}</h2>
                <form method="POST" action="{{ route('barangay-log-details.submit', $entry) }}" class="row g-2 align-items-end" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="col-md-5">
                        <label class="form-label small mb-1">Date Submitted</label>
                        <input type="datetime-local" name="date_submitted" value="{{ now()->format('Y-m-d\TH:i') }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Report File (optional)</label>
                        <input type="file" name="report_file" class="form-control form-control-sm" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100">Mark Submitted</button>
                    </div>
                </form>
                <p class="form-text mt-2 mb-0">
                    Whether this counts as "Submitted" or "Submitted Late" is decided automatically by
                    comparing the date above to the deadline ({{ $entry->deadline_date->format('M j, Y') }}).
                    Attaching a file here is optional - it can also be added later by submitting this form
                    again. PDF, Word, or image files up to 10 MB.
                </p>
            </div>
        </div>
    @endif
@endsection
