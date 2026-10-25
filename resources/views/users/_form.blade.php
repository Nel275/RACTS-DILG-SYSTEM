{{-- Shared fields for users.create / users.edit. $user is null when creating. --}}
@php($user = $user ?? null)

<div class="mb-3">
    <label for="name" class="form-label">Full Name</label>
    <input type="text" name="name" id="name" maxlength="100" data-input-filter="name"
           value="{{ old('name', $user?->name) }}"
           class="form-control @error('name') is-invalid @enderror" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="username" class="form-label">Username</label>
    <input type="text" name="username" id="username" maxlength="30" data-input-filter="username"
           value="{{ old('username', $user?->username) }}"
           class="form-control @error('username') is-invalid @enderror" required>
    <div class="form-text">3-30 characters: letters, numbers, periods, underscores or hyphens. Must start with a letter.</div>
    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="role" class="form-label">Role</label>
    <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
        <option value="" disabled @selected(! old('role', $user?->role))>Select a role</option>
        @foreach (\App\Models\User::ROLES as $value => $label)
            <option value="{{ $value }}" @selected(old('role', $user?->role) === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

{{-- Only for Barangay User accounts: this link is what classifies the
     account to its barangay automatically at login. --}}
<div class="mb-3" id="barangay-field" style="display: none;">
    <label for="barangay_id" class="form-label">Barangay</label>
    <select name="barangay_id" id="barangay_id" class="form-select @error('barangay_id') is-invalid @enderror">
        <option value="">Select a barangay</option>
        @foreach ($barangays as $barangay)
            <option value="{{ $barangay->id }}" @selected((string) old('barangay_id', $user?->barangay_id) === (string) $barangay->id)>
                {{ $barangay->barangay_name }}
            </option>
        @endforeach
    </select>
    <div class="form-text">This account will only ever see this barangay's data.</div>
    @error('barangay_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

@if ($user)
    <div class="mb-3 form-check form-switch">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" class="form-check-input" role="switch" name="is_active" id="is_active" value="1"
               @checked(old('is_active', $user->is_active))>
        <label for="is_active" class="form-check-label">Account is active (untick to block sign-in without deleting the account)</label>
    </div>
@endif

<div class="mb-3">
    <label for="password" class="form-label">Password @if ($user)<span class="text-muted">(leave blank to keep the current one)</span>@endif</label>
    <input type="password" name="password" id="password" maxlength="64" autocomplete="new-password"
           class="form-control @error('password') is-invalid @enderror" @required(! $user)>
    <div class="form-text">8-64 characters with at least one letter and one number, no spaces.</div>
    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="password_confirmation" class="form-label">Confirm Password</label>
    <input type="password" name="password_confirmation" id="password_confirmation" maxlength="64"
           autocomplete="new-password" class="form-control" @required(! $user)>
</div>

<script>
    (function () {
        const role = document.getElementById('role');
        const field = document.getElementById('barangay-field');
        const select = document.getElementById('barangay_id');
        function sync() {
            const isBarangay = role.value === 'barangay';
            field.style.display = isBarangay ? '' : 'none';
            select.required = isBarangay;
            if (!isBarangay) select.value = '';
        }
        role.addEventListener('change', sync);
        sync();
    })();
</script>
