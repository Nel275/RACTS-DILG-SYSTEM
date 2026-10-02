@extends('layouts.app')

@section('title', 'SK Reports')

@section('content')
    @include('report-grid._reports-tabs', ['active' => 'grid', 'councilType' => \App\Models\ReportRequirement::COUNCIL_SK])
    @include('report-grid._grid')
@endsection
