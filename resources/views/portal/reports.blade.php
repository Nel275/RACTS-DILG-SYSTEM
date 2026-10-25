@extends('layouts.app')

@section('title', 'My Reports')

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <p class="text-muted mb-3">
                Reports DILG requires from <strong>Barangay {{ $barangay->barangay_name }}</strong>. Monthly and annual
                reports appear here automatically each period. Attach your file to submit; the submission date is
                recorded automatically.
            </p>

            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ $council === 'barangay' ? 'active' : '' }}"
                       href="{{ route('portal.reports', ['council' => 'barangay']) }}">Barangay Council</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $council === 'sk' ? 'active' : '' }}"
                       href="{{ route('portal.reports', ['council' => 'sk']) }}">SK Council</a>
                </li>
            </ul>

            <form method="GET" action="{{ route('portal.reports') }}" class="row g-2">
                <input type="hidden" name="council" value="{{ $council }}">
                <div class="col-md-3">
                    <input type="text" name="search" value="{{ $search }}" maxlength="100"
                           class="form-control" placeholder="Search report name" data-input-filter="text">
                </div>
                <div class="col-6 col-md-2">
                    <select name="frequency" class="form-select" aria-label="Report type">
                        <option value="">All types</option>
                        @foreach (\App\Models\BarangayLog::FREQUENCIES as $value => $label)
                            <option value="{{ $value }}" @selected($frequency === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="status" class="form-select" aria-label="Status">
                        <option value="">All statuses</option>
                        @foreach (\App\Models\BarangayLogDetail::STATUSES as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                        <option value="resubmitted" @selected($status === 'resubmitted')>Resubmitted</option>
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <select name="year" class="form-select" aria-label="Year">
                        <option value="">Year</option>
                        @foreach (range(now()->year, now()->year - 3) as $y)
                            <option value="{{ $y }}" @selected((string) $year === (string) $y)>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="month" class="form-select" aria-label="Month">
                        <option value="">Any month</option>
                        @foreach (range(1, 12) as $m)
                            @php($mm = str_pad($m, 2, '0', STR_PAD_LEFT))
                            <option value="{{ $mm }}" @selected($month === $mm)>{{ \Illuminate\Support\Carbon::create(2000, $m, 1)->format('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Filter</button>
                    <a href="{{ route('portal.reports', ['council' => $council]) }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Report</th>
                        <th>Period</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Last Updated</th>
                        <th>DILG Stamp</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($entries as $entry)
                        @php($canSubmit = $entry->isOpenForSubmission())
                        <tr>
                            <td>
                                {{ $entry->item_description }}
                                <div class="small text-muted">
                                    {{ $entry->barangayLog->frequencyLabel() }}
                                    &middot; {{ $entry->barangayLog->is_mandatory ? 'Mandatory' : 'Optional' }}
                                </div>
                                @if ($entry->review_remarks)
                                    <div class="small mt-1 {{ $entry->status === 'returned' ? 'text-danger' : 'text-muted' }}">
                                        <i class="bi bi-chat-left-text"></i> DILG: {{ $entry->review_remarks }}
                                    </div>
                                @endif
                            </td>
                            <td class="text-nowrap small">{{ $entry->periodLabel() ?? '—' }}</td>
                            <td class="text-nowrap">{{ $entry->deadline_date->format('M d, Y') }}</td>
                            <td>
                                <span class="badge {{ $entry->badgeClass() }}">{{ $entry->statusLabel() }}</span>
                                @if ($entry->isResubmission() && in_array($entry->status, ['submitted', 'late'], true))
                                    <span class="badge bg-info-subtle text-info-emphasis">Resubmitted</span>
                                @endif
                            </td>
                            <td class="text-nowrap small">{{ $entry->date_submitted?->format('M d, Y g:i A') ?? '—' }}</td>
                            <td class="text-nowrap small">{{ $entry->updated_at?->format('M d, Y') ?? '—' }}</td>
                            <td class="small">{{ $entry->stampingStatusLabel() }}</td>
                            <td class="text-end text-nowrap">
                                @if ($entry->hasReportFile())
                                    <a href="{{ route('portal.view-file', $entry) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-eye"></i> View file
                                    </a>
                                @endif
                                @if ($entry->currentStamp())
                                    <a href="{{ route('portal.stamped-file', $entry) }}" class="btn btn-outline-success btn-sm">
                                        <i class="bi bi-patch-check"></i> Stamped copy
                                    </a>
                                @endif
                                @if ($canSubmit)
                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#submit-{{ $entry->id }}">
                                        <i class="bi bi-upload"></i> {{ $entry->status === 'returned' ? 'Resubmit' : 'Submit' }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @if ($canSubmit)
                            <tr class="collapse" id="submit-{{ $entry->id }}">
                                <td colspan="8" class="bg-light">
                                    <form method="POST" action="{{ route('portal.submit', $entry) }}" enctype="multipart/form-data"
                                          class="d-flex flex-wrap align-items-end gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="flex-grow-1">
                                            <label class="form-label small mb-1">Attach report (PDF, Word, JPG or PNG &mdash; max 10 MB)</label>
                                            <input type="file" name="report_file" class="form-control form-control-sm"
                                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-sm">{{ $entry->status === 'returned' ? 'Resubmit report' : 'Submit report' }}</button>
                                        @if ($entry->status === 'missed')
                                            <span class="small text-danger w-100">This report is past its deadline and will be marked late.</span>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="p-0">
                                <div class="racts-empty-state">
                                    <i class="bi bi-inbox"></i>
                                    No reports found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @error('report_file')
        <div class="alert alert-danger mt-3">{{ $message }}</div>
    @enderror

    <div class="mt-3">{{ $entries->links() }}</div>
@endsection
