<?php $__env->startSection('title','Repair Stock'); ?>
<?php $__env->startSection('topbar-actions'); ?>
<a href="<?php echo e(route('parts.report')); ?>" target="_blank" class="btn btn-outline-secondary btn-sm">🖨️ Print Report</a>
<a href="<?php echo e(route('parts.create')); ?>" class="btn btn-primary btn-sm">+ Add Part</a>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="page-header"><div><h2>🗃️ Repair Stock</h2><p>Parts inventory</p></div></div>
<div class="row g-3 mb-3">
    <div class="col-6 col-md"><div class="card border-top border-primary border-3"><div class="card-body py-3"><div class="small text-secondary">Total Parts</div><div class="fw-bold fs-5"><?php echo e($totalParts); ?></div></div></div></div>
    <div class="col-6 col-md"><div class="card border-top border-success border-3"><div class="card-body py-3"><div class="small text-secondary">In Stock</div><div class="fw-bold fs-5 text-success"><?php echo e($totalParts - $outOfStock); ?></div></div></div></div>
    <div class="col-6 col-md"><div class="card border-top border-danger border-3"><div class="card-body py-3"><div class="small text-secondary">Low Stock</div><div class="fw-bold fs-5 text-danger"><?php echo e($lowStock); ?></div></div></div></div>
    <div class="col-6 col-md"><div class="card border-top border-secondary border-3"><div class="card-body py-3"><div class="small text-secondary">Out of Stock</div><div class="fw-bold fs-5"><?php echo e($outOfStock); ?></div></div></div></div>
    <div class="col-6 col-md"><div class="card border-top border-warning border-3"><div class="card-body py-3"><div class="small text-secondary">Stock Value</div><div class="fw-bold fs-5">£<?php echo e(number_format($totalStockValue,0)); ?></div></div></div></div>
</div>
<div class="card mb-3"><div class="card-body py-2">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-sm-3"><label class="form-label">Search</label><input type="text" name="search" class="form-control form-control-sm" value="<?php echo e(request('search')); ?>" placeholder="Part name..."></div>
        <div class="col-sm-2"><label class="form-label">Category</label><select name="category" class="form-select form-select-sm no-ts"><option value="">All</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php echo e(request('category')==$c->id?'selected':''); ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select></div>
        <div class="col-sm-2"><label class="form-label">Stock</label><select name="stock_status" class="form-select form-select-sm no-ts"><option value="">All</option><option value="in_stock" <?php echo e(request('stock_status')==='in_stock'?'selected':''); ?>>In Stock</option><option value="low" <?php echo e(request('stock_status')==='low'?'selected':''); ?>>Low Stock</option><option value="out" <?php echo e(request('stock_status')==='out'?'selected':''); ?>>Out of Stock</option></select></div>
        <div class="col-auto"><button class="btn btn-primary btn-sm">🔍 Filter</button></div>
        <div class="col-auto"><a href="<?php echo e(route('parts.index')); ?>" class="btn btn-outline-secondary btn-sm">Clear</a></div>
    </form>
</div></div>
<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-secondary"><tr><th>Category</th><th>Part</th><th>Type</th><th>Quality</th><th>Stock</th><th>Cost £</th><th>Sell £</th><th>Actions</th></tr></thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($part->category->icon ?? ''); ?> <?php echo e($part->category->name); ?></td>
                <td class="fw-semibold"><?php echo e($part->name); ?></td>
                <td class="text-secondary"><?php echo e($part->part_type ?? '—'); ?></td>
                <td class="text-secondary"><?php echo e($part->quality ?? '—'); ?></td>
                <td>
                    <?php $stock = $part->remainingStock(); ?>
                    <span class="badge bg-<?php echo e($stock<=0?'danger':($stock<=($part->low_stock_threshold??2)?'warning text-dark':'success')); ?>"><?php echo e($stock); ?></span>
                </td>
                <td class="text-secondary"><?php echo e($part->cost_price ? '£'.number_format($part->cost_price,2) : '—'); ?></td>
                <td class="fw-semibold"><?php echo e($part->sell_price ? '£'.number_format($part->sell_price,2) : '—'); ?></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#topup-<?php echo e($part->id); ?>">📦 Topup</button>
                    <a href="<?php echo e(route('parts.edit',$part)); ?>" class="btn btn-sm btn-outline-secondary">✏️</a>
                    <form method="POST" action="<?php echo e(route('parts.destroy',$part)); ?>" class="d-inline" onsubmit="return confirm('Delete?')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger">🗑️</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="8" class="text-center text-secondary py-4">No parts. <a href="<?php echo e(route('parts.create')); ?>">Add one</a></td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3"><?php echo e($parts->links()); ?></div>


<?php $__currentLoopData = $parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="topup-<?php echo e($part->id); ?>" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">📦 Top Up Stock</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="<?php echo e(route('parts.topup',$part)); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="small text-secondary mb-3"><?php echo e($part->name); ?><br>Current stock: <strong><?php echo e($part->remainingStock()); ?></strong></div>
                    <div class="mb-3"><label class="form-label">Add Quantity *</label><input type="number" name="quantity" class="form-control" min="1" required></div>
                    <div class="mb-2"><label class="form-label">Notes</label><input type="text" name="notes" class="form-control" placeholder="Optional"></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary w-100">💾 Add Stock</button></div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/parts/index.blade.php ENDPATH**/ ?>