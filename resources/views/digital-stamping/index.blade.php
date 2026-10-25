@extends('layouts.app')

@section('title', 'Digital Stamping')

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p class="text-muted mb-3">
                Reports submitted to DILG. Stamping creates a separate stamped copy (PDF or DOCX) - the barangay's
                original file is never changed. Only PDF and DOCX files can be stamped.
            </p>
            <ul class="nav nav-tabs">
                @foreach (\App\Http\Controllers\DigitalStampingController::STATES as $value => $label)
                    <li class="nav-item">
                        <a class="nav-link {{ $state === $value ? 'active' : '' }}"
                           href="{{ route('digital-stamping.index', ['state' => $value]) }}">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Report</th>
                        <th>Barangay</th>
                        <th>Submitted</th>
                        <th>Submission</th>
                        <th>Stamping</th>
                        <th>Stamped By / On</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $entry)
                        @php($stamp = $entry->currentStamp())
                        <tr>
                            <td class="text-nowrap small">{{ $entry->referenceNumber() }}</td>
                            <td>{{ $entry->item_description }}</td>
                            <td>{{ $entry->barangay->barangay_name }}</td>
                            <td class="text-nowrap small">{{ $entry->date_submitted->format('M d, Y g:i A') }}</td>
                            <td><span class="badge {{ $entry->badgeClass() }}">{{ $entry->statusLabel() }}</span></td>
                            <td><span class="badge {{ $stamp ? 'racts-badge-green' : 'bg-secondary-subtle text-secondary-emphasis' }}">{{ $entry->stampingStatusLabel() }}</span></td>
                            <td class="small">
                                @if ($stamp)
                                    {{ $stamp->stamped_by_name }}<br>
                                    <span class="text-muted">{{ $stamp->created_at->format('M d, Y g:i A') }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('barangay-log-details.view-file', $entry) }}" target="_blank" rel="noopener"
                                   class="btn btn-outline-secondary btn-sm" title="Preview original"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('digital-stamping.show', $entry) }}" class="btn btn-outline-secondary btn-sm"
                                   title="Stamping details &amp; history"><i class="bi bi-clock-history"></i></a>
                                @if ($stamp)
                                    <a href="{{ route('digital-stamping.download', $stamp) }}" class="btn btn-outline-success btn-sm"
                                       title="Download stamped copy"><i class="bi bi-download"></i></a>
                                @elseif ($entry->isStampable())
                                    @include('digital-stamping._stamp-button')
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-0">
                                <div class="racts-empty-state">
                                    <i class="bi bi-inbox"></i>
                                    No reports here.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $entries->links() }}</div>
@endsection
