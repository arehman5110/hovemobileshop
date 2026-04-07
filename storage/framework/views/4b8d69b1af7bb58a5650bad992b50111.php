<?php $__env->startSection('title','Users'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.pro-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e; }
.user-row { cursor:pointer;transition:background .1s; }
.user-row:hover { background:rgba(13,110,253,.04) !important; }
.perm-group { margin-bottom:16px; }
.perm-group-title { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.5;margin-bottom:8px; }
.perm-item { display:flex;align-items:center;justify-content:space-between;padding:7px 10px;border-radius:8px;margin-bottom:4px;transition:background .1s; }
.perm-item:hover { background:var(--bs-tertiary-bg); }
.perm-label { font-size:12px;font-weight:500; }
.perm-source { font-size:10px;padding:1px 7px;border-radius:10px; }
.perm-from-role { background:rgba(13,110,253,.1);color:#0d6efd; }
.perm-override-on  { background:rgba(25,135,84,.1);color:#198754; }
.perm-override-off { background:rgba(220,53,69,.1);color:#dc3545; }
/* Fix: right column scrollable independently */
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">👥 Users</h2>
        <div class="text-secondary small">Manage staff accounts, roles and individual permissions</div>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus me-1"></i>Add User
    </button>
</div>

<div class="row g-4" style="align-items:start;">

    
    <div class="col-lg-5">
        <div class="pro-card overflow-hidden">
            <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2" style="background:var(--bs-tertiary-bg);">
                <span class="fw-bold" style="font-size:13px;">All Users</span>
                <span class="badge bg-secondary ms-auto"><?php echo e($users->count()); ?></span>
            </div>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $cfg = $user->roleConfig(); ?>
            <div class="user-row border-bottom px-4 py-3 d-flex align-items-center gap-3"
                onclick="selectUser(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>', '<?php echo e($user->email); ?>', '<?php echo e($user->role); ?>')"
                id="user-row-<?php echo e($user->id); ?>">
                <div style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0099ff);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:#fff;flex-shrink:0;">
                    <?php echo e(strtoupper(substr($user->name,0,1))); ?>

                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="fw-semibold" style="font-size:13px;"><?php echo e($user->name); ?>

                        <?php if($user->id === auth()->id()): ?>
                        <span class="badge bg-secondary ms-1" style="font-size:9px;">You</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-secondary" style="font-size:11px;"><?php echo e($user->email); ?></div>
                </div>
                <div class="d-flex align-items-center gap-1 flex-shrink-0">
                    <span class="badge bg-<?php echo e($cfg['color']); ?> rounded-pill" style="font-size:10px;"><?php echo e($cfg['icon']); ?> <?php echo e($cfg['label']); ?></span>
                    <?php $overrideCount = $user->permissions()->count(); ?>
                    <?php if($overrideCount > 0): ?>
                    <span class="badge bg-warning text-dark rounded-pill" style="font-size:10px;" title="<?php echo e($overrideCount); ?> custom permission(s)"><?php echo e($overrideCount); ?> custom</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="col-lg-7">
        
        <div id="perm-empty" class="pro-card p-5 text-center text-secondary">
            <div style="font-size:40px;opacity:.2;margin-bottom:12px;">🔐</div>
            <div class="fw-semibold mb-1">Select a user to edit permissions</div>
            <div class="small">Click any user on the left to manage their individual permissions</div>
        </div>

        
        <div id="perm-editor" style="display:none;">
            <div class="pro-card mb-3 p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div id="pe-avatar" style="width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0099ff);display:flex;align-items:center;justify-content:center;font-size:17px;font-weight:800;color:#fff;flex-shrink:0;"></div>
                    <div class="flex-grow-1">
                        <div class="fw-bold" id="pe-name" style="font-size:15px;"></div>
                        <div class="text-secondary small" id="pe-email"></div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" onclick="openEditUser()">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </button>
                        <button class="btn btn-sm btn-outline-danger" id="pe-delete-btn" onclick="deleteUser()">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>

                
                <div class="d-flex align-items-center gap-2 p-3 rounded-3" style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">
                    <span class="text-secondary small fw-semibold">Role:</span>
                    <form method="POST" id="role-form" action="" class="d-flex align-items-center gap-2 flex-grow-1">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <select name="role" class="form-select form-select-sm no-ts flex-grow-1" onchange="this.closest('form').submit()" id="pe-role-select">
                            <?php $__currentLoopData = \App\Helpers\UserRole::ROLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $roleCfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($roleKey); ?>"><?php echo e($roleCfg['icon']); ?> <?php echo e($roleCfg['label']); ?> — <?php echo e($roleCfg['description']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </form>
                    <span class="text-secondary small">→ sets defaults below</span>
                </div>
            </div>

            
            <div class="pro-card p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div>
                        <div class="fw-bold" style="font-size:14px;">🔐 Individual Permissions</div>
                        <div class="text-secondary" style="font-size:11px;">Override role defaults for this user specifically</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="checkAll(true)">Grant All</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="checkAll(false)">Deny All</button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetToRole()">Reset to Role</button>
                    </div>
                </div>

                <form method="POST" id="permissions-form" action="">
                    <?php echo csrf_field(); ?>
                    <?php
                        $permGroups = [];
                        foreach(\App\Helpers\UserRole::PERMISSIONS as $perm => $roles) {
                            $group = explode('.', $perm)[0];
                            $permGroups[$group][] = $perm;
                        }
                    ?>

                    <?php $__currentLoopData = $permGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $perms): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="perm-group">
                        <div class="perm-group-title"><?php echo e(ucfirst(str_replace('-', ' ', $group))); ?></div>
                        <?php $__currentLoopData = $perms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="perm-item">
                            <div>
                                <div class="perm-label"><?php echo e($perm); ?></div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="perm-source" id="src-<?php echo e(str_replace(['.'], '-', $perm)); ?>"></span>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input perm-toggle" type="checkbox"
                                        name="permissions[]"
                                        value="<?php echo e($perm); ?>"
                                        id="perm-<?php echo e(str_replace(['.'], '-', $perm)); ?>"
                                        onchange="updatePermSource('<?php echo e($perm); ?>', this.checked)">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <div class="mt-3 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i>Save Permissions
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetToRole()">
                            Reset to Role Defaults
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2 text-success"></i>Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('users.store')); ?>">
                <?php echo csrf_field(); ?>
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
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Role *</label>
                            <select name="role" class="form-select no-ts" required>
                                <?php $__currentLoopData = \App\Helpers\UserRole::ROLES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleKey => $roleCfg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($roleKey); ?>"><?php echo e($roleCfg['icon']); ?> <?php echo e($roleCfg['label']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
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


<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil me-2 text-primary"></i>Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="edit-user-form" action="">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
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
                        <div class="col-12">
                            <label class="form-label">New Password <span class="text-secondary fw-normal">(leave blank to keep current)</span></label>
                            <input type="password" name="password" class="form-control" minlength="8">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i>Update</button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


<form method="POST" id="delete-user-form" action="" style="display:none;">
    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
</form>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// All permissions data from PHP
var allPermissions  = <?php echo json_encode(array_keys(\App\Helpers\UserRole::PERMISSIONS), 15, 512) ?>;
var rolePermissions = <?php echo json_encode(\App\Helpers\UserRole::PERMISSIONS, 15, 512) ?>;
<?php
$usersData = $users->map(function($u) {
    $overrides = [];
    foreach($u->permissions()->get(['permission','granted']) as $p) {
        $overrides[$p->permission] = (bool)$p->granted;
    }
    return [
        'id'        => $u->id,
        'name'      => $u->name,
        'email'     => $u->email,
        'role'      => $u->role,
        'overrides' => $overrides,
    ];
})->values()->toArray();
?>
var allUsers = <?php echo json_encode($usersData); ?>;

var currentUserId   = null;
var currentUserRole = null;

function selectUser(userId, name, email, role) {
    currentUserId   = userId;
    currentUserRole = role;

    // Highlight row
    document.querySelectorAll('.user-row').forEach(r => r.style.background = '');
    var row = document.getElementById('user-row-' + userId);
    if (row) row.style.background = 'rgba(13,110,253,.06)';

    // Show editor
    document.getElementById('perm-empty').style.display  = 'none';
    document.getElementById('perm-editor').style.display = 'block';

    // Fill header
    document.getElementById('pe-avatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('pe-name').textContent   = name;
    document.getElementById('pe-email').textContent  = email;

    // Set role selector
    var roleSelect = document.getElementById('pe-role-select');
    roleSelect.value = role;
    document.getElementById('role-form').action = '/users/' + userId + '/role';

    // Set delete button
    var currentAuthId = <?php echo e(auth()->id()); ?>;
    document.getElementById('pe-delete-btn').style.display =
        (userId === currentAuthId) ? 'none' : '';
    document.getElementById('delete-user-form').action = '/users/' + userId;

    // Set permissions form action
    document.getElementById('permissions-form').action = '/users/' + userId + '/permissions';

    // Get this user's overrides
    var userData = allUsers.find(u => u.id === userId);
    var overrides = userData ? userData.overrides : {};

    // Fill checkboxes
    allPermissions.forEach(function(perm) {
        var permId   = 'perm-' + perm.replace(/\./g, '-');
        var checkbox = document.getElementById(permId);
        if (!checkbox) return;

        var roleHasPerm = (rolePermissions[perm] || []).includes(role);

        // Check if override exists
        if (overrides.hasOwnProperty(perm)) {
            checkbox.checked = overrides[perm];
        } else {
            checkbox.checked = roleHasPerm;
        }

        updatePermSource(perm, checkbox.checked, role, overrides);
    });
}

function updatePermSource(perm, isChecked, forceRole, forceOverrides) {
    var role      = forceRole || currentUserRole;
    var overrides = forceOverrides || null;
    var srcId     = 'src-' + perm.replace(/\./g, '-');
    var srcEl     = document.getElementById(srcId);
    if (!srcEl) return;

    var roleHasPerm = (rolePermissions[perm] || []).includes(role);

    // Determine source label
    var userData  = allUsers.find(u => u.id === currentUserId);
    var userOverrides = overrides || (userData ? userData.overrides : {});
    var hasOverride = userOverrides && userOverrides.hasOwnProperty(perm);

    if (hasOverride) {
        if (isChecked) {
            srcEl.className = 'perm-source perm-override-on';
            srcEl.textContent = '✓ Custom';
        } else {
            srcEl.className = 'perm-source perm-override-off';
            srcEl.textContent = '✕ Denied';
        }
    } else if (isChecked) {
        srcEl.className = 'perm-source perm-from-role';
        srcEl.textContent = 'From role';
    } else {
        srcEl.className = 'perm-source';
        srcEl.style.opacity = '0.4';
        srcEl.textContent = 'Denied';
    }
}

function checkAll(grant) {
    document.querySelectorAll('.perm-toggle').forEach(function(cb) {
        cb.checked = grant;
        var perm = cb.value;
        updatePermSource(perm, grant);
    });
}

function resetToRole() {
    var role = currentUserRole;
    allPermissions.forEach(function(perm) {
        var permId   = 'perm-' + perm.replace(/\./g, '-');
        var checkbox = document.getElementById(permId);
        if (!checkbox) return;
        var roleHasPerm = (rolePermissions[perm] || []).includes(role);
        checkbox.checked = roleHasPerm;
        // Remove override indicator
        var srcId = 'src-' + perm.replace(/\./g, '-');
        var srcEl = document.getElementById(srcId);
        if (srcEl) {
            srcEl.className   = roleHasPerm ? 'perm-source perm-from-role' : 'perm-source';
            srcEl.textContent = roleHasPerm ? 'From role' : 'Denied';
            srcEl.style.opacity = roleHasPerm ? '1' : '0.4';
        }
    });
}

function openEditUser() {
    var userData = allUsers.find(u => u.id === currentUserId);
    if (!userData) return;
    document.getElementById('edit-user-name').value  = userData.name;
    document.getElementById('edit-user-email').value = userData.email;
    document.getElementById('edit-user-form').action = '/users/' + currentUserId;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editUserModal')).show();
}

function deleteUser() {
    var userData = allUsers.find(u => u.id === currentUserId);
    if (!userData) return;
    if (confirm('Delete user ' + userData.name + '? This cannot be undone.')) {
        document.getElementById('delete-user-form').submit();
    }
}

// Auto-select first user if available
document.addEventListener('DOMContentLoaded', function() {
    if (allUsers && allUsers.length > 0) {
        var first = allUsers[0];
        setTimeout(function() {
            selectUser(first.id, first.name, first.email, first.role);
        }, 100);
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/auth/users.blade.php ENDPATH**/ ?>