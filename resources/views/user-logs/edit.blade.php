@extends('layouts.app')

@section('title', 'Edit Personnel')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('user-logs.update', $userLog) }}">
                @csrf
                @method('PUT')
                @include('user-logs._form', ['userLog' => $userLog])

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save</button>
                    <a href="{{ route('user-logs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
