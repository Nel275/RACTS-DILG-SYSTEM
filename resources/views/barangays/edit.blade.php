@extends('layouts.app')

@section('title', 'Edit Barangay')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('barangays.update', $barangay) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="barangay_name" class="form-label">Barangay Name</label>
                            <input type="text" name="barangay_name" id="barangay_name"
                                   value="{{ old('barangay_name', $barangay->barangay_name) }}"
                                   class="form-control @error('barangay_name') is-invalid @enderror" required maxlength="255"
                                   pattern="[\p{L} .\-]+" title="Letters, spaces, periods and hyphens only"
                                   oninput="this.value = this.value.replace(/[^\p{L} .\-]/gu, '')">
                            @error('barangay_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach (\App\Models\Barangay::STATUSES as $value => $label)
                                    <option value="{{ $value }}" @selected(old('status', $barangay->status) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <a href="{{ route('barangays.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
