
<?php
    $selectId  = $selectId  ?? 'customer_select';
    $previewId = $previewId ?? 'customer';
    $hasCustomer = isset($customer) && $customer;
?>

<div id="<?php echo e($previewId); ?>-preview" class="mt-3" style="<?php echo e($hasCustomer ? '' : 'display:none;'); ?>">
    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">

        
        <div id="<?php echo e($previewId); ?>-avatar"
            style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0099ff);
                   display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;">
            <?php echo e($hasCustomer ? strtoupper(substr($customer->name,0,1)) : ''); ?>

        </div>

        
        <div class="flex-grow-1 min-width-0">
            <div class="fw-bold" id="<?php echo e($previewId); ?>-preview-name" style="font-size:14px;">
                <?php echo e($hasCustomer ? $customer->name : ''); ?>

            </div>
            <div class="d-flex flex-wrap gap-3 mt-1">
                <span id="<?php echo e($previewId); ?>-preview-phone" class="text-secondary" style="font-size:11px;<?php echo e(($hasCustomer && $customer->phone) ? '' : 'display:none;'); ?>">
                    <i class="bi bi-telephone me-1"></i><span><?php echo e($hasCustomer ? $customer->phone : ''); ?></span>
                </span>
                <span id="<?php echo e($previewId); ?>-preview-email" class="text-secondary" style="font-size:11px;<?php echo e(($hasCustomer && $customer->email) ? '' : 'display:none;'); ?>">
                    <i class="bi bi-envelope me-1"></i><span><?php echo e($hasCustomer ? $customer->email : ''); ?></span>
                </span>
                <span id="<?php echo e($previewId); ?>-preview-address" class="text-secondary" style="font-size:11px;<?php echo e(($hasCustomer && $customer->address) ? '' : 'display:none;'); ?>">
                    <i class="bi bi-geo-alt me-1"></i><span><?php echo e($hasCustomer ? $customer->address : ''); ?></span>
                </span>
            </div>
        </div>

        
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <span id="<?php echo e($previewId); ?>-history-loading" style="display:none;">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
            <div id="<?php echo e($previewId); ?>-history-stats" style="display:none;text-align:right;">
                <div style="font-size:11px;font-weight:700;font-family:'Syne',sans-serif;" id="<?php echo e($previewId); ?>-stat-spent" class="text-success"></div>
                <div style="font-size:10px;" id="<?php echo e($previewId); ?>-stat-due" class="text-danger"></div>
            </div>
            <a id="<?php echo e($previewId); ?>-view-jobs" href="#" target="_blank"
                class="btn btn-sm btn-outline-secondary" style="font-size:11px;display:none;"></a>
            <button type="button" onclick="openEditCustomerModal('<?php echo e($selectId); ?>')"
                class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil"></i>
            </button>
        </div>
    </div>
</div>
<?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/jobs/_customer_preview.blade.php ENDPATH**/ ?>