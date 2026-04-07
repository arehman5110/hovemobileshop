<?php $__env->startSection('title','Jobs'); ?>
<?php $__env->startSection('topbar-actions'); ?>
<a href="<?php echo e(route('jobs.create')); ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Job</a>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* ── Cards ─────────────────────────────────────────────────────── */
.pro-card {
    background:#fff;border:none;border-radius:14px;
    box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);
}
[data-bs-theme="dark"] .pro-card {
    background:#1c1c1e;
    box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4);
}

/* ── Stat cards ─────────────────────────────────────────────────── */
.stat-card { padding:18px 20px;border-radius:14px;transition:transform .2s,box-shadow .2s; }
.stat-card:hover { transform:translateY(-2px);box-shadow:0 6px 24px rgba(0,0,0,.12); }
.stat-val { font-family:'Syne',sans-serif;font-size:30px;font-weight:800;line-height:1;margin:4px 0 2px; }
.stat-icon { width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }

/* ── Filter card ────────────────────────────────────────────────── */
.filter-card { padding:14px 16px;border-radius:14px; }

/* ── Active filters bar ─────────────────────────────────────────── */
.active-filters {
    display:flex;align-items:center;gap:8px;flex-wrap:wrap;
    padding:10px 16px;
    background:rgba(13,110,253,.04);
    border-radius:10px;
    border:1px solid rgba(13,110,253,.1);
    font-size:12px;
}
[data-bs-theme="dark"] .active-filters {
    background:rgba(13,110,253,.08);
    border-color:rgba(13,110,253,.2);
}
.filter-tag {
    display:inline-flex;align-items:center;gap:4px;
    padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
}
<?php foreach(\App\Helpers\JobStatus::STATUSES as $s => $cfg):
    $cls = 'filter-tag-status-'.strtolower(str_replace(' ','-',$s));
?>
.<?php echo e($cls); ?> { background:<?php echo e($cfg['bg']); ?>;color:<?php echo e($cfg['color']); ?>; }
<?php endforeach; ?>

/* ── Table ──────────────────────────────────────────────────────── */
.table-card { border-radius:14px;overflow:hidden; }
.job-row { cursor:pointer;transition:background .15s; }
.job-row:hover { background:rgba(13,110,253,.05) !important; }
[data-bs-theme="dark"] .job-row:hover { background:rgba(13,110,253,.1) !important; }
/* Selected/expanded row — amber/orange tint so it's clearly different from hover */
.job-row.expanded {
    background:rgba(255,152,0,.08) !important;
    border-left:3px solid #ff9800;
    box-shadow:inset 0 0 0 1px rgba(255,152,0,.15);
}
[data-bs-theme="dark"] .job-row.expanded {
    background:rgba(255,152,0,.12) !important;
}

/* ── Expand row ─────────────────────────────────────────────────── */
.expand-row { display:none; }
.expand-row.show { display:table-row; }
.expand-cell { padding:0 !important;border-top:none !important; }
.expand-inner {
    padding:20px 28px 22px;
    background:rgba(255,152,0,.04);
    border-bottom:2px solid rgba(255,152,0,.25);
    border-left:3px solid #ff9800;
}
[data-bs-theme="dark"] .expand-inner {
    background:rgba(255,152,0,.06);
}
.expand-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
    gap:16px;
    margin-bottom:16px;
}
.expand-section { display:flex;flex-direction:column;gap:4px; }
.expand-lbl {
    font-size:10px;font-weight:700;text-transform:uppercase;
    letter-spacing:.07em;opacity:.45;margin-bottom:3px;
}
.expand-val { font-size:13px;font-weight:500; }
.expand-sub { font-size:11px;color:var(--bs-secondary-color); }
.expand-divider { border-top:1px solid var(--bs-border-color);margin:14px 0 12px; }
.expand-actions { display:flex;gap:8px;flex-wrap:wrap; }

/* ── Chevron ────────────────────────────────────────────────────── */
.row-chevron { transition:transform .22s;font-size:11px;opacity:.35;display:block; }
.job-row.expanded .row-chevron { transform:rotate(180deg);opacity:.7; }

/* ── Results bar ────────────────────────────────────────────────── */
.results-bar {
    display:flex;align-items:center;justify-content:space-between;
    padding:12px 20px;
    border-bottom:1px solid var(--bs-border-color);
    font-size:12px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $selectedStatuses = request()->has('status')
        ? (array)request('status')
        : ['In Progress','Waiting Parts'];
    $allStatuses = \App\Models\Job::statuses();
    $isAll       = count(array_intersect($selectedStatuses,$allStatuses)) === count($allStatuses);
    $hasSearch   = request()->filled('search');
    $hasDateFrom = request()->filled('date_from');
    $hasDateTo   = request()->filled('date_to');
    $hasFilters  = $hasSearch || $hasDateFrom || $hasDateTo || (!$isAll);

    $statsTotal   = \App\Models\Job::count();
    $statsInProg  = \App\Models\Job::where('status','In Progress')->count();
    $statsWaiting = \App\Models\Job::where('status','Waiting Parts')->count();
    $statsDone    = \App\Models\Job::where('status','Completed')->count();
    $statsCancelled = \App\Models\Job::where('status','Cancelled')->count();
?>


<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h2 class="syne mb-0" style="font-size:24px;font-weight:800;">🔧 Repair Jobs</h2>
        <div class="text-secondary" style="font-size:13px;margin-top:2px;">
            Manage and track all customer repair jobs
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('jobs.create')); ?>" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>New Job
        </a>
    </div>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="pro-card stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="text-secondary" style="font-size:12px;">Total Jobs</div>
                    <div class="stat-val"><?php echo e($statsTotal); ?></div>
                    <div class="text-secondary" style="font-size:11px;">All time</div>
                </div>
                <div class="stat-icon" style="background:rgba(13,110,253,.1);color:#0d6efd;">🗂️</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="text-secondary" style="font-size:12px;">In Progress</div>
                    <div class="stat-val text-primary"><?php echo e($statsInProg); ?></div>
                    <div class="text-secondary" style="font-size:11px;">Active now</div>
                </div>
                <div class="stat-icon" style="background:rgba(13,110,253,.1);color:#0d6efd;">🔧</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="text-secondary" style="font-size:12px;">Waiting Parts</div>
                    <div class="stat-val text-warning"><?php echo e($statsWaiting); ?></div>
                    <div class="text-secondary" style="font-size:11px;">On hold</div>
                </div>
                <div class="stat-icon" style="background:rgba(255,193,7,.12);color:#cc9a00;">⏳</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="text-secondary" style="font-size:12px;">Completed</div>
                    <div class="stat-val text-success"><?php echo e($statsDone); ?></div>
                    <div class="text-secondary" style="font-size:11px;">All time</div>
                </div>
                <div class="stat-icon" style="background:rgba(25,135,84,.1);color:#198754;">✅</div>
            </div>
        </div>
    </div>
</div>


<div class="pro-card filter-card mb-3">
    <form method="GET" id="filter-form">
        <div class="row g-2 align-items-end">

            
            <div class="col-12 col-sm-4 col-md-3">
                <label class="form-label">Search</label>
                <div class="position-relative">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search-input" class="form-control"
                            value="<?php echo e(request('search')); ?>"
                            placeholder="Customer name or phone..."
                            autocomplete="off">
                    </div>
                    
                    <div id="search-suggestions"
                        style="display:none;position:absolute;top:100%;left:0;right:0;z-index:1050;
                               background:var(--bs-body-bg);border:1px solid var(--bs-border-color);
                               border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.15);
                               max-height:220px;overflow-y:auto;margin-top:2px;">
                    </div>
                </div>
            </div>

            
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label">Status</label>
                <div class="dropdown">
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start d-flex align-items-center justify-content-between"
                        style="font-size:12px;"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <span id="status-dd-label">
                            <?php if($isAll): ?> All Statuses
                            <?php elseif(count($selectedStatuses)===1): ?> <?php echo e($selectedStatuses[0]); ?>

                            <?php else: ?> <?php echo e(count($selectedStatuses)); ?> selected
                            <?php endif; ?>
                        </span>
                        <i class="bi bi-chevron-down" style="font-size:10px;"></i>
                    </button>
                    <ul class="dropdown-menu p-2" style="min-width:190px;">
                        <li>
                            <label class="dropdown-item rounded d-flex align-items-center gap-2 py-2" style="cursor:pointer;">
                                <input type="checkbox" class="form-check-input m-0" id="chk-all"
                                    <?php echo e($isAll ? 'checked' : ''); ?> onchange="toggleAll()">
                                <span class="fw-semibold" style="font-size:12px;">All Statuses</span>
                            </label>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <?php $__currentLoopData = \App\Models\Job::statuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $cfg = \App\Helpers\JobStatus::config($st); $cls=''; $ic=$cfg['icon']; ?>
                        <li>
                            <label class="dropdown-item rounded d-flex align-items-center gap-2 py-2" style="cursor:pointer;">
                                <input type="checkbox" class="form-check-input m-0 status-chk" value="<?php echo e($st); ?>"
                                    <?php echo e(in_array($st,$selectedStatuses)?'checked':''); ?> onchange="updateStatusDropdown()">
                                <span class="<?php echo e($cls); ?>" style="font-size:12px;"><?php echo e($ic); ?> <?php echo e($st); ?></span>
                            </label>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li class="px-2"><button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Apply</button></li>
                    </ul>
                </div>
                <div id="status-inputs">
                    <?php $__currentLoopData = $selectedStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><input type="hidden" name="status[]" value="<?php echo e($s); ?>"><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm auto-submit" value="<?php echo e(request('date_from')); ?>">
            </div>

            
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm auto-submit" value="<?php echo e(request('date_to')); ?>">
            </div>

            
            <div class="col-auto">
                <a href="<?php echo e(route('jobs.index')); ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg me-1"></i>Clear
                </a>
            </div>
        </div>
    </form>
</div>


<div class="active-filters mb-3">
    <span style="font-weight:700;opacity:.6;font-size:11px;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;">Showing:</span>

    
    <?php if($isAll): ?>
        <span class="filter-tag" style="background:rgba(108,117,125,.1);color:var(--bs-secondary-color);">🗂️ All Statuses</span>
    <?php else: ?>
        <?php $__currentLoopData = $selectedStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $tagCls  = 'filter-tag-status-'.strtolower(str_replace(' ','-',$s));
            $tagIcon = \App\Helpers\JobStatus::config($s)['icon'];
        ?>
        <span class="filter-tag <?php echo e($tagCls); ?>"><?php echo e($tagIcon); ?> <?php echo e($s); ?></span>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php if($hasSearch): ?>
    <span class="filter-tag" style="background:rgba(13,110,253,.08);color:#0d6efd;">
        <i class="bi bi-search" style="font-size:10px;"></i> "<?php echo e(request('search')); ?>"
    </span>
    <?php endif; ?>

    <?php if($hasDateFrom): ?>
    <span class="filter-tag" style="background:rgba(102,16,242,.08);color:#6610f2;">
        <i class="bi bi-calendar" style="font-size:10px;"></i> From <?php echo e(\Carbon\Carbon::parse(request('date_from'))->format('d M Y')); ?>

    </span>
    <?php endif; ?>

    <?php if($hasDateTo): ?>
    <span class="filter-tag" style="background:rgba(102,16,242,.08);color:#6610f2;">
        <i class="bi bi-calendar" style="font-size:10px;"></i> To <?php echo e(\Carbon\Carbon::parse(request('date_to'))->format('d M Y')); ?>

    </span>
    <?php endif; ?>

    <?php if($hasFilters): ?>
    <a href="<?php echo e(route('jobs.index')); ?>" class="ms-auto text-secondary text-decoration-none d-flex align-items-center gap-1" style="font-size:11px;white-space:nowrap;">
        <i class="bi bi-x-circle"></i> Clear filters
    </a>
    <?php endif; ?>
</div>


<div class="pro-card table-card">

    
    <div class="results-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold" style="font-size:13px;">
                <?php echo e($jobs->total()); ?> job<?php echo e($jobs->total()!==1?'s':''); ?> found
            </span>
            <?php if($jobs->total() > 0): ?>
            <span class="text-secondary">· page <?php echo e($jobs->currentPage()); ?> of <?php echo e($jobs->lastPage()); ?></span>
            <?php endif; ?>
        </div>
        <div class="text-secondary d-flex align-items-center gap-1" style="font-size:11px;">
            <i class="bi bi-info-circle"></i> Click a row to expand details
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mb-0 align-middle" style="font-size:13px;">
            <thead style="background:var(--bs-tertiary-bg);">
                <tr>
                    <th style="width:36px;border:none;padding:10px 12px;"></th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">#</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Customer</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Devices</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Date In</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Status</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Location</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Total</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Balance</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 16px 10px 8px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $jobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

            
            <tr class="job-row border-top" id="row-<?php echo e($job->id); ?>" onclick="toggleExpand(<?php echo e($job->id); ?>,this)">
                <td style="padding:12px 12px;text-align:center;width:36px;">
                    <i class="bi bi-chevron-down row-chevron"></i>
                </td>
                <td style="padding:12px 8px;">
                    <span class="text-secondary fw-semibold">#<?php echo e($job->id); ?></span>
                </td>
                <td style="padding:12px 8px;">
                    <div class="fw-semibold" style="line-height:1.3;"><?php echo e($job->customer->name); ?></div>
                    <?php if($job->customer->phone): ?>
                    <div class="text-secondary" style="font-size:11px;"><?php echo e($job->customer->phone); ?></div>
                    <?php endif; ?>
                </td>
                <td style="padding:12px 8px;">
                    <?php $__currentLoopData = $job->devices->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div style="line-height:1.4;">📱 <?php echo e($d->name); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php if($job->devices->count() > 2): ?>
                    <div class="text-secondary" style="font-size:11px;">+<?php echo e($job->devices->count()-2); ?> more</div>
                    <?php endif; ?>
                </td>
                <td style="padding:12px 8px;" class="text-secondary">
                    <?php echo e($job->date_in->format('d M Y')); ?>

                    <?php if($job->date_out): ?>
                    <div style="font-size:11px;">Out: <?php echo e($job->date_out->format('d M')); ?></div>
                    <?php endif; ?>
                </td>
                <td style="padding:12px 8px;">
                    <?php echo $__env->make('jobs._status_badge', ['status' => $job->status], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </td>
                <td style="padding:12px 8px;">
                    <?php if(($job->device_location ?? 'With Us') === 'With Us'): ?>
                    <span class="badge bg-primary rounded-pill" style="font-size:10px;">📦 Us</span>
                    <?php else: ?>
                    <span class="badge bg-warning text-dark rounded-pill" style="font-size:10px;">👤 Customer</span>
                    <?php endif; ?>
                </td>
                <td style="padding:12px 8px;" class="fw-semibold">
                    £<?php echo e(number_format($job->totalAfterDiscount(),2)); ?>

                    <?php if($job->discount_type): ?>
                    <div class="text-success" style="font-size:10px;">
                        <?php if($job->discount_type==='percent'): ?> -<?php echo e($job->discount_value); ?>%
                        <?php else: ?> -£<?php echo e(number_format($job->discount_value,2)); ?>

                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </td>
                <td style="padding:12px 8px;">
                    <?php if($job->isPaidInFull()): ?>
                    <span class="s-badge s-completed"><i class="bi bi-check"></i> Paid</span>
                    <?php elseif($job->totalPaid() > 0): ?>
                    <div class="text-danger fw-bold">£<?php echo e(number_format($job->balanceDue(),2)); ?></div>
                    <div class="text-secondary" style="font-size:10px;">Partial</div>
                    <?php else: ?>
                    <span class="text-danger fw-bold">£<?php echo e(number_format($job->balanceDue(),2)); ?></span>
                    <?php endif; ?>
                </td>
                <td style="padding:12px 16px 12px 8px;text-align:right;" onclick="event.stopPropagation()">
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="<?php echo e(route('jobs.show',$job)); ?>" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                        <a href="<?php echo e(route('jobs.edit',$job)); ?>" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="<?php echo e(route('jobs.destroy',$job)); ?>" class="d-inline" onsubmit="return confirm('Delete job #<?php echo e($job->id); ?>?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>

            
            <tr class="expand-row" id="expand-<?php echo e($job->id); ?>">
                <td colspan="10" class="expand-cell">
                    <div class="expand-inner">
                        <div class="expand-grid">

                            
                            <div class="expand-section">
                                <span class="expand-lbl">👤 Customer</span>
                                <span class="expand-val"><?php echo e($job->customer->name); ?></span>
                                <?php if($job->customer->phone): ?>
                                <span class="expand-sub"><i class="bi bi-telephone me-1"></i><?php echo e($job->customer->phone); ?></span>
                                <?php endif; ?>
                                <?php if($job->customer->email): ?>
                                <span class="expand-sub"><i class="bi bi-envelope me-1"></i><?php echo e($job->customer->email); ?></span>
                                <?php endif; ?>
                            </div>

                            
                            <div class="expand-section">
                                <span class="expand-lbl">📱 Devices & Repairs</span>
                                <?php $__currentLoopData = $job->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div style="margin-bottom:4px;">
                                    <span class="expand-val"><?php echo e($device->name); ?></span>
                                    <?php if($device->imei): ?>
                                    <div class="expand-sub">IMEI: <?php echo e($device->imei); ?></div>
                                    <?php endif; ?>
                                    <?php $__currentLoopData = $device->repairItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ri): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($ri->repairType): ?>
                                    <div class="expand-sub">↳ <?php echo e($ri->repairType->icon ?? '🔧'); ?> <?php echo e($ri->repairType->name); ?></div>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            
                            <div class="expand-section">
                                <span class="expand-lbl">📅 Timeline</span>
                                <span class="expand-val"><i class="bi bi-box-arrow-in-right me-1 text-primary"></i><?php echo e($job->date_in->format('d M Y')); ?></span>
                                <?php if($job->date_out): ?>
                                <span class="expand-sub"><i class="bi bi-box-arrow-right me-1"></i><?php echo e($job->date_out->format('d M Y')); ?></span>
                                <?php $days = $job->date_in->diffInDays($job->date_out); ?>
                                <span class="expand-sub">Duration: <?php echo e($days); ?> day<?php echo e($days!=1?'s':''); ?></span>
                                <?php else: ?>
                                <span class="text-warning expand-sub"><i class="bi bi-clock me-1"></i>In progress since <?php echo e($job->date_in->diffForHumans()); ?></span>
                                <?php endif; ?>
                            </div>

                            
                            <div class="expand-section">
                                <span class="expand-lbl">📍 Device Location</span>
                                <?php if(($job->device_location ?? 'With Us') === 'With Us'): ?>
                                <span class="expand-val"><span class="badge bg-primary" style="font-size:11px;">📦 With Us</span></span>
                                <?php else: ?>
                                <span class="expand-val"><span class="badge bg-warning text-dark" style="font-size:11px;">👤 With Customer</span></span>
                                <?php endif; ?>
                            </div>

                            
                            <div class="expand-section">
                                <span class="expand-lbl">💳 Payments</span>
                                <?php if($job->payments->isEmpty()): ?>
                                <span class="text-warning expand-sub">No payments recorded</span>
                                <?php else: ?>
                                <?php $__currentLoopData = $job->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pmt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $isSplit = $pmt->notes && str_starts_with(trim($pmt->notes),'['); ?>
                                <span class="expand-val">£<?php echo e(number_format($pmt->amount,2)); ?> <span class="expand-sub"><?php echo e($isSplit?'✂️ Split':$pmt->payment_type); ?></span></span>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>

                            
                            <div class="expand-section">
                                <span class="expand-lbl">💰 Financial</span>
                                <span class="expand-val">Total: £<?php echo e(number_format($job->totalAfterDiscount(),2)); ?></span>
                                <?php if($job->discount_type): ?>
                                <span class="text-success expand-sub">Discount: <?php echo e($job->discount_type==='percent'?$job->discount_value.'%':'£'.number_format($job->discount_value,2)); ?> off</span>
                                <?php endif; ?>
                                <?php if($job->voucher_code): ?>
                                <span class="text-success expand-sub">🎟️ <?php echo e($job->voucher_code); ?> applied</span>
                                <?php endif; ?>
                                <span class="expand-sub">Paid: £<?php echo e(number_format($job->totalPaid(),2)); ?></span>
                                <?php if($job->isPaidInFull()): ?>
                                <span class="text-success fw-bold expand-sub">✅ Fully Paid</span>
                                <?php else: ?>
                                <span class="text-danger fw-bold expand-sub">Balance: £<?php echo e(number_format($job->balanceDue(),2)); ?></span>
                                <?php endif; ?>
                            </div>

                            
                            <?php if($job->notes): ?>
                            <div class="expand-section">
                                <span class="expand-lbl">📋 Notes</span>
                                <span class="expand-val" style="font-size:12px;line-height:1.5;"><?php echo e(Str::limit($job->notes,150)); ?></span>
                            </div>
                            <?php endif; ?>

                        </div>

                        <div class="expand-divider"></div>

                        
                        <div class="expand-actions">
                            <a href="<?php echo e(route('jobs.show',$job)); ?>" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>Full Details
                            </a>
                            <a href="<?php echo e(route('jobs.edit',$job)); ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <?php if(!$job->isPaidInFull()): ?>
                            <a href="<?php echo e(route('jobs.show',$job)); ?>" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-credit-card me-1"></i>Add Payment
                            </a>
                            <?php endif; ?>
                            <?php if($job->status !== 'Completed'): ?>
                            <form method="POST" action="<?php echo e(route('jobs.update-status',$job)); ?>" class="d-inline" onclick="event.stopPropagation()">
                                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                <input type="hidden" name="status" value="Completed">
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-check-circle me-1"></i>Mark Complete
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div style="font-size:40px;opacity:.2;margin-bottom:12px;">🔧</div>
                    <div class="fw-semibold mb-1">No jobs found</div>
                    <div class="text-secondary small mb-3">
                        <?php if($hasFilters): ?> Try adjusting your filters <?php else: ?> No repair jobs yet <?php endif; ?>
                    </div>
                    <?php if($hasFilters): ?>
                    <a href="<?php echo e(route('jobs.index')); ?>" class="btn btn-outline-secondary btn-sm me-2">Clear Filters</a>
                    <?php endif; ?>
                    <a href="<?php echo e(route('jobs.create')); ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Create First Job</a>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if($jobs->hasPages()): ?>
    <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="text-secondary" style="font-size:12px;">
            Showing <?php echo e($jobs->firstItem()); ?>–<?php echo e($jobs->lastItem()); ?> of <?php echo e($jobs->total()); ?> jobs
        </div>
        <div><?php echo e($jobs->links()); ?></div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Status dropdown ───────────────────────────────────────────────
var allStatuses = <?php echo json_encode(\App\Models\Job::statuses()); ?>;

function updateStatusDropdown() {
    var checked = Array.from(document.querySelectorAll('.status-chk:checked')).map(function(c){return c.value;});
    var allChk  = document.getElementById('chk-all');
    allChk.checked = (checked.length === allStatuses.length);

    var container = document.getElementById('status-inputs');
    container.innerHTML = '';
    (allChk.checked ? allStatuses : checked).forEach(function(s) {
        var inp = document.createElement('input');
        inp.type='hidden'; inp.name='status[]'; inp.value=s;
        container.appendChild(inp);
    });

    var label = document.getElementById('status-dd-label');
    if (allChk.checked) { label.textContent = 'All Statuses'; }
    else if (checked.length === 0) { label.textContent = 'Select...'; }
    else if (checked.length === 1) { label.textContent = checked[0]; }
    else { label.textContent = checked.length + ' selected'; }
}

function toggleAll() {
    var isOn = document.getElementById('chk-all').checked;
    document.querySelectorAll('.status-chk').forEach(function(c){ c.checked = isOn; });
    updateStatusDropdown();
}

// Auto-submit when status Apply clicked (already handled by button type=submit)
// Auto-submit on date change
document.querySelectorAll('.auto-submit').forEach(function(el) {
    el.addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });
});

// ── Live search suggestions ───────────────────────────────────────
var searchInput       = document.getElementById('search-input');
var suggestionsBox    = document.getElementById('search-suggestions');
var searchTimer       = null;
var selectedSuggIdx   = -1;
var currentSuggestions = [];

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimer);
    var q = this.value.trim();
    if (q.length < 2) {
        hideSuggestions();
        return;
    }
    searchTimer = setTimeout(function() { fetchSuggestions(q); }, 250);
});

searchInput.addEventListener('keydown', function(e) {
    var items = suggestionsBox.querySelectorAll('.sugg-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedSuggIdx = Math.min(selectedSuggIdx + 1, items.length - 1);
        highlightSugg(items);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedSuggIdx = Math.max(selectedSuggIdx - 1, -1);
        highlightSugg(items);
    } else if (e.key === 'Enter') {
        if (selectedSuggIdx >= 0 && items[selectedSuggIdx]) {
            e.preventDefault();
            items[selectedSuggIdx].click();
        } else {
            hideSuggestions();
            document.getElementById('filter-form').submit();
        }
    } else if (e.key === 'Escape') {
        hideSuggestions();
    }
});

// Close suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
        hideSuggestions();
    }
});

function fetchSuggestions(q) {
    fetch('<?php echo e(route("jobs.index")); ?>?search_suggest=1&q=' + encodeURIComponent(q), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { renderSuggestions(data, q); })
    .catch(function() { hideSuggestions(); });
}

function renderSuggestions(items, q) {
    currentSuggestions = items;
    selectedSuggIdx = -1;
    suggestionsBox.innerHTML = '';

    if (!items || items.length === 0) {
        suggestionsBox.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:var(--bs-secondary-color);">No customers found</div>';
        suggestionsBox.style.display = 'block';
        return;
    }

    items.forEach(function(item) {
        var div = document.createElement('div');
        div.className = 'sugg-item';
        div.style.cssText = 'padding:9px 14px;cursor:pointer;display:flex;align-items:center;gap:10px;font-size:13px;border-bottom:1px solid var(--bs-border-color);transition:background .1s;';
        div.onmouseenter = function() { div.style.background = 'rgba(13,110,253,.08)'; };
        div.onmouseleave = function() { div.style.background = ''; };

        // Highlight matching text
        var name  = highlightMatch(item.name, q);
        var phone = item.phone ? highlightMatch(item.phone, q) : '';

        div.innerHTML =
            '<span style="width:30px;height:30px;border-radius:50%;background:rgba(13,110,253,.12);color:#0d6efd;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">'
            + item.name.charAt(0).toUpperCase()
            + '</span>'
            + '<div style="min-width:0;">'
            + '<div class="fw-semibold" style="line-height:1.2;">' + name + '</div>'
            + (phone ? '<div style="font-size:11px;color:var(--bs-secondary-color);">' + phone + '</div>' : '')
            + '<div style="font-size:10px;color:var(--bs-secondary-color);">' + item.jobs_count + ' job' + (item.jobs_count !== 1 ? 's' : '') + '</div>'
            + '</div>';

        div.onclick = function() {
            searchInput.value = item.name;
            hideSuggestions();
            document.getElementById('filter-form').submit();
        };
        suggestionsBox.appendChild(div);
    });

    suggestionsBox.style.display = 'block';
}

function highlightMatch(text, q) {
    var escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return text.replace(new RegExp('(' + escaped + ')', 'gi'), '<mark style="background:rgba(255,193,7,.4);border-radius:2px;padding:0 1px;">$1</mark>');
}

function highlightSugg(items) {
    items.forEach(function(item, idx) {
        item.style.background = idx === selectedSuggIdx ? 'rgba(13,110,253,.1)' : '';
    });
    if (selectedSuggIdx >= 0 && items[selectedSuggIdx]) {
        // Peek the name into input
        var name = currentSuggestions[selectedSuggIdx] ? currentSuggestions[selectedSuggIdx].name : '';
        if (name) searchInput.value = name;
    }
}

function hideSuggestions() {
    suggestionsBox.style.display = 'none';
    selectedSuggIdx = -1;
}

// ── Row expand ────────────────────────────────────────────────────
var expandedRow = null;

function toggleExpand(jobId, rowEl) {
    var expandRow = document.getElementById('expand-'+jobId);

    if (expandedRow === jobId) {
        expandRow.classList.remove('show');
        rowEl.classList.remove('expanded');
        expandedRow = null;
        return;
    }

    if (expandedRow !== null) {
        var pr = document.getElementById('row-'+expandedRow);
        var pe = document.getElementById('expand-'+expandedRow);
        if (pr) pr.classList.remove('expanded');
        if (pe) pe.classList.remove('show');
    }

    expandRow.classList.add('show');
    rowEl.classList.add('expanded');
    expandedRow = jobId;

    setTimeout(function(){
        expandRow.scrollIntoView({behavior:'smooth',block:'nearest'});
    }, 60);
}
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/jobs/index.blade.php ENDPATH**/ ?>