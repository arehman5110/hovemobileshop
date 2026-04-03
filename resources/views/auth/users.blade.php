@extends('layouts.app')
@section('title','Users')
@section('content')
<div class="page-header"><div><h2>👥 Users</h2><p>Manage staff accounts</p></div></div>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header fw-bold">{{ $users->count() }} User(s)</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-secondary"><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th></tr></thead>
                    <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td class="fw-semibold">{{ $u->name }} @if($u->id===auth()->id())<span class="badge bg-primary ms-1" style="font-size:10px;">You</span>@endif</td>
                        <td class="text-secondary small">{{ $u->email }}</td>
                        <td><span class="badge bg-{{ $u->role==='admin'?'warning text-dark':'secondary' }}">{{ ucfirst($u->role) }}</span></td>
                        <td><span class="badge bg-{{ $u->is_active?'success':'danger' }}">{{ $u->is_active?'Active':'Disabled' }}</span></td>
                        <td>
                            <button onclick="openEdit({{ $u->id }},'{{ addslashes($u->name) }}','{{ $u->email }}','{{ $u->role }}',{{ $u->is_active?1:0 }})" class="btn btn-sm btn-outline-secondary">✏️ Edit</button>
                            @if($u->id!==auth()->id())
                            <form method="POST" action="{{ route('users.destroy',$u) }}" class="d-inline" onsubmit="return confirm('Delete user?')">
                                @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑️</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header fw-bold" id="form-title">➕ Add User</div>
            <div class="card-body">
                <form id="user-form" method="POST" action="{{ route('users.store') }}">
                    @csrf<input type="hidden" name="_method" id="u-method" value="POST">
                    <div class="mb-3"><label class="form-label">Name *</label><input type="text" name="name" id="u-name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email *</label><input type="email" name="email" id="u-email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Role *</label><select name="role" id="u-role" class="form-select no-ts" required><option value="staff">Staff</option><option value="admin">Admin</option></select></div>
                    <div class="mb-3"><label class="form-label">Password <span id="pw-hint" class="text-secondary" style="font-size:11px;text-transform:none;">(required)</span></label><input type="password" name="password" id="u-password" class="form-control" placeholder="Min 6 characters"></div>
                    <div class="mb-3" id="active-wrap" style="display:none;"><div class="form-check"><input class="form-check-input" type="checkbox" name="is_active" id="u-active" value="1" checked><label class="form-check-label small" for="u-active">Account Active</label></div></div>
                    <div class="d-flex gap-2"><button type="submit" class="btn btn-primary" id="u-submit">➕ Add User</button><button type="button" onclick="resetForm()" class="btn btn-outline-secondary">Cancel</button></div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header fw-bold">🔑 Change My Password</div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.change') }}">
                    @csrf
                    <div class="mb-3"><label class="form-label">Current Password *</label><input type="password" name="current_password" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">New Password *</label><input type="password" name="new_password" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Confirm New Password *</label><input type="password" name="new_password_confirmation" class="form-control" required></div>
                    <button class="btn btn-primary">🔑 Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function openEdit(id,name,email,role,active){document.getElementById('form-title').textContent='✏️ Edit User';document.getElementById('u-submit').textContent='💾 Update';document.getElementById('u-method').value='PUT';document.getElementById('user-form').action='/users/'+id;document.getElementById('u-name').value=name;document.getElementById('u-email').value=email;document.getElementById('u-role').value=role;document.getElementById('u-password').value='';document.getElementById('u-active').checked=active==1;document.getElementById('active-wrap').style.display='block';document.getElementById('pw-hint').textContent='(leave blank to keep current)';}
function resetForm(){document.getElementById('form-title').textContent='➕ Add User';document.getElementById('u-submit').textContent='➕ Add User';document.getElementById('u-method').value='POST';document.getElementById('user-form').action='{{ route("users.store") }}';document.getElementById('user-form').reset();document.getElementById('active-wrap').style.display='none';document.getElementById('pw-hint').textContent='(required)';}
</script>
@endpush
