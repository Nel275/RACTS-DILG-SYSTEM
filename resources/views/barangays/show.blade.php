@extends('layouts.app')

@section('title', $barangay->barangay_name)

@section('content')
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('barangays.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back to Barangays
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Barangay Name</dt>
                <dd class="col-sm-9">{{ $barangay->barangay_name }}</dd>

                <dt class="col-sm-3">Status</dt>
                <dd class="col-sm-9">
                    <span class="badge {{ $barangay->isActive() ? 'bg-success' : 'bg-secondary' }}">
                        {{ $barangay->statusLabel() }}
                    </span>
                </dd>

                <dt class="col-sm-3">Recorded Since</dt>
                <dd class="col-sm-9">{{ $barangay->created_at->format('F j, Y') }}</dd>
            </dl>
        </div>
    </div>

    @if (auth()->user()->isAdmin())
        <div class="mt-3">
            <a href="{{ route('barangays.edit', $barangay) }}" class="btn btn-primary btn-sm">Edit</a>
        </div>
    @endif
@endsection
