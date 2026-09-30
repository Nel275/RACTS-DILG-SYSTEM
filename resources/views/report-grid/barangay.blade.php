@extends('layouts.app')

@section('title', 'Barangay Reports')

@section('content')
    @include('report-grid._reports-tabs', ['active' => 'grid'])
    @include('report-grid._grid')
@endsection
