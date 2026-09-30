{{-- Expects $data (['records' => paginator, 'summary' => collection of
     {period, entry_count}]) and $councilLabel ('Barangay' or 'SK').
     $formatPeriod is defined once in records/index.blade.php and shared
     into this partial automatically by @include. --}}

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <h2 class="h6 mb-2">Periods recorded so far &mdash; {{ $councilLabel }} Council</h2>
        @if ($data['summary']->isEmpty())
            <p class="text-muted small mb-0">Nothing recorded for this council yet.</p>
        @else
            <div class="d-flex flex-wrap gap-2">
                @foreach ($data['summary'] as $row)
                    <span class="badge bg-secondary-subtle text-secondary-emphasis border">
                        {{ $formatPeriod($row->period) }} &mdash; {{ $row->entry_count }} {{ Str::plural('entry', $row->entry_count) }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>Barangay</th>
                        <th>Report</th>
                        <th>Period</th>
                        <th>Grade</th>
                        <th>Timeliness</th>
                        <th>Edited By</th>
                        <th>Edited At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data['records'] as $record)
                        @php
                            // Follow-up Round 5: purely informational, read
                            // via ReportDeadlineService (the single place
                            // the DILG 1st-5th rule lives) - doesn't touch
                            // status_color or anything else about this row.
                            $timeliness = app(\App\Services\ReportDeadlineService::class)->statusFor(
                                $record->period,
                                $record->reportRequirement->frequency,
                                $record->submission_date
                            );
                            $timelinessClass = match ($timeliness) {
                                'on_time' => 'bg-success-subtle text-success-emphasis',
                                'late', 'overdue' => 'bg-danger-subtle text-danger-emphasis',
                                'pending' => 'bg-secondary-subtle text-secondary-emphasis',
                                default => 'bg-light text-muted',
                            };
                        @endphp
                        <tr>
                            <td>{{ $record->barangay->barangay_name }}</td>
                            <td>{{ $record->reportRequirement->report_name }}</td>
                            <td>{{ $formatPeriod($record->period) }}</td>
                            <td>
                                @if ($record->status_color)
                                    <span class="badge racts-badge-{{ $record->status_color }}">{{ $record->colorLabel() }}</span>
                                @else
                                    <span class="badge bg-secondary">Not Graded</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $timelinessClass }}">
                                    {{ app(\App\Services\ReportDeadlineService::class)->statusLabel($timeliness) }}
                                </span>
                            </td>
                            <td>{{ $record->editedBy?->name ?? '—' }}</td>
                            <td>{{ $record->edited_at?->format('M j, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <div class="racts-empty-state py-3">
                                    <i class="bi bi-inbox"></i>
                                    No records match these filters.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data['records']->hasPages())
            {{-- Hand-built rather than calling ->links(): Laravel's default
                 pagination view is styled for Tailwind, which this
                 project doesn't use anywhere - this keeps it plain
                 Bootstrap 5 with no extra setup required elsewhere. --}}
            <nav class="mt-3" aria-label="Records pagination">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item {{ $data['records']->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $data['records']->previousPageUrl() ?? '#' }}">Previous</a>
                    </li>
                    <li class="page-item disabled">
                        <span class="page-link">Page {{ $data['records']->currentPage() }} of {{ $data['records']->lastPage() }}</span>
                    </li>
                    <li class="page-item {{ $data['records']->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $data['records']->nextPageUrl() ?? '#' }}">Next</a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>
</div>
