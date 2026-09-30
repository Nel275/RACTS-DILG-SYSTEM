{{--
    Shared fields for user-logs/create.blade.php and edit.blade.php.
    $userLog is null on create.
--}}
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" value="{{ old('first_name', $userLog?->first_name) }}" class="form-control @error('first_name') is-invalid @enderror" required>
        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Middle Name</label>
        <input type="text" name="middle_name" value="{{ old('middle_name', $userLog?->middle_name) }}" class="form-control @error('middle_name') is-invalid @enderror">
        @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" value="{{ old('last_name', $userLog?->last_name) }}" class="form-control @error('last_name') is-invalid @enderror" required>
        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Birthdate</label>
        <input type="date" name="birthdate" value="{{ old('birthdate', $userLog?->birthdate?->format('Y-m-d')) }}" class="form-control @error('birthdate') is-invalid @enderror" required>
        @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Gender</label>
        <input type="text" name="gender" value="{{ old('gender', $userLog?->gender) }}" class="form-control @error('gender') is-invalid @enderror" required>
        @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Barangay</label>
        <select name="barangay_id" class="form-select @error('barangay_id') is-invalid @enderror">
            <option value="">— Not assigned —</option>
            @foreach ($barangayOptions as $option)
                <option value="{{ $option->id }}" @selected(old('barangay_id', $userLog?->barangay_id) == $option->id)>{{ $option->barangay_name }}</option>
            @endforeach
        </select>
        @error('barangay_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Designation</label>
        <input type="text" name="designation" value="{{ old('designation', $userLog?->designation) }}" class="form-control @error('designation') is-invalid @enderror" required>
        @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Role</label>
        <input type="text" name="roles" value="{{ old('roles', $userLog?->roles) }}" class="form-control @error('roles') is-invalid @enderror" required>
        @error('roles')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <select name="status" class="form-select @error('status') is-invalid @enderror">
            @foreach (\App\Models\UserLog::STATUSES as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $userLog?->status ?? \App\Models\UserLog::STATUS_ACTIVE) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
