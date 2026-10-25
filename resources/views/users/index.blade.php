@extends('layouts.app')

@section('title', 'User Accounts')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="text-muted mb-0">DILG administrator, monitoring staff, and barangay accounts with access to RACTS.</p>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Add User
        </a>
    </div>

    <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-3">
        <div class="col-md-5">
            <input type="text" name="search" value="{{ $search }}" maxlength="50" class="form-control"
                   placeholder="Search name or username" data-input-filter="text">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-select">
                <option value="">All roles</option>
                @foreach (\App\Models\User::ROLES as $value => $label)
                    <option value="{{ $value }}" @selected($role === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-outline-primary" type="submit">Filter</button>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Barangay</th>
                        <th>Status</th>
                        <th>Last sign-in</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->roleLabel() }}</td>
                            <td>{{ $user->barangay?->barangay_name ?? '—' }}</td>
                            <td>
                                @if ($user->is_active)
                                    <span class="badge bg-success-subtle text-success-emphasis">Active</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Deactivated</span>
                                @endif
                            </td>
                            <td class="small text-nowrap">{{ $user->last_login_at?->format('M d, Y g:i A') ?? 'Never' }}</td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this user account? This cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-0">
                                <div class="racts-empty-state">
                                    <i class="bi bi-people"></i>
                                    No user accounts found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->links() }}
    </div>
@endsection
