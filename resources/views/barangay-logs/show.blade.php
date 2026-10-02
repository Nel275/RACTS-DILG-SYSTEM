@extends('layouts.app')

@section('title', $barangayLog->item_description)

@section('content')
    {{--
        Follow-up round: this report catalog item could belong to
        either council now (see BarangayLog::council_type), so the back
        link is resolved from it rather than always pointing at the
        Barangay list.
    --}}
    @php
        $isSk = $barangayLog->council_type === \App\Models\ReportRequirement::COUNCIL_SK;
    @endphp
    <a href="{{ route($isSk ? 'sk-log-details.index' : 'barangay-log-details.index') }}" class="d-inline-block mb-3">&larr; Back to Report Filing</a>

    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $barangayLog->item_description }}</h1>
            <p class="text-muted mb-0">
                Deadline: {{ $barangayLog->deadline_date->format('F j, Y') }}
                &mdash; {{ $entries->count() }} {{ $entries->count() === 1 ? 'barangay' : 'barangays' }} logged with this report.
            </p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive racts-table-no-clip">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Barangay</th>
                        <th>Deadline</th>
                        <th>Date Submitted</th>
                        <th>Last Processed</th>
                        <th>Report File</th>
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
                            <td>{{ $entry->deadline_date->format('M j, Y') }}</td>
                            <td>{{ $entry->date_submitted?->format('M j, Y g:i A') ?? '—' }}</td>
                            <td>
                                {{ $entry->date_process?->format('M j, Y') ?? '—' }}
                                @if ($entry->processed_by)
                                    <span class="text-muted">by {{ $entry->processed_by }}</span>
                                @endif
                            </td>
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
                                    No barangay has been logged with this report yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
