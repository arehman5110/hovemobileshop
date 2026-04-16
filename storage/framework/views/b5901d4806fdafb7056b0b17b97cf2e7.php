<?php $__env->startSection('title','Add Part'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.form-card { background:#fff;border:none;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);padding:24px; }
[data-bs-theme="dark"] .form-card { background:#1c1c1e; }
.section-title { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;opacity:.5;margin-bottom:12px; }
.usage-pill { display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;padding:14px 10px;border:2px solid var(--bs-border-color);border-radius:12px;cursor:pointer;transition:all .15s;text-align:center;user-select:none; }
.usage-pill:hover { border-color:#0d6efd; }
.usage-pill.selected-repair   { border-color:#0d6efd;background:rgba(13,110,253,.08); }
.usage-pill.selected-accessory { border-color:#198754;background:rgba(25,135,84,.08); }
.usage-pill.selected-both     { border-color:#6f42c1;background:rgba(111,66,193,.08); }
.usage-pill .up-icon { font-size:24px; }
.usage-pill .up-label { font-size:12px;font-weight:700; }
.usage-pill .up-desc { font-size:10px;color:var(--bs-secondary-color);line-height:1.3; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?php echo e(route('parts.index')); ?>" class="btn btn-outline-secondary btn-sm">←</a>
    <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">➕ Add Part / Accessory</h2>
</div>

<form method="POST" action="<?php echo e(route('parts.store')); ?>">
<?php echo csrf_field(); ?>
<div class="row g-4">

    
    <div class="col-lg-8">

        
        <div class="form-card mb-4">
            <div class="section-title">Basic Information</div>
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Part / Product Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo e(old('name')); ?>"
                        placeholder="e.g. Tempered Glass Screen Protector">
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Category *</label>
                    <select name="category_id" class="form-select no-ts" required>
                        <option value="">Select category...</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id') == $cat->id ? 'selected' : ''); ?>>
                            <?php echo e($cat->icon); ?> <?php echo e($cat->name); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Type *</label>
                    <select name="part_type" class="form-select no-ts" required>
                        <option value="">Select type...</option>
                        <?php $__currentLoopData = $partTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type); ?>" <?php echo e(old('part_type') === $type ? 'selected' : ''); ?>><?php echo e($type); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Quality *</label>
                    <select name="quality" class="form-select no-ts" required>
                        <?php $__currentLoopData = ['Original','Compatible','Refurbished','Good Used']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($q); ?>" <?php echo e(old('quality') === $q ? 'selected' : ''); ?>><?php echo e($q); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Initial Stock *</label>
                    <input type="number" name="stock" class="form-control" required min="0" value="<?php echo e(old('stock',0)); ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any extra notes..."><?php echo e(old('notes')); ?></textarea>
                </div>
            </div>
        </div>

        
        <div class="form-card mb-4">
            <div class="section-title">Usage Type *</div>
            <p class="text-secondary small mb-3">How is this item used? This controls where it appears.</p>
            <div class="row g-3" id="usage-pills">
                <div class="col-4">
                    <div class="usage-pill <?php echo e(old('usage_type','repair')==='repair' ? 'selected-repair' : ''); ?>"
                        onclick="selectUsage('repair',this)">
                        <div class="up-icon">🔧</div>
                        <div class="up-label">Repair Only</div>
                        <div class="up-desc">Used in job repairs. Hidden from POS.</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="usage-pill <?php echo e(old('usage_type')==='accessory' ? 'selected-accessory' : ''); ?>"
                        onclick="selectUsage('accessory',this)">
                        <div class="up-icon">🛍️</div>
                        <div class="up-label">Accessory</div>
                        <div class="up-desc">Sold in POS. Cases, glass, chargers etc.</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="usage-pill <?php echo e(old('usage_type')==='both' ? 'selected-both' : ''); ?>"
                        onclick="selectUsage('both',this)">
                        <div class="up-icon">⚡</div>
                        <div class="up-label">Both</div>
                        <div class="up-desc">Used in repairs AND sold in POS.</div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="usage_type" id="usage_type" value="<?php echo e(old('usage_type','repair')); ?>" required>
            <?php $__errorArgs = ['usage_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="text-danger small mt-1"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="form-card mb-4" id="brand-section">
            <div class="section-title">Brand & Compatibility</div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <label class="form-label">Brand</label>
                    <input type="text" name="brand" class="form-control" value="<?php echo e(old('brand')); ?>"
                        placeholder="e.g. Apple, Samsung, Universal">
                    <div class="form-text">Leave blank for universal / unbranded</div>
                </div>
                <div class="col-sm-6">
                    <label class="form-label">Compatible With</label>
                    <input type="text" name="compatible_with" class="form-control" value="<?php echo e(old('compatible_with')); ?>"
                        placeholder="e.g. iPhone 15 Pro, Samsung S24">
                    <div class="form-text">Model(s) this fits</div>
                </div>
            </div>
        </div>

    </div>

    
    <div class="col-lg-4">
        <div class="form-card mb-4">
            <div class="section-title">Pricing</div>
            <div class="mb-3">
                <label class="form-label">Cost Price £</label>
                <input type="number" name="cost_price" class="form-control" step="0.01" min="0"
                    value="<?php echo e(old('cost_price')); ?>" placeholder="0.00">
                <div class="form-text">What you paid for it</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Sell Price £</label>
                <input type="number" name="sell_price" class="form-control" step="0.01" min="0"
                    value="<?php echo e(old('sell_price')); ?>" placeholder="0.00">
                <div class="form-text">What you charge customers</div>
            </div>
            <?php
                $cost = old('cost_price',0);
                $sell = old('sell_price',0);
            ?>
            <?php if($cost && $sell && $sell > $cost): ?>
            <div class="p-2 rounded-2 text-success small" style="background:rgba(25,135,84,.08);">
                Margin: £<?php echo e(number_format($sell-$cost,2)); ?> (<?php echo e(number_format(($sell-$cost)/$cost*100,0)); ?>%)
            </div>
            <?php endif; ?>
        </div>

        <div class="form-card">
            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">
                <i class="bi bi-check-circle me-2"></i>Add Part
            </button>
            <a href="<?php echo e(route('parts.index')); ?>" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
        </div>
    </div>

</div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function selectUsage(type, el) {
    // Remove all selected classes
    document.querySelectorAll('.usage-pill').forEach(function(p) {
        p.className = 'usage-pill';
    });
    // Add correct class
    el.className = 'usage-pill selected-' + type;
    document.getElementById('usage_type').value = type;

    // Show/hide brand section for repair-only parts
    var brandSection = document.getElementById('brand-section');
    if (brandSection) {
        brandSection.style.display = type === 'repair' ? 'none' : '';
    }
}

// Init on load
document.addEventListener('DOMContentLoaded', function() {
    var current = document.getElementById('usage_type').value || 'repair';
    var brandSection = document.getElementById('brand-section');
    if (brandSection) {
        brandSection.style.display = current === 'repair' ? 'none' : '';
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/parts/create.blade.php ENDPATH**/ ?>