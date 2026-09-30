{{--
    One council's current-cycle progress widget (client spec Section
    3.5). Included twice by dashboard.blade.php with a different
    $progress array each time - see
    DashboardController::currentCycleProgress().
--}}
@if ($progress['expected'] === 0)
    <p class="text-muted mb-0">No active monthly report slots configured for this council yet.</p>
@else
    <div class="progress mb-2" style="height: 1.5rem;" role="progressbar"
         aria-valuenow="{{ $progress['submitted'] }}" aria-valuemin="0" aria-valuemax="{{ $progress['expected'] }}">
        <div class="progress-bar bg-success" style="width: {{ $progress['submitted'] / $progress['expected'] * 100 }}%">
            {{ $progress['submitted'] }}
        </div>
        <div class="progress-bar bg-danger" style="width: {{ $progress['not_submitted'] / $progress['expected'] * 100 }}%">
            {{ $progress['not_submitted'] }}
        </div>
        <div class="progress-bar bg-secondary" style="width: {{ $progress['pending'] / $progress['expected'] * 100 }}%">
            {{ $progress['pending'] }}
        </div>
    </div>
    <div class="d-flex flex-wrap gap-3 small text-muted">
        <span><span class="badge bg-success">&nbsp;</span> Submitted (green/yellow): {{ $progress['submitted'] }}</span>
        <span><span class="badge bg-danger">&nbsp;</span> Not Submitted (red): {{ $progress['not_submitted'] }}</span>
        <span><span class="badge bg-secondary">&nbsp;</span> Pending / Not Yet Graded: {{ $progress['pending'] }}</span>
        <span class="ms-auto fw-semibold">{{ $progress['percent_submitted'] }}% submitted of {{ $progress['expected'] }} expected</span>
    </div>
@endif
