@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
    <div class="row g-3">
        {{-- Identity card --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="d-flex justify-content-center mb-3">
                        @include('profile._avatar', ['user' => $user, 'size' => 96])
                    </div>
                    <h2 class="h5 mb-0">{{ $user->name }}</h2>
                    <div class="text-muted mb-2">{{ $user->position ?: $user->roleLabel() }}</div>
                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ $user->roleLabel() }}</span>
                    @if ($user->isBarangay())
                        <div class="mt-3 small">
                            <i class="bi bi-signpost-2-fill text-secondary"></i>
                            Barangay <strong>{{ $user->barangay?->barangay_name }}</strong>
                        </div>
                    @endif

                    <hr>
                    <dl class="text-start small mb-0">
                        <dt class="text-muted">Username</dt>
                        <dd>{{ $user->username }}</dd>
                        <dt class="text-muted">Contact number</dt>
                        <dd>{{ $user->contact_number ?: '—' }}</dd>
                        <dt class="text-muted">Last sign-in</dt>
                        <dd>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y g:i A') : '—' }}</dd>
                        <dt class="text-muted">Account created</dt>
                        <dd class="mb-0">{{ $user->created_at?->format('M d, Y') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Edit profile --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3"><i class="bi bi-pencil-square text-secondary"></i> Edit Profile</h2>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full name</label>
                                <input type="text" name="name" id="name" maxlength="100"
                                       value="{{ old('name', $user->name) }}" data-input-filter="name"
                                       class="form-control @error('name') is-invalid @enderror" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="position" class="form-label">Position / designation</label>
                                <input type="text" name="position" id="position" maxlength="100"
                                       value="{{ old('position', $user->position) }}" data-input-filter="text"
                                       class="form-control @error('position') is-invalid @enderror"
                                       placeholder="{{ $user->isBarangay() ? 'e.g. Barangay Secretary' : 'e.g. Monitoring Officer' }}">
                                @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_number" class="form-label">Contact number</label>
                                <input type="text" name="contact_number" id="contact_number" maxlength="20"
                                       value="{{ old('contact_number', $user->contact_number) }}" data-input-filter="phone"
                                       class="form-control @error('contact_number') is-invalid @enderror"
                                       placeholder="09171234567">
                                @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="avatar" class="form-label">Profile photo</label>
                                <input type="file" name="avatar" id="avatar" accept="image/png,image/jpeg"
                                       class="form-control @error('avatar') is-invalid @enderror">
                                @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($user->hasAvatar())
                                    <div class="form-check mt-1">
                                        <input type="checkbox" class="form-check-input" name="remove_avatar" id="remove_avatar" value="1">
                                        <label for="remove_avatar" class="form-check-label small">Remove current photo</label>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <p class="small text-muted mt-3 mb-0">
                            Your username and role are managed by the DILG administrator.
                        </p>
                        <button type="submit" class="btn btn-primary mt-3">Save Changes</button>
                    </form>
                </div>
            </div>

            {{-- Change password --}}
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h2 class="h6 mb-3"><i class="bi bi-key text-secondary"></i> Change Password</h2>
                    <form method="POST" action="{{ route('profile.password') }}" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label">Current password</label>
                                <input type="password" name="current_password" id="current_password" maxlength="64"
                                       class="form-control @error('current_password') is-invalid @enderror" autocomplete="current-password" required>
                                @error('current_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">New password</label>
                                <input type="password" name="password" id="password" maxlength="64"
                                       class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label">Confirm new password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" maxlength="64"
                                       class="form-control" autocomplete="new-password" required>
                            </div>
                        </div>
                        <p class="small text-muted mt-2 mb-0">8-64 characters with at least one letter and one number, no spaces.</p>
                        <button type="submit" class="btn btn-outline-primary mt-3">Change Password</button>
                    </form>
                </div>
            </div>

            {{-- Role & limitations --}}
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="h6 mb-3"><i class="bi bi-shield-check text-secondary"></i> Your Role &amp; Limitations &mdash; {{ $user->roleLabel() }}</h2>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="fw-semibold text-success mb-1"><i class="bi bi-check-circle-fill"></i> You can</div>
                            <ul class="small mb-0 ps-3">
                                @foreach ($permissions['can'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="fw-semibold text-danger mb-1"><i class="bi bi-x-circle-fill"></i> You cannot</div>
                            <ul class="small mb-0 ps-3">
                                @foreach ($permissions['cannot'] as $item)
                                    <li>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
