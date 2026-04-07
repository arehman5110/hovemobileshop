@extends('layouts.app')
@section('title','Users')

@push('styles')
<style>
.pro-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e; }
.role-badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700; }
.perm-check { display:flex;align-items:center;gap:6px;padding:4px 0;font-size:12px; }
.perm-check i { font-size:11px; }
</style>
@endpush

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">👥 Users</h2>
        <div class="text-secondary small">Manage staff accounts and roles</div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus me-1"></i>Add User
    </button>
</div>

{{-- Role overview cards --}}
<div class="row g-3 mb-4">
    @foreach(\App\Helpers\UserRole::ROLES as $roleKey => $roleCfg)
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span style="font-size:20px;">{{ $roleCfg['icon'] }}</span>
                <span class="fw-bold" style="font-size:14px;">{{ $roleCfg['label'] }}</span>
                <span class="badge bg-{{ $roleCfg['color'] }} ms-auto">{{ $users->where('role',$roleKey)->count() }}</span>
            </div>
            <div class="text-secondary" style="font-size:11px;">{{ $roleCfg['description'] }}</div>
        </div>
    </div>
    @endforeach
</div>

{{-- Users table --}}
<div class="pro-card">
    <table class="table mb-0 align-middle" style="font-size:13px;">
        <thead style="background:var(--bs-tertiary-bg);">
            <tr>
                <th class="px-4 py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">User</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Role</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Permissions</th>
                <th class="py-3 pe-4 text-end" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
        @php $cfg = $user->roleConfig(); $perms = \App\Helpers\UserRole::permissionsFor($user->role); @endphp
        <tr class="border-top">
            <td class="px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0099ff);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;">
                        {{ strtoupper(substr($user->name,0,1)) }}
                    </div>
                    <div>
                        <div class="fw-semibold">{{ $user->name }}
                            @if($user->id === auth()->id())
                            <span class="badge bg-secondary ms-1" style="font-size:9px;">You</span>
                            @endif
                        </div>
                        <div class="text-secondary" style="font-size:11px;">{{ $user->email }}</div>
                    </div>
                </div>
            </td>
            <td class="py-3">
                <span class="badge bg-{{ $cfg['color'] }} rounded-pill">
                    {{ $cfg['icon'] }} {{ $cfg['label'] }}
                </span>
            </td>
            <td class="py-3" style="max-width:320px;">
                <div class="d-flex flex-wrap gap-1">
                    @foreach(array_slice($perms, 0, 6) as $perm)
                    <span class="badge bg-secondary" style="font-size:10px;opacity:.8;">{{ $perm }}</span>
                    @endforeach
                    @if(count($perms) > 6)
                    <span class="badge bg-secondary" style="font-size:10px;opacity:.6;">+{{ count($perms)-6 }} more</span>
                    @endif
                </div>
            </td>
            <td class="py-3 pe-4 text-end">
                <div class="d-flex gap-1 justify-content-end">
                    <button class="btn btn-sm btn-outline-secondary"
                        onclick="openEditUser({{ $user->id }},'{{ addslashes($user->name) }}','{{ $user->email }}','{{ $user->role }}')"
                        title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    @if($user->id !== auth()->id())
                    <form method="POST" action="{{ route('users.destroy',$user) }}" class="d-inline"
                        onsubmit="return confirm('Delete {{ $user->name }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- Role Permissions Reference --}}
<div class="pro-card mt-4 p-4">
    <h6 class="syne fw-bold mb-3">🔐 Role Permissions Reference</h6>
    <div class="row g-3">
        @foreach(\App\Helpers\UserRole::ROLES as $roleKey => $roleCfg)
        <div class="col-md-3">
            <div class="p-3 rounded-3" style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
                <div class="fw-bold mb-2">
                    <span class="badge bg-{{ $roleCfg['color'] }} me-1">{{ $roleCfg['icon'] }} {{ $roleCfg['label'] }}</span>
                </div>
                @foreach(\App\Helpers\UserRole::permissionsFor($roleKey) as $perm)
                <div class="perm-check text-secondary">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span>{{ $perm }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Add User Modal --}}
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2 text-success"></i>Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="modal-body pt-0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. John Smith">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" class="form-control" required placeholder="john@example.com">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Password *</label>
                            <input type="password" name="password" class="form-control" required minlength="8" placeholder="Min 8 characters">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Role *</label>
                            <select name="role" class="form-select no-ts" required>
                                @foreach(\App\Helpers\UserRole::ROLES as $roleKey => $roleCfg)
                                <option value="{{ $roleKey }}">{{ $roleCfg['icon'] }} {{ $roleCfg['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12" id="add-role-desc" style="margin-top:-8px;">
                            <div class="text-secondary small" style="font-size:11px;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-lg me-1"></i>Create User</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit User Modal --}}
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-2 text-primary"></i>Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="edit-user-form" action="">
                @csrf @method('PUT')
                <div class="modal-body pt-0">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Full Name *</label>
                            <input type="text" name="name" id="edit-user-name" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email *</label>
                            <input type="email" name="email" id="edit-user-email" class="form-control" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" minlength="8" placeholder="Leave blank to keep current">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Role *</label>
                            <select name="role" id="edit-user-role" class="form-select no-ts" required>
                                @foreach(\App\Helpers\UserRole::ROLES as $roleKey => $roleCfg)
                                <option value="{{ $roleKey }}">{{ $roleCfg['icon'] }} {{ $roleCfg['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update User</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
var roleDescriptions = @json(\App\Helpers\UserRole::ROLES);

function openEditUser(id, name, email, role) {
    document.getElementById('edit-user-name').value  = name;
    document.getElementById('edit-user-email').value = email;
    document.getElementById('edit-user-role').value  = role;
    document.getElementById('edit-user-form').action = '/users/' + id;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal')).show();
}
</script>
@endpush