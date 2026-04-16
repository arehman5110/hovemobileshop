<?php $__env->startSection('title', 'Edit Job #'.$job->id); ?>

<?php $__env->startPush('styles'); ?>
<style>
.job-form-card { border:none;border-radius:14px;background:#ffffff;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);overflow:visible; }
[data-bs-theme="dark"] .job-form-card { background:#1c1c1e;box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4); }
.summary-card { border:none;border-radius:14px;background:#ffffff;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);transition:box-shadow .2s; }
[data-bs-theme="dark"] .summary-card { background:#1c1c1e;box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4); }
@media(min-width:992px){
    .summary-card.is-pinned { position:fixed;top:76px;max-height:calc(100vh - 92px);overflow-y:auto;box-shadow:0 4px 24px rgba(0,0,0,.15);z-index:100; }
    .summary-placeholder { display:none; }
    .summary-placeholder.active { display:block; }
}
.section-badge { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:50%;background:#0d6efd;color:#fff;font-size:13px;font-weight:700;flex-shrink:0; }
.device-card { border:2px solid var(--bs-border-color);border-radius:12px;overflow:visible;transition:border-color .2s,box-shadow .2s;margin-bottom:16px; }
.device-card:hover { border-color:#0d6efd66;box-shadow:0 4px 16px rgba(13,110,253,.1); }
.device-card-header { background:var(--bs-tertiary-bg);border-radius:10px 10px 0 0;padding:12px 16px;display:flex;align-items:center;gap:10px;border-bottom:1px solid var(--bs-border-color); }
.device-card-body { padding:16px;border-radius:0 0 10px 10px; }
.device-number { display:inline-flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:50%;background:#0d6efd;color:#fff;font-size:11px;font-weight:700;flex-shrink:0; }
.add-device-btn { border:2px dashed #198754;border-radius:12px;padding:14px;width:100%;background:transparent;color:#198754;font-size:14px;font-weight:600;transition:all .2s;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px; }
.add-device-btn:hover { background:rgba(25,135,84,.08);border-color:#146c43;color:#146c43;transform:translateY(-1px); }
.pay-method-btn { flex:1;text-align:center;padding:10px 6px;border:2px solid var(--bs-border-color);border-radius:10px;cursor:pointer;transition:all .15s;user-select:none; }
.pay-method-btn.active { border-color:#198754;background:rgba(25,135,84,.1);color:#198754; }
.pay-method-btn .pay-icon { font-size:22px; }
.pay-method-btn .pay-label { font-size:12px;font-weight:600;margin-top:3px; }
.summary-total { font-family:'Syne',sans-serif;font-size:32px;font-weight:800;line-height:1;transition:transform .15s ease; }
.form-label { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.65;margin-bottom:5px; }
.btn-save-job { background:linear-gradient(135deg,#0d6efd,#0099ff);border:none;border-radius:12px;padding:14px;font-size:15px;font-weight:700;width:100%;color:#fff;cursor:pointer;transition:all .2s;box-shadow:0 4px 14px rgba(13,110,253,.35); }
.btn-save-job:hover { transform:translateY(-2px);box-shadow:0 6px 20px rgba(13,110,253,.45);color:#fff; }
.applied-row { display:flex;justify-content:space-between;align-items:center;padding:6px 10px;border-radius:8px;background:var(--bs-tertiary-bg);font-size:13px; }
.btn-trigger { width:100%;border:2px solid #0d6efd;border-radius:12px;padding:11px;background:rgba(13,110,253,.06);color:#0d6efd;font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px; }
.btn-trigger:hover { background:rgba(13,110,253,.12); }
.btn-trigger.applied { box-shadow:0 0 0 3px rgba(13,110,253,.2); }
.btn-trigger-disc { border-color:#e67e22;background:rgba(230,126,34,.06);color:#e67e22; }
.btn-trigger-disc:hover { background:rgba(230,126,34,.12); }
.btn-trigger-disc.applied { box-shadow:0 0 0 3px rgba(230,126,34,.2); }
.payments-modal-section { background:var(--bs-tertiary-bg);border-radius:12px;padding:16px;margin-bottom:12px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?php echo e(route('jobs.show',$job)); ?>" class="btn btn-outline-secondary btn-sm">←</a>
    <div>
        <h2 class="syne mb-0" style="font-size:22px;font-weight:800;">Edit Job #<?php echo e($job->id); ?></h2>
        <div class="text-secondary small"><?php echo e($job->customer->name); ?> · <?php echo e($job->date_in->format('d M Y')); ?></div>
    </div>
</div>

<form method="POST" action="<?php echo e(route('jobs.update',$job)); ?>" id="job-form">
<?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
<input type="hidden" name="discount_type"  id="discount_type"  value="<?php echo e($job->discount_type); ?>">
<input type="hidden" name="discount_value" id="discount_value" value="<?php echo e($job->discount_value ?? 0); ?>">


<div class="row g-4" style="align-items:start;">
<div class="col-lg-8 d-flex flex-column gap-4">

    
    <div class="job-form-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-badge">1</span>
                <div><div class="fw-bold" style="font-size:15px;">Customer</div><div class="text-secondary" style="font-size:12px;">Change customer if needed</div></div>
            </div>
            <div class="d-flex gap-2 align-items-start">
                <div class="flex-grow-1">
                    <select class="form-select" name="customer_id" id="edit_customer_select" onchange="onEditCustomerChange(this)">
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($c->id); ?>" <?php echo e($job->customer_id==$c->id?'selected':''); ?>

                            data-phone="<?php echo e($c->phone); ?>" data-email="<?php echo e($c->email); ?>" data-address="<?php echo e($c->address); ?>" data-notes="<?php echo e(addslashes($c->notes ?? '')); ?>">
                            <?php echo e($c->name); ?><?php echo e($c->phone?' · '.$c->phone:''); ?>

                        </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="button" onclick="openEditCustomerModal()"
                    class="btn btn-outline-secondary d-flex align-items-center gap-1" style="white-space:nowrap;flex-shrink:0;">
                    <i class="bi bi-pencil"></i> Edit
                </button>
                <button type="button" onclick="openCustomerModal()"
                    class="btn btn-success d-flex align-items-center gap-1" style="white-space:nowrap;flex-shrink:0;">
                    <i class="bi bi-person-plus"></i> New
                </button>
            </div>

            
            <?php echo $__env->make('jobs._customer_preview', [
                'selectId'   => 'edit_customer_select',
                'previewId'  => 'edit-customer',
                'customer'   => $job->customer,
            ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </div>

    
    <div class="job-form-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-badge">2</span>
                <div class="flex-grow-1"><div class="fw-bold" style="font-size:15px;">Devices & Repairs</div><div class="text-secondary" style="font-size:12px;">Update devices and repair details</div></div>
            </div>
            <div id="devices-container">
            <?php $__currentLoopData = $job->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $di => $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $dRepairTypeIds = $device->repairItems->pluck('repair_type_id')->filter()->unique()->values();
                $dPartIds       = $device->repairItems->pluck('part_id')->filter()->unique()->values();
                $dStatus        = $device->repairItems->first()?->status ?? 'In Progress';
                $dIssue         = $device->repairItems->first()?->issue ?? '';
                $dPrice         = $device->repairItems->sum('price');
            ?>
            <div class="device-card">
                <div class="device-card-header">
                    <span class="device-number"><?php echo e($di+1); ?></span>
                    <input type="text" name="devices[<?php echo e($di); ?>][name]" value="<?php echo e($device->name); ?>" placeholder="Device name"
                        style="background:transparent;border:none;outline:none;font-weight:600;font-size:14px;flex:1;color:var(--bs-body-color);">
                    <button type="button" onclick="removeDevice(this)" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-trash"></i></button>
                </div>
                <div class="device-card-body">
                    <div class="row g-2 mb-3 pb-3 border-bottom">
                        <div class="col-sm-4"><label class="form-label">IMEI / Serial</label><input type="text" class="form-control form-control-sm" name="devices[<?php echo e($di); ?>][imei]" value="<?php echo e($device->imei); ?>" maxlength="20"></div>
                        <div class="col-sm-4"><label class="form-label">Colour</label><input type="text" class="form-control form-control-sm" name="devices[<?php echo e($di); ?>][color]" value="<?php echo e($device->color); ?>"></div>
                        <div class="col-sm-4"><label class="form-label">Warranty Expiry</label><input type="date" class="form-control form-control-sm" name="devices[<?php echo e($di); ?>][warranty]" value="<?php echo e($device->warranty); ?>"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">🔧 Repair Types</label>
                        <select name="devices[<?php echo e($di); ?>][repair_type_ids][]" multiple class="repair-type-select">
                            <?php $__currentLoopData = $repairTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->id); ?>" <?php echo e($dRepairTypeIds->contains($type->id)?'selected':''); ?>><?php echo e($type->icon); ?> <?php echo e($type->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-sm-5">
                            <label class="form-label">🗃️ Parts Used</label>
                            <select name="devices[<?php echo e($di); ?>][part_ids][]" multiple class="part-select">
                                <?php $__currentLoopData = $parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($part->id); ?>" <?php echo e($dPartIds->contains($part->id)?'selected':''); ?>><?php echo e($part->category->icon); ?> <?php echo e($part->name); ?> (<?php echo e($part->remainingStock()); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-sm-4"><label class="form-label">Status</label>
                            <select name="devices[<?php echo e($di); ?>][repair_status]" class="form-select form-select-sm no-ts">
                                <?php $__currentLoopData = \App\Models\Job::statuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($s); ?>" <?php echo e($dStatus===$s?'selected':''); ?>><?php echo e($s); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-sm-3"><label class="form-label">Price £</label>
                            <input type="number" name="devices[<?php echo e($di); ?>][repair_price]" class="form-control form-control-sm device-price" value="<?php echo e($dPrice); ?>" step="0.01" min="0" oninput="recalc()">
                        </div>
                    </div>
                    <div><label class="form-label">📋 Issue / Description</label>
                        <textarea class="form-control form-control-sm" name="devices[<?php echo e($di); ?>][issue]" rows="2" placeholder="Describe the issue..."><?php echo e($dIssue); ?></textarea>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <button type="button" onclick="addDevice()" class="add-device-btn mt-2"><i class="bi bi-plus-circle"></i> Add Device</button>
        </div>
    </div>

    
    <div class="job-form-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-3">
                <span class="section-badge">3</span>
                <div><div class="fw-bold" style="font-size:15px;">Job Details</div><div class="text-secondary" style="font-size:12px;">Status, dates and notes</div></div>
            </div>
            <div class="row g-3">
                <div class="col-sm-4"><label class="form-label">Status</label>
                    <?php echo $__env->make('jobs._status_select', ['selected' => $job->status], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
                <div class="col-sm-4">
                    <label class="form-label">📍 Device Location</label>
                    <select class="form-select no-ts" name="device_location">
                        <option value="With Us"       <?php echo e(($job->device_location ?? 'With Us')==='With Us'?'selected':''); ?>>📦 With Us</option>
                        <option value="With Customer" <?php echo e(($job->device_location ?? '')==='With Customer'?'selected':''); ?>>👤 With Customer</option>
                    </select>
                </div>
                <div class="col-sm-4"><label class="form-label">Book-In Date</label><input type="date" class="form-control" name="date_in" value="<?php echo e($job->date_in?->format('Y-m-d')); ?>"></div>
                <div class="col-sm-4"><label class="form-label">Completion Date</label><input type="date" class="form-control" name="date_out" value="<?php echo e($job->date_out?->format('Y-m-d')); ?>"></div>
                <div class="col-12"><label class="form-label">Internal Notes</label><textarea class="form-control" name="notes" rows="2" placeholder="Any internal notes..."><?php echo e($job->notes); ?></textarea></div>
            </div>
        </div>
    </div>

</div>


<div class="col-lg-4" id="summary-col">
<div class="summary-placeholder" id="summary-placeholder"></div>
<div class="summary-card card" id="summary-card">
    <div class="card-body p-4">

        
        <div class="text-center pb-3 mb-3 border-bottom">
            <div class="text-secondary small mb-1">Total Due</div>
            <div class="summary-total text-primary" id="summary-total">£<?php echo e(number_format($job->totalAfterDiscount(),2)); ?></div>
        </div>
        <div class="d-flex flex-column gap-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary small">Subtotal</span>
                <span class="fw-semibold" id="summary-subtotal">£<?php echo e(number_format($job->devices->sum(fn($d) => $d->repairItems->sum('price')),2)); ?></span>
            </div>
            <div class="applied-row" id="row-discount" style="<?php echo e($job->discount_type ? 'display:flex;' : 'display:none;'); ?>">
                <span class="text-secondary small" id="lbl-discount"><?php echo e($job->discount_type === 'percent' ? 'Discount ('.$job->discount_value.'%)' : 'Discount (Fixed)'); ?></span>
                <span class="fw-semibold text-danger" id="val-discount">-£<?php echo e(number_format($job->discountAmount(),2)); ?></span>
            </div>
            <div class="applied-row" id="row-voucher" style="<?php echo e($job->voucher_code ? 'display:flex;' : 'display:none;'); ?>">
                <span class="text-secondary small" id="lbl-voucher">🎟️ <?php echo e($job->voucher_code); ?></span>
                <span class="fw-semibold text-success" id="val-voucher">-£<?php echo e(number_format($job->voucher_amount ?? 0,2)); ?></span>
            </div>
            <?php if($job->totalPaid() > 0): ?>
            <div class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background:rgba(25,135,84,.06);">
                <span class="text-secondary small">✅ Total Paid</span>
                <span class="fw-semibold text-success" id="total-paid-display">£<?php echo e(number_format($job->totalPaid(),2)); ?></span>
            </div>
            <?php endif; ?>
        </div>

        
        <div id="balance-box-due" class="d-flex justify-content-between align-items-center p-3 mb-3 rounded-3" style="background:rgba(220,53,69,.08);border:1px solid rgba(220,53,69,.2);<?php echo e($job->isPaidInFull() ? 'display:none!important;' : ''); ?>">
            <span class="fw-bold">Balance Due</span>
            <span class="fw-bold text-danger" style="font-size:20px;font-family:'Syne',sans-serif;" id="balance-due-amount">£<?php echo e(number_format($job->balanceDue(),2)); ?></span>
        </div>
        <div id="balance-box-paid" class="text-center py-2 mb-3 rounded-3 fw-bold" style="background:rgba(25,135,84,.1);border:2px solid rgba(25,135,84,.3);color:#198754;font-size:15px;<?php echo e($job->isPaidInFull() ? '' : 'display:none;'); ?>">✅ Fully Paid</div>

        
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold small">💳 Payments</span>
                <button type="button" class="btn btn-success btn-sm" onclick="openAddPaymentModal()" style="border-radius:8px;">
                    <i class="bi bi-plus-lg me-1"></i>Add
                </button>
            </div>
            <?php if($job->payments->isEmpty()): ?>
            <div class="text-secondary small text-center py-2">No payments yet</div>
            <?php else: ?>
            <div class="d-flex flex-column gap-1">
                <?php $__currentLoopData = $job->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pmt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $isSplit = $pmt->notes && str_starts_with(trim($pmt->notes),'[');
                    $typeLabel = $isSplit ? '✂️ Split' : $pmt->payment_type;
                ?>
                <div class="d-flex align-items-center justify-content-between p-2 rounded-2" style="background:var(--bs-tertiary-bg);font-size:12px;">
                    <div>
                        <span class="fw-semibold"><?php echo e($typeLabel); ?></span>
                        <?php if($isSplit): ?>
                        <?php try { $sp=json_decode($pmt->notes,true); } catch(\Exception $e){$sp=[];} ?>
                        <div class="text-secondary" style="font-size:11px;">
                            <?php $__currentLoopData = $sp??[]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php echo e(($s['type']??''). ': £'.number_format($s['amount']??0,2).($loop->last?'':' + ')); ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-bold text-success">£<?php echo e(number_format($pmt->amount,2)); ?></span>
                        <?php if($isSplit): ?>
                        <button type="button" class="btn btn-xs btn-outline-secondary" style="padding:1px 6px;font-size:10px;"
                            onclick="openEditSplitModal(<?php echo e($pmt->id); ?>,<?php echo e($pmt->amount); ?>,'<?php echo e(addslashes($pmt->notes)); ?>')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php else: ?>
                        <button type="button" class="btn btn-xs btn-outline-secondary" style="padding:1px 6px;font-size:10px;"
                            onclick="openEditPaymentModal(<?php echo e($pmt->id); ?>,'<?php echo e($pmt->payment_type); ?>',<?php echo e($pmt->amount); ?>,'<?php echo e(addslashes($pmt->notes??'')); ?>')">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-xs btn-outline-danger" style="padding:1px 6px;font-size:10px;"
                            onclick="deletePayment(<?php echo e($pmt->id); ?>, this)"
                            title="Delete payment">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
        </div>

        
        <button type="button" class="btn-trigger btn-trigger-disc w-100 mb-3" id="btn-disc-trigger" onclick="openDiscountModal()">
            <i class="bi bi-tag"></i> 🏷️ Discount
        </button>

        <button type="submit" class="btn-save-job"><i class="bi bi-check-circle me-2"></i>Update Job</button>
        <a href="<?php echo e(route('jobs.show',$job)); ?>" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
    </div>
</div>
</div>
</div>
</form>


<template id="device-template">
<div class="device-card">
    <div class="device-card-header">
        <span class="device-number">–</span>
        <input type="text" name="devices[__DIDX__][name]" placeholder="Device name e.g. iPhone 15 Pro"
            style="background:transparent;border:none;outline:none;font-weight:600;font-size:14px;flex:1;color:var(--bs-body-color);">
        <button type="button" onclick="removeDevice(this)" class="btn btn-sm btn-outline-danger ms-auto"><i class="bi bi-trash"></i></button>
    </div>
    <div class="device-card-body">
        <div class="row g-2 mb-3 pb-3 border-bottom">
            <div class="col-sm-4"><label class="form-label">IMEI / Serial</label><input type="text" class="form-control form-control-sm" name="devices[__DIDX__][imei]" maxlength="20"></div>
            <div class="col-sm-4"><label class="form-label">Colour</label><input type="text" class="form-control form-control-sm" name="devices[__DIDX__][color]"></div>
            <div class="col-sm-4"><label class="form-label">Warranty Expiry</label><input type="date" class="form-control form-control-sm warranty-date" name="devices[__DIDX__][warranty]"></div>
        </div>
        <div class="mb-3"><label class="form-label">🔧 Repair Types</label>
            <select name="devices[__DIDX__][repair_type_ids][]" multiple class="repair-type-select">
                <?php $__currentLoopData = $repairTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($type->id); ?>"><?php echo e($type->icon); ?> <?php echo e($type->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="row g-2 mb-3">
            <div class="col-sm-5"><label class="form-label">🗃️ Parts Used</label>
                <select name="devices[__DIDX__][part_ids][]" multiple class="part-select">
                    <?php $__currentLoopData = $parts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $part): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($part->id); ?>" data-price="<?php echo e($part->sell_price??0); ?>"><?php echo e($part->category->icon); ?> <?php echo e($part->name); ?> (<?php echo e($part->remainingStock()); ?>)</option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-sm-4"><label class="form-label">Status</label>
                <select name="devices[__DIDX__][repair_status]" class="form-select form-select-sm no-ts">
                    <?php $__currentLoopData = \App\Models\Job::statuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php echo e($s==='In Progress'?'selected':''); ?>><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-sm-3"><label class="form-label">Price £</label>
                <input type="number" name="devices[__DIDX__][repair_price]" class="form-control form-control-sm device-price" value="0" step="0.01" min="0" oninput="recalc()">
            </div>
        </div>
        <div><label class="form-label">📋 Issue / Description</label>
            <textarea class="form-control form-control-sm" name="devices[__DIDX__][issue]" rows="2"></textarea>
        </div>
    </div>
</div>
</template>


<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div><h5 class="modal-title fw-bold">💳 Add Payment</h5>
                <div class="text-secondary small mt-1">Balance: <strong class="<?php echo e($job->isPaidInFull()?'text-success':'text-danger'); ?>">£<?php echo e(number_format($job->balanceDue(),2)); ?></strong></div></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('payments.store',$job)); ?>" id="add-pay-form"><?php echo csrf_field(); ?>
                
                <input type="hidden" name="payment_type" id="ap-type-hidden" value="">
                <input type="hidden" name="amount"       id="ap-amount-hidden" value="">
                <input type="hidden" name="notes"        id="ap-notes-hidden"  value="">
                <div class="modal-body px-4 pt-3 pb-2">
                    <div class="payments-modal-section">
                        <div class="d-flex gap-2 mb-3 flex-wrap">
                            <?php $__currentLoopData = ['Cash'=>'💵','Card'=>'💳','Trade'=>'🔄']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t=>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="pay-method-btn ap-pill" id="ap-<?php echo e($t); ?>" onclick="apPill('<?php echo e($t); ?>',this)">
                                <div class="pay-icon"><?php echo e($i); ?></div><div class="pay-label"><?php echo e($t); ?></div>
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <label class="pay-method-btn ap-pill" id="ap-Split" onclick="apPill('Split',this)">
                                <div class="pay-icon">✂️</div><div class="pay-label">Split</div>
                            </label>
                        </div>
                        
                        <div id="ap-single">
                            <div class="row g-2">
                                <div class="col-7"><label class="form-label">Amount (£)</label><input type="number" id="ap-amount" class="form-control" step="0.01" min="0.01" value="<?php echo e(number_format($job->balanceDue(),2)); ?>"></div>
                                <div class="col-5"><label class="form-label">Note</label><input type="text" id="ap-notes" class="form-control" placeholder="Optional..."></div>
                            </div>
                        </div>
                        
                        <div id="ap-split" style="display:none;">
                            <div class="rounded-3 p-3 mb-2" style="background:rgba(13,110,253,.06);border:1px solid rgba(13,110,253,.15);">
                                <div class="fw-semibold small mb-2 text-primary">1st Payment</div>
                                <div class="row g-2">
                                    <div class="col-5"><label class="form-label">Method</label><select class="form-select form-select-sm no-ts" id="ap-s1-type"><option value="Cash">💵 Cash</option><option value="Card">💳 Card</option><option value="Trade">🔄 Trade</option></select></div>
                                    <div class="col-4"><label class="form-label">Amount £</label><input type="number" class="form-control form-control-sm" id="ap-s1-amt" step="0.01" min="0" placeholder="0.00"></div>
                                    <div class="col-3"><label class="form-label">Note</label><input type="text" class="form-control form-control-sm" id="ap-s1-note" placeholder="..."></div>
                                </div>
                            </div>
                            <div class="rounded-3 p-3" style="background:rgba(25,135,84,.06);border:1px solid rgba(25,135,84,.15);">
                                <div class="fw-semibold small mb-2 text-success">2nd Payment</div>
                                <div class="row g-2">
                                    <div class="col-5"><label class="form-label">Method</label><select class="form-select form-select-sm no-ts" id="ap-s2-type"><option value="Card">💳 Card</option><option value="Cash">💵 Cash</option><option value="Trade">🔄 Trade</option></select></div>
                                    <div class="col-4"><label class="form-label">Amount £</label><input type="number" class="form-control form-control-sm" id="ap-s2-amt" step="0.01" min="0" placeholder="0.00"></div>
                                    <div class="col-3"><label class="form-label">Note</label><input type="text" class="form-control form-control-sm" id="ap-s2-note" placeholder="..."></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-success fw-bold w-100 py-3" style="border-radius:12px;" onclick="submitAddPayment()">💾 Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content border-0 shadow" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0"><h5 class="modal-title fw-bold">✏️ Edit Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="edit-payment-form" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-body px-4 pt-3">
                    <div class="mb-3">
                        <label class="form-label">Type *</label>
                        <div class="d-flex gap-2">
                            <?php $__currentLoopData = ['Cash'=>'💵','Card'=>'💳','Trade'=>'🔄']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t=>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="pay-method-btn ep-pill" id="ep-pill-<?php echo e($t); ?>" onclick="epSelectPill('<?php echo e($t); ?>',this)" style="flex:1;">
                                <input type="radio" name="payment_type" value="<?php echo e($t); ?>" class="d-none">
                                <div class="pay-icon"><?php echo e($i); ?></div><div class="pay-label"><?php echo e($t); ?></div>
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="mb-3"><label class="form-label">Amount £ *</label><input type="number" name="amount" id="ep-amount" class="form-control" step="0.01" min="0.01" required></div>
                    <div><label class="form-label">Note</label><input type="text" name="notes" id="ep-notes" class="form-control"></div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2"><button class="btn btn-primary fw-bold w-100" style="border-radius:12px;">💾 Update</button></div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editSplitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0"><h5 class="modal-title fw-bold">✂️ Edit Split Payment</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form id="edit-split-form" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <input type="hidden" name="payment_type" value="Split">
                <input type="hidden" name="amount" id="es-total">
                <input type="hidden" name="notes"  id="es-notes-json">
                <div class="modal-body px-4 pt-3 pb-2">
                    <div class="rounded-3 p-3 mb-2" style="background:rgba(13,110,253,.06);border:1px solid rgba(13,110,253,.15);">
                        <div class="fw-semibold small mb-2 text-primary">1st Payment</div>
                        <div class="row g-2">
                            <div class="col-5"><label class="form-label">Method</label><select class="form-select form-select-sm no-ts" id="es1-type"><option value="Cash">💵 Cash</option><option value="Card">💳 Card</option><option value="Trade">🔄 Trade</option></select></div>
                            <div class="col-4"><label class="form-label">Amount £</label><input type="number" class="form-control form-control-sm" id="es1-amt" step="0.01" min="0" placeholder="0.00"></div>
                            <div class="col-3"><label class="form-label">Note</label><input type="text" class="form-control form-control-sm" id="es1-note" placeholder="..."></div>
                        </div>
                    </div>
                    <div class="rounded-3 p-3" style="background:rgba(25,135,84,.06);border:1px solid rgba(25,135,84,.15);">
                        <div class="fw-semibold small mb-2 text-success">2nd Payment</div>
                        <div class="row g-2">
                            <div class="col-5"><label class="form-label">Method</label><select class="form-select form-select-sm no-ts" id="es2-type"><option value="Card">💳 Card</option><option value="Cash">💵 Cash</option><option value="Trade">🔄 Trade</option></select></div>
                            <div class="col-4"><label class="form-label">Amount £</label><input type="number" class="form-control form-control-sm" id="es2-amt" step="0.01" min="0" placeholder="0.00"></div>
                            <div class="col-3"><label class="form-label">Note</label><input type="text" class="form-control form-control-sm" id="es2-note" placeholder="..."></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-primary fw-bold w-100 py-3" style="border-radius:12px;" onclick="submitEditSplit()">💾 Update Split</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="discountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:430px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold" style="font-size:18px;">🏷️ Apply Savings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-3 pb-2">

                
                <div class="rounded-3 p-3 mb-2" style="background:rgba(13,110,253,.05);border:1.5px solid rgba(13,110,253,.15);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span style="width:28px;height:28px;border-radius:8px;background:#0d6efd;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;">%</span>
                        <div>
                            <div class="fw-bold" style="font-size:13px;">Price Discount</div>
                            <div class="text-secondary" style="font-size:11px;">Reduce the job price by a percentage or fixed amount</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <label class="pay-method-btn" id="disc-pill-none"    onclick="discSelectType('',this)"       style="flex:1;"><div class="pay-icon" style="font-size:14px;">✕</div><div class="pay-label">None</div></label>
                        <label class="pay-method-btn" id="disc-pill-percent" onclick="discSelectType('percent',this)" style="flex:1;"><div class="pay-icon" style="font-size:14px;">%</div><div class="pay-label">Percent</div></label>
                        <label class="pay-method-btn" id="disc-pill-fixed"   onclick="discSelectType('fixed',this)"   style="flex:1;"><div class="pay-icon" style="font-size:14px;">£</div><div class="pay-label">Fixed</div></label>
                    </div>
                    <div id="disc-value-wrap" style="<?php echo e($job->discount_type ? '' : 'display:none;'); ?>">
                        <div class="input-group mt-2">
                            <span class="input-group-text fw-bold" id="disc-prefix"><?php echo e($job->discount_type==='percent' ? '%' : '£'); ?></span>
                            <input type="number" class="form-control form-control-lg" id="modal_discount_value"
                                step="0.01" min="0" placeholder="0" oninput="recalc()"
                                value="<?php echo e($job->discount_type ? $job->discount_value : ''); ?>"
                                style="font-size:22px;font-weight:700;">
                        </div>
                        <div class="text-primary small mt-1" id="disc-preview"></div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm w-100 mt-2 fw-semibold" onclick="applyDiscountAndClose()" style="border-radius:8px;">
                        <i class="bi bi-check-circle me-1"></i>Apply Discount
                    </button>
                </div>

                
                <div class="d-flex align-items-center gap-2 my-2">
                    <div style="flex:1;height:1px;background:var(--bs-border-color);"></div>
                    <span class="text-secondary" style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;">or apply a voucher</span>
                    <div style="flex:1;height:1px;background:var(--bs-border-color);"></div>
                </div>

                
                <div class="rounded-3 p-3" style="background:rgba(25,135,84,.05);border:1.5px solid rgba(25,135,84,.15);">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span style="width:28px;height:28px;border-radius:8px;background:#198754;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;">🎟️</span>
                        <div>
                            <div class="fw-bold" style="font-size:13px;">Voucher Code</div>
                            <div class="text-secondary" style="font-size:11px;">Apply a voucher code independent of any discount</div>
                        </div>
                    </div>

                    
                    <input type="hidden" id="voucher_code_hidden" name="voucher_code"
                        value="<?php echo e($job->voucher_code ?? ''); ?>"
                        data-amount="<?php echo e($job->voucher_amount ?? 0); ?>">

                    
                    <div id="edit-applied-voucher"
                        class="d-flex align-items-center justify-content-between p-2 mb-2 rounded-2"
                        style="background:rgba(25,135,84,.12);border:1px solid rgba(25,135,84,.25);<?php echo e($job->voucher_code ? '' : 'display:none;'); ?>">
                        <span class="text-success small fw-semibold" id="edit-applied-voucher-label">
                            ✅ <strong><?php echo e($job->voucher_code ?? ''); ?></strong><?php if($job->voucher_code): ?> — -£<?php echo e(number_format($job->voucher_amount ?? 0, 2)); ?><?php endif; ?>
                        </span>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2"
                            style="font-size:11px;border-radius:6px;" onclick="removeEditVoucher()">✕ Remove</button>
                    </div>

                    <div id="edit-voucher-list" class="mb-2" style="max-height:140px;overflow-y:auto;border:1px solid rgba(25,135,84,.2);border-radius:8px;display:none;"></div>
                    <div id="edit-voucher-list-empty" class="text-secondary small text-center py-2 mb-2" style="display:none;">No vouchers available</div>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(25,135,84,.08);border-color:rgba(25,135,84,.3);">🎟️</span>
                        <input type="text" class="form-control text-uppercase fw-bold" id="edit-voucher-input"
                            placeholder="ENTER CODE..." style="letter-spacing:.08em;border-color:rgba(25,135,84,.3);"
                            oninput="this.value=this.value.toUpperCase()">
                        <button class="btn btn-success fw-bold px-3" type="button" onclick="editApplyVoucher()">Apply</button>
                    </div>
                    <div id="edit-voucher-msg" class="mt-2 small" style="display:none;"></div>

                    <?php
                    $editVouchersJson = $vouchers->map(fn($v) => [
                        'code'        => $v->code,
                        'customer_id' => $v->customer_id,
                        'label'       => $v->type === 'percent' ? $v->value.'% off' : '£'.number_format($v->value,2).' off',
                        'min_spend'   => (float)($v->min_spend ?? 0),
                        'expires'     => $v->expires_at ? $v->expires_at->format('d M Y') : '',
                        'personal'    => (bool)$v->customer_id,
                    ])->values()->toJson();
                    ?>
                    <script id="edit-vouchers-json" type="application/json"><?php echo $editVouchersJson; ?></script>
                </div>

            </div>
            <div class="modal-footer border-0 px-4 pb-3 pt-1">
                <button type="button" class="btn btn-outline-secondary w-100" style="border-radius:10px;" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<?php echo $__env->make('jobs._customer_modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let deviceIndex     = <?php echo e($job->devices->count()); ?>;
let currentDiscType = '';

// ── Devices ──────────────────────────────────────────────────────
function addDevice() {
    const tpl = document.getElementById('device-template').innerHTML.replaceAll('__DIDX__', deviceIndex);
    const wrap = document.createElement('div'); wrap.innerHTML = tpl;
    const block = wrap.firstElementChild;
    document.getElementById('devices-container').appendChild(block);
    if (window.initTomSelect) window.initTomSelect(block);
    const wi = block.querySelector('.warranty-date');
    if (wi) { const d = new Date(); d.setDate(d.getDate()+180); wi.value = d.toISOString().split('T')[0]; }
    deviceIndex++; renumberDevices(); recalc();
    block.scrollIntoView({behavior:'smooth',block:'nearest'});
}
function removeDevice(btn) { btn.closest('.device-card').remove(); renumberDevices(); recalc(); }
function renumberDevices() {
    document.querySelectorAll('#devices-container .device-card').forEach((c,i) => {
        const n = c.querySelector('.device-number'); if(n) n.textContent = i+1;
    });
}

// ── Recalc (discount display only — payments are separate) ────────
function recalc() {
    const subtotal   = Array.from(document.querySelectorAll('.device-price')).reduce((a,i)=>a+(parseFloat(i.value)||0),0);
    const dType      = document.getElementById('discount_type').value;
    const dVal       = parseFloat(document.getElementById('discount_value').value)||0;
    const vfEl       = document.getElementById('voucher_code_hidden');
    const voucherAmt = vfEl ? (parseFloat(vfEl.dataset.amount)||0) : 0;
    const voucherCode= vfEl ? (vfEl.value || '') : '';
    const totalPaid  = parseFloat('<?php echo e($job->totalPaid()); ?>') || 0;

    let disc = 0;
    if (dType==='percent') disc = subtotal*dVal/100;
    else if (dType==='fixed') disc = Math.min(dVal,subtotal);

    // Cap voucher to available amount — never go negative
    const afterDiscBeforeVoucher = Math.max(0, subtotal - disc);
    const actualVoucherDeduction = Math.min(voucherAmt, afterDiscBeforeVoucher);
    const afterDisc  = Math.max(0, afterDiscBeforeVoucher - actualVoucherDeduction);
    const balanceDue = Math.max(0, afterDisc - totalPaid);

    document.getElementById('summary-subtotal').textContent = '£'+subtotal.toFixed(2);
    document.getElementById('summary-total').textContent    = '£'+afterDisc.toFixed(2);

    // Discount row
    const rowDisc = document.getElementById('row-discount');
    if (disc > 0) {
        rowDisc.style.display='flex';
        document.getElementById('lbl-discount').textContent = dType==='percent' ? 'Discount ('+dVal+'%)' : 'Discount (Fixed)';
        document.getElementById('val-discount').textContent = '-£'+disc.toFixed(2);
    } else { rowDisc.style.display='none'; }

    // Voucher row — show whenever applied, even at £0 subtotal
    const rowVouch = document.getElementById('row-voucher');
    if (voucherAmt > 0 && voucherCode) {
        rowVouch.style.display='flex';
        document.getElementById('lbl-voucher').textContent = '🎟️ ' + voucherCode;
        const valEl = document.getElementById('val-voucher');
        if (subtotal === 0) {
            valEl.textContent = '-£' + voucherAmt.toFixed(2) + ' (pending)';
            valEl.style.opacity = '0.6';
        } else {
            valEl.textContent = '-£' + actualVoucherDeduction.toFixed(2);
            valEl.style.opacity = '1';
        }
    } else { rowVouch.style.display='none'; }

    // Balance due box
    const boxDue  = document.getElementById('balance-box-due');
    const boxPaid = document.getElementById('balance-box-paid');
    const balEl   = document.getElementById('balance-due-amount');
    if (balanceDue <= 0) {
        if (boxDue)  boxDue.style.display  = 'none';
        if (boxPaid) boxPaid.style.display = '';
    } else {
        if (boxDue)  { boxDue.style.display = ''; }
        if (boxPaid) boxPaid.style.display  = 'none';
        if (balEl)   balEl.textContent      = '£'+balanceDue.toFixed(2);
    }

    // Pulse animation
    const t = document.getElementById('summary-total');
    t.style.transform='scale(1.06)'; setTimeout(function(){ t.style.transform=''; },150);

    // Discount modal preview
    const prev = document.getElementById('disc-preview');
    if (prev) {
        const mv = parseFloat(document.getElementById('modal_discount_value') ? document.getElementById('modal_discount_value').value : 0)||0;
        let md = 0;
        if (currentDiscType==='percent') md=subtotal*mv/100;
        else if (currentDiscType==='fixed') md=Math.min(mv,subtotal);
        prev.textContent = md>0 ? 'Saves £'+md.toFixed(2)+' on £'+subtotal.toFixed(2)+' subtotal' : '';
    }
}

// ── Delete payment via fetch (avoids nested form / accidental DELETE on job) ──
function deletePayment(paymentId, btn) {
    if (!confirm('Remove this payment?')) return;
    btn.disabled = true;
    fetch('/payments/' + paymentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: '_method=DELETE'
    })
    .then(function(r) {
        if (r.ok) {
            // Remove the payment row from DOM
            var row = btn.closest('.d-flex.align-items-center.justify-content-between');
            if (row) row.remove();
            // Reload page to refresh totals
            window.location.reload();
        } else {
            alert('Failed to delete payment. Please try again.');
            btn.disabled = false;
        }
    })
    .catch(function() {
        alert('Network error. Please try again.');
        btn.disabled = false;
    });
}

// ── Add payment modal ─────────────────────────────────────────────
let apType = '';
function openAddPaymentModal() {
    apType = '';
    document.querySelectorAll('.ap-pill').forEach(p=>p.classList.remove('active'));
    document.getElementById('ap-single').style.display = 'block';
    document.getElementById('ap-split').style.display  = 'none';
    bootstrap.Modal.getOrCreateInstance(document.getElementById('addPaymentModal')).show();
}
function apPill(type, el) {
    apType = type;
    document.querySelectorAll('.ap-pill').forEach(p=>p.classList.remove('active'));
    el.classList.add('active');
    const isSplit = type === 'Split';
    document.getElementById('ap-single').style.display = isSplit ? 'none' : 'block';
    document.getElementById('ap-split').style.display  = isSplit ? 'block' : 'none';
}
function submitAddPayment() {
    if (!apType) { alert('Please select a payment method.'); return; }
    const isSplit = apType === 'Split';
    let amount = 0, notes = '';
    if (isSplit) {
        const a1 = parseFloat(document.getElementById('ap-s1-amt').value)||0;
        const a2 = parseFloat(document.getElementById('ap-s2-amt').value)||0;
        if (a1 <= 0 && a2 <= 0) { alert('Enter at least one split amount.'); return; }
        amount = a1 + a2;
        notes  = JSON.stringify([
            {type:document.getElementById('ap-s1-type').value, amount:a1, notes:document.getElementById('ap-s1-note').value},
            {type:document.getElementById('ap-s2-type').value, amount:a2, notes:document.getElementById('ap-s2-note').value}
        ]);
    } else {
        amount = parseFloat(document.getElementById('ap-amount').value)||0;
        notes  = document.getElementById('ap-notes').value;
        if (amount <= 0) { alert('Enter a payment amount.'); return; }
    }
    document.getElementById('ap-type-hidden').value   = apType;
    document.getElementById('ap-amount-hidden').value = amount.toFixed(2);
    document.getElementById('ap-notes-hidden').value  = notes;
    document.getElementById('add-pay-form').submit();
}

// ── Edit normal payment ───────────────────────────────────────────
function epSelectPill(type, el) {
    document.querySelectorAll('.ep-pill').forEach(p=>p.classList.remove('active'));
    el.classList.add('active'); el.querySelector('input[type=radio]').checked=true;
}
function openEditPaymentModal(id, type, amount, notes) {
    document.getElementById('edit-payment-form').action = '/payments/'+id;
    document.getElementById('ep-amount').value = parseFloat(amount).toFixed(2);
    document.getElementById('ep-notes').value  = notes||'';
    document.querySelectorAll('.ep-pill').forEach(p=>p.classList.remove('active'));
    const pill = document.getElementById('ep-pill-'+type);
    if (pill) { pill.classList.add('active'); pill.querySelector('input[type=radio]').checked=true; }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editPaymentModal')).show();
}

// ── Edit split payment ────────────────────────────────────────────
function openEditSplitModal(id, amount, notesJson) {
    document.getElementById('edit-split-form').action = '/payments/'+id;
    try {
        const splits = JSON.parse(notesJson);
        const s1 = splits[0]||{}, s2 = splits[1]||{};
        document.getElementById('es1-type').value = s1.type||'Cash';
        document.getElementById('es1-amt').value  = s1.amount||'';
        document.getElementById('es1-note').value = s1.notes||'';
        document.getElementById('es2-type').value = s2.type||'Card';
        document.getElementById('es2-amt').value  = s2.amount||'';
        document.getElementById('es2-note').value = s2.notes||'';
    } catch(e) { document.getElementById('es1-amt').value = amount; }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editSplitModal')).show();
}
function submitEditSplit() {
    const a1=parseFloat(document.getElementById('es1-amt').value)||0;
    const a2=parseFloat(document.getElementById('es2-amt').value)||0;
    if (a1<=0&&a2<=0) { alert('Enter at least one amount.'); return; }
    document.getElementById('es-total').value      = (a1+a2).toFixed(2);
    document.getElementById('es-notes-json').value = JSON.stringify([
        {type:document.getElementById('es1-type').value,amount:a1,notes:document.getElementById('es1-note').value},
        {type:document.getElementById('es2-type').value,amount:a2,notes:document.getElementById('es2-note').value}
    ]);
    document.getElementById('edit-split-form').submit();
}

// ── Discount modal — same as create page ─────────────────────────
const EDIT_VOUCHERS = (function(){
    try { return JSON.parse(document.getElementById('edit-vouchers-json')?.textContent||'[]'); }
    catch(e){ return []; }
})();

function openDiscountModal() {
    document.querySelectorAll('#discountModal .pay-method-btn').forEach(b=>b.classList.remove('active'));
    document.getElementById('disc-pill-'+(currentDiscType||'none'))?.classList.add('active');
    document.getElementById('disc-value-wrap').style.display = currentDiscType ? 'block' : 'none';
    const dVal = document.getElementById('discount_value').value;
    if (dVal && currentDiscType) document.getElementById('modal_discount_value').value = dVal;
    updateDiscPrefix();
    renderEditVoucherList();
    bootstrap.Modal.getOrCreateInstance(document.getElementById('discountModal')).show();
}

function discSelectType(type, el) {
    currentDiscType = type;
    document.querySelectorAll('#discountModal .pay-method-btn').forEach(b=>b.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('disc-value-wrap').style.display = type ? 'block' : 'none';
    if (!type) document.getElementById('modal_discount_value').value = '';
    updateDiscPrefix(); recalc();
}

function updateDiscPrefix() {
    const pfx = document.getElementById('disc-prefix');
    const lbl = document.getElementById('disc-value-label');
    if (pfx) pfx.textContent = currentDiscType==='percent' ? '%' : '£';
    if (lbl) lbl.textContent = currentDiscType==='percent' ? 'Percentage Off' : 'Amount Off';
}

function renderEditVoucherList() {
    const list  = document.getElementById('edit-voucher-list');
    const empty = document.getElementById('edit-voucher-list-empty');
    if (!list) return;
    const custSel = document.querySelector('select[name="customer_id"]');
    const cid     = custSel ? custSel.value : '';

    const filtered = EDIT_VOUCHERS.filter(function(v) {
        var vc = v.customer_id;
        if (!vc || vc === 'null') return true;
        if (!cid) return false;
        return String(vc) === String(cid);
    });

    list.innerHTML = '';
    if (filtered.length === 0) {
        list.style.display = 'none';
        if (empty) empty.style.display = '';
        return;
    }
    list.style.display = ''; if (empty) empty.style.display = 'none';

    filtered.forEach(function(v, i) {
        const div = document.createElement('div');
        div.className = 'ev-list-item';
        div.style.cssText = 'padding:10px 14px;cursor:pointer;border-bottom:' + (i < filtered.length-1 ? '1px solid var(--bs-border-color)' : 'none') + ';transition:background .15s;';
        div.onmouseenter = function() { if (!div.dataset.selected) div.style.background = 'rgba(25,135,84,.06)'; };
        div.onmouseleave = function() { if (!div.dataset.selected) div.style.background = ''; };
        div.onclick = function() {
            // Clear other selections
            document.querySelectorAll('.ev-list-item').forEach(function(r) {
                delete r.dataset.selected;
                r.style.background = '';
                r.style.borderLeft = '';
                var chk = r.querySelector('.ev-check'); if (chk) chk.remove();
            });
            // Mark this one selected
            div.dataset.selected = '1';
            div.style.background = 'rgba(25,135,84,.12)';
            div.style.borderLeft = '3px solid #198754';
            var chk = document.createElement('span');
            chk.className = 'ev-check text-success fw-bold ms-2'; chk.textContent = '✓';
            div.appendChild(chk);
            document.getElementById('edit-voucher-input').value = v.code;
            editApplyVoucher();
        };
        const badge = v.personal
            ? '<span class="badge bg-info text-dark ms-1" style="font-size:9px;">Personal</span>'
            : '<span class="badge bg-secondary ms-1" style="font-size:9px;">All</span>';
        const meta = [v.label, v.min_spend > 0 ? ('· min £' + parseFloat(v.min_spend).toFixed(2)) : '', v.expires ? ('· exp ' + v.expires) : ''].filter(Boolean).join(' ');
        div.innerHTML = '<div class="d-flex align-items-center justify-content-between"><div><code class="fw-bold" style="font-size:13px;letter-spacing:.05em;">' + v.code + '</code><div class="text-secondary" style="font-size:11px;">' + meta + ' ' + badge + '</div></div><i class="bi bi-chevron-right text-secondary" style="font-size:11px;"></i></div>';
        list.appendChild(div);
    });
}

async function editApplyVoucher() {
    const code  = document.getElementById('edit-voucher-input').value.trim();
    const msgEl = document.getElementById('edit-voucher-msg');
    if (!code) return;
    msgEl.style.display='block'; msgEl.textContent='Checking...'; msgEl.className='mt-2 small text-secondary';
    try {
        const subtotal = Array.from(document.querySelectorAll('.device-price')).reduce((a,i)=>a+(parseFloat(i.value)||0),0);
        const custSel  = document.querySelector('select[name="customer_id"]');
        const cid      = custSel ? custSel.value : null;
        const res = await fetch('<?php echo e(route("jobs.check-voucher")); ?>', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>','Accept':'application/json'},
            body: JSON.stringify({code, subtotal, customer_id: cid})
        });
        const data = await res.json();
        if (data.valid) {
            msgEl.className='mt-2 small text-success';
            msgEl.textContent='✅ ' + data.message + ' — -£' + parseFloat(data.discount).toFixed(2);
            // Update hidden field
            var vf = document.getElementById('voucher_code_hidden');
            vf.value = code.toUpperCase();
            vf.dataset.amount = parseFloat(data.discount);
            // Show applied strip
            var strip = document.getElementById('edit-applied-voucher');
            var lbl   = document.getElementById('edit-applied-voucher-label');
            if (lbl)   lbl.textContent    = '✅ ' + code.toUpperCase() + ' — -£' + parseFloat(data.discount).toFixed(2);
            if (strip) strip.style.display = 'flex';
            // Update trigger button
            const btn = document.getElementById('btn-disc-trigger');
            if (btn) {
                btn.classList.add('applied');
                btn.innerHTML = '🎟️ ' + code.toUpperCase() + ' <i class="bi bi-check-circle-fill ms-1" style="font-size:11px;"></i>';
            }
            recalc();
            // Don't close modal — let user see the confirmation
        } else {
            msgEl.className='mt-2 small text-danger'; msgEl.textContent='❌ '+data.message;
            // Clear bad selection
            document.querySelectorAll('.ev-list-item').forEach(function(r) {
                delete r.dataset.selected; r.style.background=''; r.style.borderLeft='';
                var chk = r.querySelector('.ev-check'); if(chk) chk.remove();
            });
        }
    } catch(e) { msgEl.className='mt-2 small text-danger'; msgEl.textContent='Network error.'; }
}

// Remove voucher — JS only like create page
function removeEditVoucher() {
    var vf = document.getElementById('voucher_code_hidden');
    if (vf) { vf.value = ''; vf.dataset.amount = 0; }
    var strip = document.getElementById('edit-applied-voucher');
    if (strip) strip.style.display = 'none';
    var rowVouch = document.getElementById('row-voucher');
    if (rowVouch) rowVouch.style.display = 'none';
    var btn = document.getElementById('btn-disc-trigger');
    if (btn) {
        btn.classList.remove('applied');
        btn.innerHTML = '<i class="bi bi-tag me-1"></i>🏷️ Discount';
    }
    recalc();
}

function applyDiscountAndClose() {
    document.getElementById('discount_type').value  = currentDiscType;
    document.getElementById('discount_value').value = dVal;

    const btn = document.getElementById('btn-disc-trigger');
    const applied = currentDiscType && parseFloat(dVal) > 0;
    if (btn) {
        btn.classList.toggle('applied', applied);
        btn.innerHTML = applied
            ? (currentDiscType==='percent'?`🏷️ ${dVal}% Off`:`🏷️ £${parseFloat(dVal).toFixed(2)} Off`)+' <i class="bi bi-check-circle-fill ms-1" style="font-size:11px;"></i>'
            : '<i class="bi bi-tag"></i> 🏷️ Discount';
        btn.animate([{transform:'scale(1)'},{transform:'scale(1.06)'},{transform:'scale(1)'}],{duration:260,easing:'ease-out'});
    }
    bootstrap.Modal.getInstance(document.getElementById('discountModal')).hide();
    recalc();
}

document.addEventListener('DOMContentLoaded', () => {
    const dType = document.getElementById('discount_type').value;
    const dVal  = document.getElementById('discount_value').value;
    if (dType) {
        currentDiscType = dType;
        document.getElementById('disc-pill-'+dType)?.classList.add('active');
        if (dVal && parseFloat(dVal) > 0) {
            document.getElementById('modal_discount_value').value = dVal;
            updateDiscPrefix();
            const btn = document.getElementById('btn-disc-trigger');
            if (btn) {
                btn.classList.add('applied');
                btn.innerHTML = (dType==='percent'?`🏷️ ${dVal}% Off`:`🏷️ £${parseFloat(dVal).toFixed(2)} Off`)+' <i class="bi bi-check-circle-fill ms-1" style="font-size:11px;"></i>';
            }
        }
    } else {
        document.getElementById('disc-pill-none')?.classList.add('active');
    }
    recalc();

    // ── Scroll to top ─────────────────────────────────────────────
    window.scrollTo({ top: 0, behavior: 'instant' });

    // ── Load initial customer history ─────────────────────────────
    var initCustomerId = '<?php echo e($job->customer_id); ?>';
    if (initCustomerId) { loadCustomerHistory(initCustomerId, 'edit-customer'); }

    // ── Smart pin summary card ────────────────────────────────────
    var summaryCard  = document.getElementById('summary-card');
    var summaryCol   = document.getElementById('summary-col');
    var placeholder  = document.getElementById('summary-placeholder');
    var pinnedWidth  = 0;

    function updateSummaryPin() {
        if (!summaryCard || !summaryCol || window.innerWidth < 992) {
            summaryCard.classList.remove('is-pinned');
            summaryCard.style.width = '';
            placeholder.classList.remove('active');
            placeholder.style.height = '';
            pinnedWidth = 0;
            return;
        }
        var colRect = summaryCol.getBoundingClientRect();
        if (colRect.top <= 76) {
            if (!summaryCard.classList.contains('is-pinned')) {
                pinnedWidth = summaryCard.offsetWidth;
                summaryCard.style.width = pinnedWidth + 'px';
                placeholder.style.height = summaryCard.offsetHeight + 'px';
                summaryCard.classList.add('is-pinned');
                placeholder.classList.add('active');
            }
        } else {
            if (summaryCard.classList.contains('is-pinned')) {
                summaryCard.classList.remove('is-pinned');
                summaryCard.style.width = '';
                placeholder.classList.remove('active');
                placeholder.style.height = '';
                pinnedWidth = 0;
            }
        }
    }
    window.addEventListener('resize', function() {
        summaryCard.classList.remove('is-pinned');
        summaryCard.style.width = '';
        placeholder.classList.remove('active');
        placeholder.style.height = '';
        pinnedWidth = 0;
        updateSummaryPin();
    }, { passive: true });
    window.addEventListener('scroll', updateSummaryPin, { passive: true });
    updateSummaryPin();
});

// ── Customer change handler ───────────────────────────────────────
function onEditCustomerChange(sel) {
    var id      = sel.value;
    var opt     = sel.querySelector('option[value="' + id + '"]');
    var name    = opt ? opt.text.split(' · ')[0].trim() : '';
    var phone   = opt ? (opt.dataset.phone   || '') : '';
    var email   = opt ? (opt.dataset.email   || '') : '';
    var address = opt ? (opt.dataset.address || '') : '';
    updateCustomerPreview(id, { name, phone, email, address }, 'edit-customer');
}

// Wire up shared partial's onCustomerSelected callback for edit page
window.onCustomerSelected = function(id, customer) {
    window._activeCustomerSelectId = 'edit_customer_select';
    updateCustomerPreview(id, customer, 'edit-customer');
};
</script>


<?php echo $__env->make('jobs._customer_js', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/jobs/edit.blade.php ENDPATH**/ ?>