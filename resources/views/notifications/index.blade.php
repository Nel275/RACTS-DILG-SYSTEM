@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    @if ($notifications->total() > 0)
        <div class="d-flex justify-content-end gap-2 mb-3">
            @if ($notifications->contains('is_read', false))
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Mark All as Read</button>
                </form>
            @endif
            <form method="POST" action="{{ route('notifications.clear') }}"
                  onsubmit="return confirm('Clear all of your notifications? This cannot be undone.');">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash3"></i> Clear Notifications
                </button>
            </form>
        </div>
    @endif

    <p class="text-muted">
        This page only shows alerts RACTS generates automatically when a monthly report is late -
        there's no manual/general notification any more. Everyone with a RACTS account sees the
        same alerts.
    </p>

    <div class="card shadow-sm">
        <div class="list-group list-group-flush">
                @php
                    $typeBadgeClass = [
                        \App\Models\Notification::TYPE_PERIOD_OPEN => 'bg-info-subtle text-info-emphasis',
                        \App\Models\Notification::TYPE_DEADLINE_TODAY => 'bg-warning-subtle text-warning-emphasis',
                        \App\Models\Notification::TYPE_REPORT_LATE => 'bg-danger-subtle text-danger-emphasis',
                        \App\Models\Notification::TYPE_LATE_SUBMISSION => 'bg-danger-subtle text-danger-emphasis',
                    ];
                @endphp
                @forelse ($notifications as $notification)
                    @php $deadline = $notification->deadline(); @endphp
                <div class="list-group-item d-flex justify-content-between align-items-start {{ $notification->is_read ? '' : 'bg-light' }}">
                    <div>
                        <div class="{{ $notification->is_read ? 'text-muted' : 'fw-semibold' }}">
                            @unless ($notification->is_read)
                                <span class="badge bg-danger me-1">New</span>
                            @endunless
                            @if ($notification->type)
                                <span class="badge {{ $typeBadgeClass[$notification->type] ?? 'bg-secondary-subtle text-secondary-emphasis' }} me-1">
                                    {{ $notification->typeLabel() }}
                                </span>
                            @endif
                            {{ $notification->message }}
                        </div>
                        @if ($notification->barangay || $notification->reportRecord)
                            <div class="small text-muted">
                                {{ $notification->barangay->barangay_name ?? $notification->reportRecord?->barangay?->barangay_name }}
                                &mdash; {{ $notification->reportRequirement->report_name ?? $notification->reportRecord?->reportRequirement?->report_name }}
                                ({{ $notification->period ?? $notification->reportRecord?->period }})
                                @if ($deadline)
                                    &mdash; deadline {{ $deadline->format('M j, Y') }}
                                @endif
                            </div>
                        @endif
                        <div class="small text-muted">
                            {{ $notification->notification_date->format('F j, Y g:i A') }}
                        </div>
                    </div>
                    @unless ($notification->is_read)
                        <form method="POST" action="{{ route('notifications.read', $notification) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-primary btn-sm">Mark as Read</button>
                        </form>
                    @endunless
                </div>
            @empty
                <div class="list-group-item racts-empty-state">
                    <i class="bi bi-bell-slash"></i>
                    No automated alerts yet. You'll see one here as soon as a barangay
                    misses a monthly report deadline and RACTS's scheduled check catches it.
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>
@endsection
