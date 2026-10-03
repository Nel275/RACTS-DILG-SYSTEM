{{--
    Shared fields for user-logs/create.blade.php and edit.blade.php.
    $userLog is null on create.
--}}
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" value="{{ old('first_name', $userLog?->first_name) }}" class="form-control @error('first_name') is-invalid @enderror" required
               maxlength="100" pattern="[\p{L} .'\-]+" title="Letters, spaces, periods, apostrophes and hyphens only" oninput="this.value = this.value.replace(/[^\p{L} .'\-]/gu, '')">
        @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Middle Name</label>
        <input type="text" name="middle_name" value="{{ old('middle_name', $userLog?->middle_name) }}" class="form-control @error('middle_name') is-invalid @enderror"
               maxlength="100" pattern="[\p{L} .'\-]+" title="Letters, spaces, periods, apostrophes and hyphens only" oninput="this.value = this.value.replace(/[^\p{L} .'\-]/gu, '')">
        @error('middle_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" value="{{ old('last_name', $userLog?->last_name) }}" class="form-control @error('last_name') is-invalid @enderror" required
               maxlength="100" pattern="[\p{L} .'\-]+" title="Letters, spaces, periods, apostrophes and hyphens only" oninput="this.value = this.value.replace(/[^\p{L} .'\-]/gu, '')">
        @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Birthdate</label>
        <input type="date" name="birthdate" min="1900-01-01" max="{{ now()->format('Y-m-d') }}" value="{{ old('birthdate', $userLog?->birthdate?->format('Y-m-d')) }}" class="form-control @error('birthdate') is-invalid @enderror" required>
        @error('birthdate')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Gender</label>
        @php $genderValue = old('gender', $userLog?->gender); @endphp
        <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
            <option value="">— Select —</option>
            @foreach (\App\Models\UserLog::GENDERS as $gender)
                <option value="{{ $gender }}" @selected(strcasecmp((string) $genderValue, $gender) === 0)>{{ $gender }}</option>
            @endforeach
        </select>
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
        <input type="text" name="designation" value="{{ old('designation', $userLog?->designation) }}" class="form-control @error('designation') is-invalid @enderror" required
               maxlength="100" pattern="[\p{L} .'\-]+" title="Letters, spaces, periods, apostrophes and hyphens only" oninput="this.value = this.value.replace(/[^\p{L} .'\-]/gu, '')">
        @error('designation')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label">Role</label>
        <input type="text" name="roles" value="{{ old('roles', $userLog?->roles) }}" class="form-control @error('roles') is-invalid @enderror" required
               maxlength="100" pattern="[\p{L} .'\-]+" title="Letters, spaces, periods, apostrophes and hyphens only" oninput="this.value = this.value.replace(/[^\p{L} .'\-]/gu, '')">
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

<hr class="my-4">

{{--
    Login Account: a separate concept from "Role" above. "Role" is this
    person's position within the barangay (e.g. Chairperson, Council
    Member) and is just text. "Account Type" below is a real RACTS
    system role (DILG Administrator or DILG Monitoring Staff) that
    controls what they can do if they log in to this system.
--}}
@php
    $hasAccount = $userLog?->hasLoginAccount();
@endphp

<div class="mt-2">
    <h2 class="h6 mb-1">Login Account</h2>
    <p class="text-muted small mb-3">
        Optional. Give this person a RACTS account so they can log in themselves &mdash;
        separate from their "Role" above, which is just their position in the barangay.
    </p>

    @unless ($hasAccount)
        <div class="form-check mb-3">
            <input type="checkbox" name="create_account" value="1" id="create-account-toggle" class="form-check-input" @checked(old('create_account'))>
            <label for="create-account-toggle" class="form-check-label">Create a login account for this person</label>
        </div>
    @endunless

    <div id="account-fields" class="row g-3 {{ $hasAccount || old('create_account') ? '' : 'd-none' }}">
        <div class="col-md-4">
            <label class="form-label">Username</label>
            <input type="text" name="username" value="{{ old('username', $userLog?->account?->username) }}" class="form-control @error('username') is-invalid @enderror"
                   minlength="4" maxlength="50" pattern="[A-Za-z0-9._]+" title="Letters, numbers, periods and underscores only (no spaces)"
                   oninput="this.value = this.value.replace(/[^A-Za-z0-9._]/g, '')" autocomplete="off">
            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">{{ $hasAccount ? 'New Password' : 'Password' }}</label>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
            @if ($hasAccount)
                <div class="form-text">Leave blank to keep the current password.</div>
            @endif
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
        </div>
        <div class="col-md-4">
            <label class="form-label">Account Type</label>
            <select name="account_role" class="form-select @error('account_role') is-invalid @enderror">
                <option value="">— Select —</option>
                @foreach (\App\Models\User::ROLES as $value => $label)
                    <option value="{{ $value }}" @selected(old('account_role', $userLog?->account?->role) === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('account_role')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

@unless ($hasAccount)
    <script>
        document.getElementById('create-account-toggle')?.addEventListener('change', function () {
            document.getElementById('account-fields').classList.toggle('d-none', !this.checked);
        });
    </script>
@endunless
