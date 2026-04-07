<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header">
    <div><h2>🏠 Dashboard</h2><p>Welcome back, <?php echo e(auth()->user()->name); ?></p></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card border-top border-primary border-3 h-100"><div class="card-body"><div class="fs-4 mb-1">🗃️</div><div class="stat-value"><?php echo e($totalParts); ?></div><div class="stat-label">Parts in Stock</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card border-top border-success border-3 h-100"><div class="card-body"><div class="fs-4 mb-1">👥</div><div class="stat-value"><?php echo e($totalCustomers); ?></div><div class="stat-label">Customers</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card border-top border-warning border-3 h-100"><div class="card-body"><div class="fs-4 mb-1">🔧</div><div class="stat-value"><?php echo e($activeJobs); ?></div><div class="stat-label">Active Jobs</div></div></div></div>
    <div class="col-6 col-md-3"><div class="card border-top border-danger border-3 h-100"><div class="card-body"><div class="fs-4 mb-1">⏳</div><div class="stat-value"><?php echo e($pendingJobs); ?></div><div class="stat-label">Pending Jobs</div></div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">🔧 Recent Jobs</span>
                <a href="<?php echo e(route('jobs.index')); ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-secondary"><tr><th>Customer</th><th>Device</th><th>Status</th><th>Date</th><th></th></tr></thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td class="fw-semibold"><?php echo e($job->customer->name); ?></td>
                        <td class="text-secondary small"><?php echo e($job->devices->first()?->name ?? '—'); ?></td>
                        <td><span class="badge rounded-pill bg-<?php echo e($job->status==='Completed'?'success':($job->status==='In Progress'?'primary':'warning')); ?>"><?php echo e($job->status); ?></span></td>
                        <td class="text-secondary small"><?php echo e($job->date_in->format('d M Y')); ?></td>
                        <td><a href="<?php echo e(route('jobs.show',$job)); ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="5" class="text-center text-secondary py-4">No jobs yet</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header fw-bold">📊 Quick Stats</div>
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between"><span class="text-secondary">Total Jobs</span><strong><?php echo e($totalJobs ?? 0); ?></strong></div>
                <div class="list-group-item d-flex justify-content-between"><span class="text-secondary">Completed</span><strong class="text-success"><?php echo e($completedJobs ?? 0); ?></strong></div>
                <div class="list-group-item d-flex justify-content-between"><span class="text-secondary">Revenue Today</span><strong>£<?php echo e(number_format($todayRevenue ?? 0, 2)); ?></strong></div>
                <div class="list-group-item d-flex justify-content-between"><span class="text-secondary">Low Stock Parts</span><strong class="text-danger"><?php echo e($lowStockParts ?? 0); ?></strong></div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/dashboard/index.blade.php ENDPATH**/ ?>