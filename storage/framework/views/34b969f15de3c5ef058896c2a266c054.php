<?php $__env->startSection('title','POS Sales History'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.pro-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .pro-card { background:#1c1c1e; }
.stat-val { font-family:'Syne',sans-serif;font-size:28px;font-weight:800; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">🧾 POS Sales History</h2>
        <div class="text-secondary small">All point-of-sale transactions</div>
    </div>
    <a href="<?php echo e(route('pos.terminal')); ?>" class="btn btn-success">
        <i class="bi bi-cart-plus me-1"></i>New Sale
    </a>
</div>


<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="text-secondary small">Today's Sales</div>
            <div class="stat-val text-primary"><?php echo e($todaySales); ?></div>
            <div class="text-secondary" style="font-size:11px;">transactions</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="text-secondary small">Today's Revenue</div>
            <div class="stat-val text-success">£<?php echo e(number_format($todayRev,2)); ?></div>
            <div class="text-secondary" style="font-size:11px;">from POS</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="text-secondary small">Total Revenue</div>
            <div class="stat-val">£<?php echo e(number_format($totalRev,2)); ?></div>
            <div class="text-secondary" style="font-size:11px;">all time</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card p-3">
            <div class="text-secondary small">Total Sales</div>
            <div class="stat-val"><?php echo e($sales->total()); ?></div>
            <div class="text-secondary" style="font-size:11px;">transactions</div>
        </div>
    </div>
</div>


<div class="pro-card p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3">
            <label class="form-label">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" value="<?php echo e(request('search')); ?>" placeholder="Customer name...">
        </div>
        <div class="col-sm-2">
            <label class="form-label">Payment</label>
            <select name="payment" class="form-select form-select-sm no-ts">
                <option value="">All</option>
                <?php $__currentLoopData = ['Cash','Card','Split']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($p); ?>" <?php echo e(request('payment')===$p?'selected':''); ?>><?php echo e($p); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-sm-2">
            <label class="form-label">From</label>
            <input type="date" name="date_from" class="form-control form-control-sm" value="<?php echo e(request('date_from')); ?>">
        </div>
        <div class="col-sm-2">
            <label class="form-label">To</label>
            <input type="date" name="date_to" class="form-control form-control-sm" value="<?php echo e(request('date_to')); ?>">
        </div>
        <div class="col-auto d-flex gap-2">
            <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
            <a href="<?php echo e(route('pos.index')); ?>" class="btn btn-outline-secondary btn-sm">Clear</a>
        </div>
    </form>
</div>


<div class="pro-card overflow-hidden">
    <table class="table mb-0 align-middle" style="font-size:13px;">
        <thead style="background:var(--bs-tertiary-bg);">
            <tr>
                <th class="px-4 py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">#</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Customer</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Items</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Payment</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Total</th>
                <th class="py-3" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Date</th>
                <th class="py-3 pe-4" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;opacity:.6;border:none;">Status</th>
                <th class="py-3 pe-4" style="border:none;"></th>
            </tr>
        </thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="border-top">
            <td class="px-4 py-3 text-secondary fw-semibold">#<?php echo e($sale->id); ?></td>
            <td class="py-3">
                <div class="fw-semibold"><?php echo e($sale->customerLabel()); ?></div>
                <?php if($sale->user): ?><div class="text-secondary" style="font-size:11px;">By <?php echo e($sale->user->name); ?></div><?php endif; ?>
            </td>
            <td class="py-3">
                <div><?php echo e($sale->items->count()); ?> item(s)</div>
                <div class="text-secondary" style="font-size:11px;">
                    <?php echo e($sale->items->pluck('name')->take(2)->implode(', ')); ?><?php echo e($sale->items->count() > 2 ? '...' : ''); ?>

                </div>
            </td>
            <td class="py-3">
                <span class="badge rounded-pill <?php echo e($sale->payment_method==='Cash'?'bg-success':($sale->payment_method==='Card'?'bg-primary':'bg-warning text-dark')); ?>">
                    <?php echo e($sale->payment_method); ?>

                </span>
            </td>
            <td class="py-3 fw-semibold">£<?php echo e(number_format($sale->total,2)); ?></td>
            <td class="py-3 text-secondary"><?php echo e($sale->created_at->format('d M Y H:i')); ?></td>
            <td class="py-3">
                <?php if($sale->status === 'refunded'): ?>
                <span class="badge bg-danger rounded-pill">Refunded</span>
                <?php else: ?>
                <span class="badge bg-success rounded-pill">Completed</span>
                <?php endif; ?>
            </td>
            <td class="py-3 pe-4">
                <div class="d-flex gap-1">
                    <a href="<?php echo e(route('pos.receipt', $sale)); ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Receipt">
                        <i class="bi bi-printer"></i>
                    </a>
                    <?php if($sale->status === 'completed'): ?>
                    <form method="POST" action="<?php echo e(route('pos.destroy', $sale)); ?>" class="d-inline"
                        onsubmit="return confirm('Void sale #<?php echo e($sale->id); ?>? Stock will be restored.')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-outline-danger" title="Void"><i class="bi bi-x-circle"></i></button>
                    </form>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="8" class="text-center text-secondary py-5">
            <div style="font-size:32px;opacity:.3;">🧾</div>
            <div class="mt-2">No sales yet. <a href="<?php echo e(route('pos.terminal')); ?>">Make your first sale</a></div>
        </td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php if($sales->hasPages()): ?>
    <div class="px-4 py-3 border-top"><?php echo e($sales->links()); ?></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/pos/index.blade.php ENDPATH**/ ?>