@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <h1 class="h4 mb-1">Welcome, {{ auth()->user()->name }}</h1>
            <p class="text-muted mb-0">
                You are logged in as <strong>{{ auth()->user()->roleLabel() }}</strong>.
            </p>
        </div>
    </div>

    <!-- Totals -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Active Barangays</div>
                        <div class="h3 mb-0">{{ $totalBarangays }}</div>
                    </div>
                    <i class="bi bi-signpost-2-fill text-secondary fs-1"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Active Report Slots</div>
                        <div class="h3 mb-0">{{ $totalReportSlots }}</div>
                    </div>
                    <i class="bi bi-folder2-open text-secondary fs-1"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Current Period</div>
                        <div class="h3 mb-0">{{ now()->format('F Y') }}</div>
                    </div>
                    <i class="bi bi-calendar-event text-secondary fs-1"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Current-Cycle Progress -->
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3"><i class="bi bi-hourglass-split text-info"></i> Barangay Current-Cycle Progress &mdash; {{ $currentPeriodLabel }}</h2>
                    @include('dashboard._cycle-progress', ['progress' => $barangayProgress])
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3"><i class="bi bi-hourglass-split text-info"></i> SK Current-Cycle Progress &mdash; {{ $currentPeriodLabel }}</h2>
                    @include('dashboard._cycle-progress', ['progress' => $skProgress])
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <!-- Barangay Council Ranking -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 mb-0"><i class="bi bi-trophy text-warning"></i> Top Barangay Councils (Year to Date)</h2>
                        <a href="{{ route('barangay-reports.index') }}" class="small">Open grid &rarr;</a>
                    </div>
                    @forelse ($barangayRanking as $row)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis me-2">#{{ $row['rank'] }}</span>
                                {{ $row['barangay']->barangay_name }}
                            </span>
                            <span class="fw-semibold">
                                {{ is_null($row['percentage']) ? '—' : $row['percentage'].'%' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No active barangays found.</p>
                    @endforelse
                    <a href="{{ route('analytics.index') }}" class="small d-inline-block mt-2">Full ranking &amp; charts &rarr;</a>
                </div>
            </div>
        </div>

        <!-- SK Council Ranking -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 mb-0"><i class="bi bi-trophy text-warning"></i> Top SK Councils (Year to Date)</h2>
                        <a href="{{ route('sk-reports.index') }}" class="small">Open grid &rarr;</a>
                    </div>
                    @forelse ($skRanking as $row)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span>
                                <span class="badge bg-secondary-subtle text-secondary-emphasis me-2">#{{ $row['rank'] }}</span>
                                {{ $row['barangay']->barangay_name }}
                            </span>
                            <span class="fw-semibold">
                                {{ is_null($row['percentage']) ? '—' : $row['percentage'].'%' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No active barangays found.</p>
                    @endforelse
                    <a href="{{ route('analytics.index') }}" class="small d-inline-block mt-2">Full ranking &amp; charts &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 mb-0"><i class="bi bi-clock-history text-secondary"></i> Recent Activity</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                    @if (auth()->user()->isAdmin() && $recentActivity->isNotEmpty())
                        <form method="POST" action="{{ route('dashboard.clear-activity') }}"
                              onsubmit="return confirm('Clear the recent activity history? This only clears this list — it never deletes any recorded report entries.');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-trash3"></i> Clear History
                            </button>
                        </form>
                    @endif
                </div>
            </div>
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
                        @forelse ($recentActivity as $entry)
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
    </div>
@endsection
