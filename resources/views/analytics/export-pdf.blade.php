{{--
    PDF export (client spec Section 3.4). This is a plain printable page
    - the DILG letterhead comes from layouts.print, already built in an
    earlier phase and reserved for exactly this. "PDF" is the browser's
    own Print / Save as PDF (the button layouts.print already has),
    matching how RACTS has always produced a PDF, rather than a new
    server-side PDF-rendering dependency.
--}}
@extends('layouts.print')

@section('title', $councilLabel.' Analytics Export')
@section('report-title', $councilLabel.' Council — Analytics & Ranking Report')

@section('content')
    <div class="mb-3">
        <strong>Report:</strong> {{ $reportLabel }} &nbsp;|&nbsp;
        <strong>Period:</strong> {{ $startLabel }} &ndash; {{ $endLabel }}
    </div>

    <table class="table table-bordered table-sm">
        <thead>
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
                    <td class="text-end">{{ is_null($row['percentage']) ? '—' : $row['percentage'].'%' }}</td>
                    <td class="text-end">{{ $row['green_count'] }}</td>
                    <td class="text-end">{{ $row['yellow_count'] }}</td>
                    <td class="text-end">{{ $row['red_count'] }}</td>
                    <td class="text-end">{{ $row['graded_count'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No active barangays found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="text-muted small mt-3">
        A "—" percentage means no report was graded for that barangay in this period; it is
        excluded from the ranking average rather than counted as 0%.
    </p>
@endsection
