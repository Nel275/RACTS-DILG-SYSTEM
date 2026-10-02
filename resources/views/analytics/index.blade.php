@extends('layouts.app')

@section('title', 'Analytics & Ranking')

@section('content')
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics.index') }}" class="row g-2 align-items-end">
                <div class="col-sm-4 col-md-3">
                    <label for="filter-start" class="form-label small mb-1">Start Month</label>
                    <input type="month" name="start" id="filter-start" value="{{ $start }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-4 col-md-3">
                    <label for="filter-end" class="form-label small mb-1">End Month</label>
                    <input type="month" name="end" id="filter-end" value="{{ $end }}" class="form-control form-control-sm">
                </div>
                <div class="col-sm-6 col-md-3">
                    <label for="filter-barangay-report" class="form-label small mb-1">Barangay Report</label>
                    <select name="barangay_report" id="filter-barangay-report" class="form-select form-select-sm">
                        <option value="">All Reports</option>
                        @foreach ($barangay['reportOptions'] as $option)
                            <option value="{{ $option->id }}" {{ $barangayRequirementId === $option->id ? 'selected' : '' }}>
                                {{ $option->report_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-6 col-md-3">
                    <label for="filter-sk-report" class="form-label small mb-1">SK Report</label>
                    <select name="sk_report" id="filter-sk-report" class="form-select form-select-sm">
                        <option value="">All Reports</option>
                        @foreach ($sk['reportOptions'] as $option)
                            <option value="{{ $option->id }}" {{ $skRequirementId === $option->id ? 'selected' : '' }}>
                                {{ $option->report_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary btn-sm">Apply</button>
                    <a href="{{ route('analytics.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
            <div class="form-text mt-2 mb-0">
                The ranking, percentages, and bar chart below cover this whole range. Yearly reports
                always count toward the percentage regardless of range. Select more than one month to
                also see a month-by-month trend. The Barangay Report and SK Report filters apply only
                within their own tab, and each is carried automatically into that tab's PDF/Excel export.
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-3" id="analytics-tabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="barangay-tab-btn" data-bs-toggle="tab" data-bs-target="#barangay-tab" type="button" role="tab" aria-controls="barangay-tab" aria-selected="true">
                Barangay Council
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sk-tab-btn" data-bs-toggle="tab" data-bs-target="#sk-tab" type="button" role="tab" aria-controls="sk-tab" aria-selected="false">
                SK Council
            </button>
        </li>
    </ul>

    <div class="tab-content" id="analytics-tab-content">
        <div class="tab-pane fade show active" id="barangay-tab" role="tabpanel" aria-labelledby="barangay-tab-btn">
            @include('analytics._council-panel', ['data' => $barangay, 'prefix' => 'barangay', 'councilLabel' => 'Barangay'])
        </div>
        <div class="tab-pane fade" id="sk-tab" role="tabpanel" aria-labelledby="sk-tab-btn">
            @include('analytics._council-panel', ['data' => $sk, 'prefix' => 'sk', 'councilLabel' => 'SK'])
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Chart.js - loaded from the same CDN Bootstrap already comes
         from, only on this page. No build step, no framework: just a
         script tag plus the vanilla JS below, consistent with the rest
         of RACTS. --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
    <script>
        const RACTS_ANALYTICS = {
            barangay: @json($barangay['chart']),
            barangayTrend: @json($barangay['trend']),
            sk: @json($sk['chart']),
            skTrend: @json($sk['trend']),
        };

        function racBarChart(canvasId, labels, data) {
            const el = document.getElementById(canvasId);
            if (!el) return null;

            return new Chart(el, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Compliance %',
                        data: data,
                        backgroundColor: '#0b3d91',
                        borderRadius: 2,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { callback: (v) => v + '%' } },
                    },
                },
            });
        }

        function racTrendChart(canvasId, labels, data) {
            const el = document.getElementById(canvasId);
            if (!el) return null;

            return new Chart(el, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Average Compliance %',
                        data: data,
                        borderColor: '#0b3d91',
                        backgroundColor: 'rgba(11, 61, 145, 0.12)',
                        tension: 0.25,
                        spanGaps: false,
                        fill: true,
                    }],
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, max: 100, ticks: { callback: (v) => v + '%' } },
                    },
                },
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            // The Barangay tab is active by default, so its charts are
            // safe to build immediately. The SK tab starts hidden
            // (display: none), and Chart.js can't size a canvas it
            // can't see - so its charts are built lazily the first time
            // that tab is actually shown, once Bootstrap has made it
            // visible.
            racBarChart('barangay-bar-chart', RACTS_ANALYTICS.barangay.labels, RACTS_ANALYTICS.barangay.percentages);
            if (RACTS_ANALYTICS.barangayTrend) {
                racTrendChart('barangay-trend-chart', RACTS_ANALYTICS.barangayTrend.labels, RACTS_ANALYTICS.barangayTrend.values);
            }

            let skChartsBuilt = false;
            const skTabBtn = document.getElementById('sk-tab-btn');
            if (skTabBtn) {
                skTabBtn.addEventListener('shown.bs.tab', function () {
                    if (skChartsBuilt) return;
                    skChartsBuilt = true;

                    racBarChart('sk-bar-chart', RACTS_ANALYTICS.sk.labels, RACTS_ANALYTICS.sk.percentages);
                    if (RACTS_ANALYTICS.skTrend) {
                        racTrendChart('sk-trend-chart', RACTS_ANALYTICS.skTrend.labels, RACTS_ANALYTICS.skTrend.values);
                    }
                });
            }
        });
    </script>
@endsection
