{{--
    Follow-up round: SK Reports' own Report Filing tab - the same
    feature as barangay-log-details/index.blade.php, just pointed at
    the SK council (see BarangayLogDetailController::indexSk() and
    BarangayLog::council_type). Both pages render the exact same shared
    partial, barangay-log-details/_list.blade.php, parameterized by
    $councilType - kept there rather than moved to a council-neutral
    folder so it stays next to the controller and model it belongs to.
--}}
@extends('layouts.app')

@section('title', 'Report Filing')

@section('content')
    @include('report-grid._reports-tabs', ['active' => 'log-details', 'councilType' => \App\Models\ReportRequirement::COUNCIL_SK])
    @include('barangay-log-details._list', ['councilType' => \App\Models\ReportRequirement::COUNCIL_SK])
@endsection
