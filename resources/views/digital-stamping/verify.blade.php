@extends('layouts.app')

@section('title', 'Verify Stamp')

@section('content')
    <div class="card shadow-sm" style="max-width: 640px;">
        <div class="card-body">
            <p class="text-muted">Enter the verification code printed on a stamped report. Results come from RACTS records, not from the document itself.</p>
            <form method="GET" action="{{ route('verify-stamp') }}" class="d-flex gap-2 mb-3">
                <input type="text" name="code" value="{{ $code }}" maxlength="32" class="form-control" placeholder="Verification code" required>
                <button type="submit" class="btn btn-primary">Verify</button>
            </form>

            @if ($code !== '')
                @if ($stamp)
                    <div class="alert {{ $stamp->isCurrent() ? 'alert-success' : 'alert-warning' }} mb-0">
                        <strong>{{ $stamp->isCurrent() ? 'Valid stamp.' : 'Valid stamp for an earlier submission - the report has since been resubmitted.' }}</strong>
                        <dl class="row mb-0 mt-2 small">
                            <dt class="col-5">Report reference</dt>
                            <dd class="col-7">{{ $stamp->barangayLogDetail->referenceNumber() }}</dd>
                            <dt class="col-5">Report status</dt>
                            <dd class="col-7">{{ $stamp->barangayLogDetail->statusLabel() }}</dd>
                            <dt class="col-5">Stamped on</dt>
                            <dd class="col-7">{{ $stamp->created_at->format('F j, Y g:i A') }}</dd>
                            <dt class="col-5">Issued by</dt>
                            <dd class="col-7">{{ config('racts.stamp.office') }} &middot; {{ $stamp->stamped_by_name }}</dd>
                        </dl>
                    </div>
                @else
                    <div class="alert alert-danger mb-0"><strong>Invalid code.</strong> No stamp with this code exists in RACTS.</div>
                @endif
            @endif
        </div>
    </div>
@endsection
