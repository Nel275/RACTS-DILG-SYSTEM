@extends('layouts.app')

@section('title', 'Personnel Record')

@section('content')
    <a href="{{ route('user-logs.index') }}" class="d-inline-block mb-3">&larr; Back to User Logs</a>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                <div>
                    <h1 class="h5 mb-1">{{ $userLog->fullName() }}</h1>
                    <p class="text-muted mb-0">{{ $userLog->designation }} &mdash; {{ $userLog->roles }}</p>
                </div>
                <span class="badge {{ $userLog->isActive() ? 'bg-success' : 'bg-secondary' }} fs-6">{{ $userLog->statusLabel() }}</span>
            </div>

            <dl class="row mb-0">
                <dt class="col-sm-3">Birthdate</dt>
                <dd class="col-sm-9">{{ $userLog->birthdate->format('F j, Y') }}</dd>

                <dt class="col-sm-3">Gender</dt>
                <dd class="col-sm-9">{{ $userLog->gender }}</dd>

                <dt class="col-sm-3">Barangay</dt>
                <dd class="col-sm-9">{{ $userLog->barangay?->barangay_name ?? '—' }}</dd>

                <dt class="col-sm-3">Designation</dt>
                <dd class="col-sm-9">{{ $userLog->designation }}</dd>

                <dt class="col-sm-3">Role</dt>
                <dd class="col-sm-9">{{ $userLog->roles }}</dd>

                <dt class="col-sm-3">Last Processed</dt>
                <dd class="col-sm-9">
                    {{ $userLog->date_process?->format('F j, Y') ?? '—' }}
                    @if ($userLog->processed_by)
                        by {{ $userLog->processed_by }}
                    @endif
                </dd>
            </dl>

            @if (auth()->user()->isAdmin())
                <a href="{{ route('user-logs.edit', $userLog) }}" class="btn btn-outline-primary btn-sm mt-2">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif
        </div>
    </div>
@endsection
