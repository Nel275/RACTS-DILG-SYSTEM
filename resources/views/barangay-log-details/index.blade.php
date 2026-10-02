@extends('layouts.app')

@section('title', 'Report Filing')

@section('content')
    @include('report-grid._reports-tabs', ['active' => 'log-details', 'councilType' => \App\Models\ReportRequirement::COUNCIL_BARANGAY])
    @include('barangay-log-details._list', ['councilType' => \App\Models\ReportRequirement::COUNCIL_BARANGAY])
@endsection
