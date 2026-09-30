{{--
    One council's ranking table + charts. Included twice by
    analytics/index.blade.php (once per Barangay/SK tab) with a
    different $data/$prefix/$councilLabel each time.
--}}
@php
    $ranking = $data['ranking'];
@endphp

<div class="row g-3 mb-3">
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h2 class="h6 mb-0">{{ $councilLabel }} Council Ranking</h2>
                        @if ($data['selectedRequirementId'])
                            <span class="badge bg-primary-subtle text-primary-emphasis mt-1">
                                Showing: {{ $data['selectedRequirementLabel'] }}
                            </span>
                        @endif
                    </div>
                    <div class="d-flex gap-2">
                        {{-- The report filter above rides along on every
                             export link, so the exported file always
                             matches whatever this tab is currently showing
                             - the user never has to re-pick it. --}}
                        <a href="{{ route('analytics.export-pdf', ['council' => $prefix, 'start' => $start, 'end' => $end, 'requirement' => $data['selectedRequirementId']]) }}"
                           target="_blank" class="btn btn-outline-secondary btn-sm" title="Opens a printable page - use its Print / Save as PDF button">
                            <i class="bi bi-file-earmark-pdf"></i> Export PDF
                        </a>
                        <a href="{{ route('analytics.export-csv', ['council' => $prefix, 'start' => $start, 'end' => $end, 'requirement' => $data['selectedRequirementId']]) }}"
                           class="btn btn-outline-secondary btn-sm" title="Downloads a .csv file - opens directly in Excel">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Rank</th>
                                <th>Barangay</th>
                                <th class="text-end">%</th>
                                <th class="text-end">Green</th>
                                <th class="text-end">Yellow</th>
                                <th class="text-end">Red</th>
                                <th class="text-end">Graded</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ranking as $row)
                                <tr>
                                    <td>#{{ $row['rank'] }}</td>
                                    <td>{{ $row['barangay']->barangay_name }}</td>
                                    <td class="text-end fw-semibold">
                                        {{ is_null($row['percentage']) ? '—' : $row['percentage'].'%' }}
                                    </td>
                                    <td class="text-end"><span class="badge racts-badge-green">{{ $row['green_count'] }}</span></td>
                                    <td class="text-end"><span class="badge racts-badge-yellow">{{ $row['yellow_count'] }}</span></td>
                                    <td class="text-end"><span class="badge racts-badge-red">{{ $row['red_count'] }}</span></td>
                                    <td class="text-end text-muted">{{ $row['graded_count'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-0">
                                        <div class="racts-empty-state">
                                            <i class="bi bi-bar-chart"></i>
                                            No active barangays found.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h2 class="h6 mb-3">Compliance by Barangay</h2>
                <canvas id="{{ $prefix }}-bar-chart" height="260"></canvas>
                <p class="form-text mt-2 mb-0">
                    A barangay with nothing graded yet in this range plots as 0% here - check the table
                    above (a "—" there) to tell that apart from a confirmed 0%.
                </p>
            </div>
        </div>
    </div>
</div>

@if ($data['trend'])
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h6 mb-3">Monthly Trend &mdash; Average {{ $councilLabel }} Compliance</h2>
            <canvas id="{{ $prefix }}-trend-chart" height="100"></canvas>
            <p class="form-text mt-2 mb-0">
                Each point is that single month's council-wide average (a gap means no barangay had
                anything graded that month yet). Yearly reports still count toward every month's
                average here, same as the percentage everywhere else on this page.
            </p>
        </div>
    </div>
@endif
