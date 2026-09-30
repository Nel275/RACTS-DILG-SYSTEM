@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">
            Every recorded action across RACTS - report entries plus admin changes to users,
            barangays, and report slots - newest first.
        </p>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Date &amp; Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $entry)
                        <tr>
                            <td>{{ $entry->user?->name ?? '—' }}</td>
                            <td>{{ $entry->action }}</td>
                            <td>{{ $entry->log_date?->format('M j, Y g:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="p-0">
                                <div class="racts-empty-state py-3">
                                    <i class="bi bi-clock-history"></i>
                                    No activity recorded yet.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $logs->links() }}
    </div>
@endsection
