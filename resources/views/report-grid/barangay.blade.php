@extends('layouts.app')

@section('title', 'Barangay Reports')

@section('content')
    @include('report-grid._reports-tabs', ['active' => 'grid', 'councilType' => \App\Models\ReportRequirement::COUNCIL_BARANGAY])
    @include('report-grid._grid')
@endsection
