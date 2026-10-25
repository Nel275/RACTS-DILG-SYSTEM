@extends('layouts.app')

@section('title', 'Stamping Details')

@section('content')
    @php($current = $entry->currentStamp())
    <a href="{{ route('digital-stamping.index') }}" class="d-inline-block mb-3">&larr; Back to Digital Stamping</a>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h1 class="h5 mb-1">{{ $entry->item_description }}</h1>
                    <p class="text-muted mb-0">{{ $entry->referenceNumber() }} &middot; {{ $entry->barangay->barangay_name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge {{ $entry->badgeClass() }} fs-6">{{ $entry->statusLabel() }}</span>
                    <span class="badge {{ $current ? 'racts-badge-green' : 'bg-secondary-subtle text-secondary-emphasis' }} fs-6">{{ $entry->stampingStatusLabel() }}</span>
                </div>
            </div>

            <dl class="row mb-0">
                <dt class="col-sm-3">Submitted</dt>
                <dd class="col-sm-9">{{ $entry->date_submitted?->format('F j, Y g:i A') ?? '—' }}</dd>

                <dt class="col-sm-3">Original File</dt>
                <dd class="col-sm-9">
                    @if ($entry->hasReportFile())
                        <a href="{{ route('barangay-log-details.view-file', $entry) }}" target="_blank" rel="noopener">{{ $entry->report_original_name }}</a>
                    @else
                        <span class="text-muted">No file attached.</span>
                    @endif
                </dd>

                @if ($current)
                    <dt class="col-sm-3">Stamped By</dt>
                    <dd class="col-sm-9">{{ $current->stamped_by_name }} on {{ $current->created_at->format('F j, Y g:i A') }}</dd>
                    <dt class="col-sm-3">Verification Code</dt>
                    <dd class="col-sm-9"><code>{{ $current->verification_code }}</code></dd>
                @endif
            </dl>

            <div class="d-flex gap-2 mt-3">
                @if ($current)
                    <a href="{{ route('digital-stamping.download', $current) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-download"></i> Download Stamped Report
                    </a>
                @elseif ($entry->isSubmittedToDilg() && $entry->hasReportFile() && $entry->isStampable())
                    @include('digital-stamping._stamp-button')
                @endif
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3">Stamping History</h2>
            @if ($entry->stamps->isEmpty())
                <p class="text-muted mb-0">This report has not been stamped yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Stamped On</th>
                                <th>Stamped By</th>
                                <th>Verification Code</th>
                                <th>Covers</th>
                                <th class="text-end">File</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($entry->stamps as $stamp)
                                <tr>
                                    <td class="text-nowrap small">{{ $stamp->created_at->format('M d, Y g:i A') }}</td>
                                    <td>{{ $stamp->stamped_by_name }}</td>
                                    <td><code>{{ $stamp->verification_code }}</code></td>
                                    <td>
                                        @if ($stamp->original_file_hash === $entry->report_file_hash)
                                            <span class="badge racts-badge-green">Current file</span>
                                        @else
                                            <span class="badge bg-dark-subtle text-dark-emphasis">Earlier submission</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('digital-stamping.download', $stamp) }}" class="btn btn-outline-secondary btn-sm" title="Download">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
