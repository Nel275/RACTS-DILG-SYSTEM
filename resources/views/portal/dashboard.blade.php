@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
    {{-- Welcome / identity banner --}}
    <div class="card shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap align-items-center gap-3">
            @include('profile._avatar', ['user' => $user, 'size' => 64])
            <div class="flex-grow-1">
                <h1 class="h4 mb-0">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ $user->name }}</h1>
                <div class="text-muted">
                    {{ $user->position ?: 'Barangay User' }} &middot;
                    <i class="bi bi-signpost-2-fill"></i> Barangay <strong>{{ $barangay->barangay_name }}</strong>,
                    Santiago City
                </div>
            </div>
            <div class="text-end small text-muted">
                <div>{{ now()->format('l, F j, Y') }}</div>
                @if ($user->last_login_at)
                    <div>Last sign-in: {{ $user->last_login_at->format('M d, g:i A') }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if ($alerts->isNotEmpty() || $counts['missed'] > 0 || $counts['returned'] > 0)
        <div class="alert alert-warning d-flex align-items-start gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill mt-1"></i>
            <div>
                @if ($counts['returned'] > 0)
                    <div><strong>{{ $counts['returned'] }}</strong> report{{ $counts['returned'] === 1 ? ' was' : 's were' }} returned by DILG for revision.
                        <a href="{{ route('portal.reports', ['status' => 'returned']) }}" class="alert-link">Review the feedback and resubmit</a>.</div>
                @endif
                @if ($counts['missed'] > 0)
                    <div><strong>{{ $counts['missed'] }}</strong> report{{ $counts['missed'] === 1 ? ' is' : 's are' }} overdue. Please
                        <a href="{{ route('portal.reports', ['status' => 'missed']) }}" class="alert-link">submit {{ $counts['missed'] === 1 ? 'it' : 'them' }} now</a>.</div>
                @endif
                @foreach ($alerts as $alert)
                    <div class="small">{{ $alert->message }}</div>
                @endforeach
                @if ($alerts->isNotEmpty())
                    <a href="{{ route('notifications.index') }}" class="small alert-link">See all notifications</a>
                @endif
            </div>
        </div>
    @endif

    {{-- My report totals --}}
    <div class="row g-3 mb-3">
        @php
            $cards = [
                ['label' => 'To Submit', 'value' => $counts['pending'], 'icon' => 'bi-hourglass-split', 'class' => 'text-secondary'],
                ['label' => 'Submitted', 'value' => $counts['submitted'], 'icon' => 'bi-check-circle-fill', 'class' => 'text-success'],
                ['label' => 'Submitted Late', 'value' => $counts['late'], 'icon' => 'bi-clock-history', 'class' => 'text-warning'],
                ['label' => 'Missed', 'value' => $counts['missed'], 'icon' => 'bi-x-circle-fill', 'class' => 'text-danger'],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="col-6 col-lg-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="h3 mb-0">{{ $card['value'] }}</div>
                        </div>
                        <i class="bi {{ $card['icon'] }} {{ $card['class'] }} fs-1"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        {{-- Standing in each council --}}
        @foreach ($standings as $standing)
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <h2 class="h6 mb-3"><i class="bi bi-trophy text-secondary"></i> {{ $standing['label'] }} Council &mdash; Year-to-Date Standing</h2>
                        @if ($standing['row'] && $standing['row']['percentage'] !== null)
                            <div class="d-flex align-items-end gap-4">
                                <div>
                                    <div class="text-muted small">Rank</div>
                                    <div class="display-6 mb-0">#{{ $standing['row']['rank'] }}<span class="fs-6 text-muted"> of {{ $standing['of'] }}</span></div>
                                </div>
                                <div>
                                    <div class="text-muted small">Compliance score</div>
                                    <div class="display-6 mb-0">{{ number_format($standing['row']['percentage'], 1) }}%</div>
                                </div>
                            </div>
                            <div class="d-flex gap-3 small mt-3">
                                <span><span class="badge racts-badge-green">&nbsp;</span> On time: {{ $standing['row']['green_count'] }}</span>
                                <span><span class="badge racts-badge-yellow">&nbsp;</span> Late: {{ $standing['row']['yellow_count'] }}</span>
                                <span><span class="badge racts-badge-red">&nbsp;</span> Missing: {{ $standing['row']['red_count'] }}</span>
                            </div>
                        @else
                            <p class="text-muted mb-0">No graded reports yet this year, so there is no ranking to show.</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-3">
        {{-- What to submit next --}}
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 mb-0"><i class="bi bi-list-check text-secondary"></i> Reports to Submit</h2>
                        <a href="{{ route('portal.reports') }}" class="small">All my reports &rarr;</a>
                    </div>
                    @forelse ($toSubmit as $entry)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <div>
                                <div class="fw-semibold">{{ $entry->item_description }}</div>
                                <div class="small text-muted">
                                    {{ $entry->barangayLog?->councilLabel() }} &middot;
                                    Due {{ $entry->deadline_date->format('M d, Y') }}
                                    @if ($entry->status === 'pending')
                                        ({{ $entry->deadline_date->isToday() ? 'today' : $entry->deadline_date->diffForHumans() }})
                                    @endif
                                </div>
                            </div>
                            <span class="badge {{ $entry->badgeClass() }}">{{ $entry->statusLabel() }}</span>
                        </div>
                    @empty
                        <div class="racts-empty-state">
                            <i class="bi bi-emoji-smile"></i>
                            You're all caught up. Nothing is waiting to be submitted.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recently submitted --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h2 class="h6 mb-3"><i class="bi bi-clock-history text-secondary"></i> Recently Submitted</h2>
                    @forelse ($recentSubmissions as $entry)
                        <div class="d-flex justify-content-between border-bottom py-2">
                            <div>
                                <div>{{ $entry->item_description }}</div>
                                <div class="small text-muted">{{ $entry->date_submitted?->format('M d, Y g:i A') }}</div>
                            </div>
                            <span class="badge {{ $entry->badgeClass() }} align-self-start">{{ $entry->statusLabel() }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nothing submitted yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- My recent activity --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3"><i class="bi bi-activity text-secondary"></i> My Barangay's Recent Activity</h2>
            @forelse ($recentActivity as $log)
                <div class="d-flex justify-content-between border-bottom py-2 small">
                    <span>{{ $log->action }} <span class="text-muted">&mdash; {{ $log->user?->name }}</span></span>
                    <span class="text-muted text-nowrap ms-3">{{ $log->log_date->format('M d, g:i A') }}</span>
                </div>
            @empty
                <p class="text-muted mb-0">No activity yet.</p>
            @endforelse
        </div>
    </div>
@endsection
