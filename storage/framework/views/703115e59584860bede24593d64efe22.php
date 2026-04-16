<?php $__env->startSection('title','Job #'.$job->id); ?>

<?php $__env->startPush('styles'); ?>
<style>
.show-card { border:none;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08); }
[data-bs-theme="dark"] .show-card { box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4); }
.bill-total { font-family:'Syne',sans-serif;font-size:36px;font-weight:800;line-height:1; }
.device-repair-card { border:1px solid var(--bs-border-color);border-radius:12px;overflow:hidden;margin-bottom:12px; }
.device-repair-card:last-child { margin-bottom:0; }
.device-repair-header { background:var(--bs-tertiary-bg);padding:12px 16px;display:flex;align-items:center;justify-content:space-between; }
.repair-tag { display:inline-flex;align-items:center;gap:4px;background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);border-radius:6px;padding:3px 9px;font-size:12px;font-weight:500; }
/* Summary card — sticky mobile, JS-controlled fixed on desktop */
.summary-sticky { position:sticky;top:16px; }
@media(min-width:992px){
    .summary-sticky.is-fixed {
        position:fixed;
        top:80px;
        width:340px;
        max-height:calc(100vh - 96px);
        overflow-y:auto;
    }
}
/* Status pill buttons */
.status-pill { padding:6px 14px;border-radius:20px;font-size:12px;font-weight:600;border:2px solid transparent;cursor:pointer;transition:all .15s;background:transparent; }
.status-pill:hover { opacity:.85;transform:translateY(-1px); }
.status-pill.active-status { color:#fff !important; }
<?php foreach(\App\Helpers\JobStatus::STATUSES as $s => $cfg): ?>
.status-pill[data-status="<?php echo e($s); ?>"] { border-color:<?php echo e($cfg['color']); ?>;color:<?php echo e($cfg['color']); ?>; }
.status-pill[data-status="<?php echo e($s); ?>"].active-status { background:<?php echo e($cfg['color']); ?>;color:#fff; }
<?php endforeach; ?>
/* Pay method pills */
.pay-pill { flex:1;text-align:center;padding:10px 6px;border:2px solid var(--bs-border-color);border-radius:10px;cursor:pointer;transition:all .15s;user-select:none; }
.pay-pill.active { border-color:#198754;background:rgba(25,135,84,.1);color:#198754; }
.pay-pill .pay-icon { font-size:20px; }
.pay-pill .pay-label { font-size:11px;font-weight:600;margin-top:3px; }
/* Action trigger buttons */
.btn-trigger { width:100%;border:2px solid #0d6efd;border-radius:12px;padding:11px;background:rgba(13,110,253,.06);color:#0d6efd;font-size:13px;font-weight:700;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:6px; }
.btn-trigger:hover { background:rgba(13,110,253,.12);transform:translateY(-1px); }
.btn-trigger.applied { box-shadow:0 0 0 3px rgba(13,110,253,.2); }
.btn-trigger-disc { border-color:#e67e22;background:rgba(230,126,34,.06);color:#e67e22; }
.btn-trigger-disc:hover { background:rgba(230,126,34,.12); }
.btn-trigger-disc.applied { box-shadow:0 0 0 3px rgba(230,126,34,.2); }
.payments-modal-section { background:var(--bs-tertiary-bg);border-radius:12px;padding:16px;margin-bottom:12px; }
.fl { font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.6; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="d-flex align-items-start gap-3 mb-4 flex-wrap">
    <a href="<?php echo e(route('jobs.index')); ?>" class="btn btn-outline-secondary btn-sm mt-1">←</a>
    <div class="flex-grow-1">
        <h2 class="syne mb-1" style="font-size:22px;font-weight:800;">Job #<?php echo e($job->id); ?></h2>
        <div class="text-secondary small"><?php echo e($job->customer->name); ?> · <?php echo e($job->date_in->format('d M Y')); ?></div>
        
        <div class="d-flex gap-2 mt-2 flex-wrap">
            <?php $__currentLoopData = \App\Models\Job::statuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $cfg = \App\Helpers\JobStatus::config($s); ?>
            <form method="POST" action="<?php echo e(route('jobs.update-status',$job)); ?>" class="d-inline">
                <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                <input type="hidden" name="status" value="<?php echo e($s); ?>">
                <button type="submit" class="status-pill <?php echo e($job->status===$s?'active-status':''); ?>" data-status="<?php echo e($s); ?>">
                    <?php echo e($cfg['icon']); ?> <?php echo e($s); ?>

                </button>
            </form>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap align-items-start">
        <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#emailModal"><i class="bi bi-envelope me-1"></i>Email</button>
        <button class="btn btn-outline-secondary btn-sm" onclick="printReceipt()" id="receipt-btn"><i class="bi bi-printer me-1"></i>Print Receipt</button>
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#refundModal"><i class="bi bi-arrow-return-left me-1"></i>Refund</button>
        <a href="<?php echo e(route('jobs.edit',$job)); ?>" class="btn btn-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit</a>
    </div>
</div>

<div class="row g-4">
<div class="col-lg-8 d-flex flex-column gap-4">

    
    <div class="show-card card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3">
                <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0099ff);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#fff;flex-shrink:0;">
                    <?php echo e(strtoupper(substr($job->customer->name,0,1))); ?>

                </div>
                <div class="flex-grow-1">
                    <a href="<?php echo e(route('customers.show',$job->customer)); ?>" class="fw-bold text-decoration-none" style="font-size:15px;"><?php echo e($job->customer->name); ?></a>
                    <div class="d-flex gap-3 mt-1 flex-wrap">
                        <?php if($job->customer->phone): ?><span class="text-secondary small"><i class="bi bi-telephone me-1"></i><?php echo e($job->customer->phone); ?></span><?php endif; ?>
                        <?php if($job->customer->email): ?><span class="text-secondary small"><i class="bi bi-envelope me-1"></i><?php echo e($job->customer->email); ?></span><?php endif; ?>
                        <?php if($job->customer->address): ?><span class="text-secondary small"><i class="bi bi-geo-alt me-1"></i><?php echo e($job->customer->address); ?></span><?php endif; ?>
                    </div>
                </div>
                <?php if($job->date_out): ?><div class="text-secondary small flex-shrink-0">Due: <?php echo e($job->date_out->format('d M Y')); ?></div><?php endif; ?>
                <div class="flex-shrink-0">
                    <?php if(($job->device_location ?? 'With Us') === 'With Us'): ?>
                    <span class="badge bg-primary rounded-pill" style="font-size:11px;">📦 With Us</span>
                    <?php else: ?>
                    <span class="badge bg-warning text-dark rounded-pill" style="font-size:11px;">👤 With Customer</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if($job->notes): ?><div class="mt-3 pt-3 border-top small text-secondary"><i class="bi bi-sticky me-1"></i><?php echo e($job->notes); ?></div><?php endif; ?>
        </div>
    </div>

    
    <div class="show-card card">
        <div class="card-header px-4 py-3 fw-bold d-flex align-items-center gap-2" style="border-radius:16px 16px 0 0;border-bottom:1px solid var(--bs-border-color);">
            <i class="bi bi-phone"></i> Devices & Repairs
            <span class="badge bg-secondary ms-1"><?php echo e($job->devices->count()); ?></span>
        </div>
        <div class="card-body p-4">
            <?php $__currentLoopData = $job->devices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $device): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $deviceStatus = $device->repairItems->first()?->status ?? 'In Progress';
                $statusColor  = \App\Helpers\JobStatus::config($deviceStatus)['color'];
            ?>
            <div class="device-repair-card">
                <div class="device-repair-header">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:18px;">📱</span>
                        <div>
                            <div class="fw-bold" style="font-size:14px;"><?php echo e($device->name); ?></div>
                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                <?php if($device->imei): ?><span class="text-secondary" style="font-size:11px;"><code><?php echo e($device->imei); ?></code></span><?php endif; ?>
                                <?php if($device->color): ?><span class="text-secondary" style="font-size:11px;">🎨 <?php echo e($device->color); ?></span><?php endif; ?>
                                <?php if($device->warranty): ?><span class="text-success" style="font-size:11px;">🛡️ <?php echo e($device->warranty); ?></span><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="fw-bold text-primary" style="font-size:16px;font-family:'Syne',sans-serif;">£<?php echo e(number_format($device->totalPrice(),2)); ?></div>
                        
                        <div class="dropdown">
                            <button class="btn btn-sm dropdown-toggle fw-semibold"
                                style="font-size:11px;padding:4px 12px;border-radius:20px;border:2px solid <?php echo e($statusColor); ?>;color:<?php echo e($statusColor); ?>;background:<?php echo e($statusColor); ?>18;white-space:nowrap;"
                                data-bs-toggle="dropdown">
                                <?php echo e($deviceStatus); ?>

                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width:160px;border-radius:12px;overflow:hidden;font-size:13px;">
                                <?php $__currentLoopData = \App\Models\Job::statuses(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($s !== $deviceStatus): ?>
                                <li>
                                    <form method="POST" action="<?php echo e(route('devices.update-status', $device)); ?>">
                                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="status" value="<?php echo e($s); ?>">
                                        <button type="submit" class="dropdown-item" style="padding:8px 16px;">
                                            <?php echo e(\App\Helpers\JobStatus::config($s)['icon']); ?> <?php echo e($s); ?>

                                        </button>
                                    </form>
                                </li>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="p-3">
                    <div class="row g-3">
                        <div class="col-sm-5">
                            <div class="fl mb-1">Repairs</div>
                            <div class="d-flex flex-wrap gap-1">
                                <?php $__empty_1 = true; $__currentLoopData = $device->repairItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php if($r->repairType): ?><span class="repair-tag"><?php echo e($r->repairType->icon); ?> <?php echo e($r->repairType->name); ?></span><?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><span class="text-secondary small">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php $partsUsed=$device->repairItems->filter(fn($r)=>$r->part)->pluck('part'); ?>
                        <?php if($partsUsed->isNotEmpty()): ?>
                        <div class="col-sm-4">
                            <div class="fl mb-1">Parts</div>
                            <div class="d-flex flex-wrap gap-1">
                                <?php $__currentLoopData = $partsUsed; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><span class="repair-tag"><?php echo e($p->name); ?></span><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php if($device->repairItems->first()?->issue): ?>
                        <div class="col-sm-3">
                            <div class="fl mb-1">Issue</div>
                            <div class="small"><?php echo e($device->repairItems->first()->issue); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    
    <div class="show-card card">
        <div class="card-header px-4 py-3 d-flex justify-content-between align-items-center fw-bold" style="border-radius:16px 16px 0 0;border-bottom:1px solid var(--bs-border-color);">
            <div class="d-flex align-items-center gap-2"><i class="bi bi-credit-card"></i> Payments</div>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#paymentModal"><i class="bi bi-plus-lg me-1"></i>Add</button>
        </div>
        <div class="card-body p-0">
            <?php if($job->payments->isEmpty()): ?>
            <div class="text-center text-secondary py-4 small">No payments recorded yet.</div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead style="background:var(--bs-tertiary-bg);">
                        <tr>
                            <th class="px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:.6;">Date</th>
                            <th style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:.6;">Type</th>
                            <th style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:.6;">Amount</th>
                            <th style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:.6;">Notes</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $job->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pmt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $isSplit  = $pmt->notes && str_starts_with(trim($pmt->notes), '[');
                        $splits   = [];
                        $notesDisplay = $pmt->notes ?? '—';
                        $typeDisplay  = $pmt->payment_type;
                        if ($isSplit) {
                            try {
                                $decoded = json_decode($pmt->notes, true);
                                if (is_array($decoded)) { $splits = $decoded; $typeDisplay = 'Split'; $notesDisplay = ''; }
                                else { $isSplit = false; }
                            } catch(\Exception $e) { $isSplit = false; }
                        }
                        $ptc = match($typeDisplay){ 'Cash'=>'success','Card'=>'primary','Trade'=>'secondary','Split'=>'info',default=>'primary' };
                    ?>
                    <tr>
                        <td class="px-4 text-secondary"><?php echo e($pmt->created_at->format('d M Y')); ?></td>
                        <td><span class="badge rounded-pill bg-<?php echo e($ptc); ?>" style="font-size:10px;"><?php echo e($typeDisplay); ?></span></td>
                        <td class="fw-bold text-success">£<?php echo e(number_format($pmt->amount,2)); ?></td>
                        <td class="text-secondary" style="font-size:12px;">
                            <?php if($isSplit): ?>
                                <?php $__currentLoopData = $splits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div><span class="badge bg-light text-dark border me-1" style="font-size:10px;"><?php echo e($s['type']??''); ?></span>£<?php echo e(number_format($s['amount']??0,2)); ?><?php echo e(!empty($s['notes'])?' · '.$s['notes']:''); ?></div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <?php echo e($notesDisplay); ?>

                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-3">
                            <?php if($isSplit): ?>
                            <button class="btn btn-sm btn-outline-secondary me-1"
                                onclick="openEditSplit(<?php echo e($pmt->id); ?>,<?php echo e($pmt->amount); ?>,'<?php echo e(addslashes($pmt->notes)); ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php else: ?>
                            <button class="btn btn-sm btn-outline-secondary me-1"
                                onclick="openEditPayment(<?php echo e($pmt->id); ?>,'<?php echo e($pmt->payment_type); ?>',<?php echo e($pmt->amount); ?>,'<?php echo e(addslashes($pmt->notes??'')); ?>')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <?php endif; ?>
                            <form method="POST" action="<?php echo e(route('payments.destroy',$pmt)); ?>" class="d-inline" onsubmit="return confirm('Remove this payment?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    
    <?php if($job->refunds->isNotEmpty()): ?>
    <div class="show-card card">
        <div class="card-header px-4 py-3 fw-bold" style="border-radius:16px 16px 0 0;border-bottom:1px solid var(--bs-border-color);">
            <i class="bi bi-arrow-return-left me-2"></i>Refunds
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead style="background:var(--bs-tertiary-bg);">
                    <tr>
                        <th class="px-4" style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:.6;">Date</th>
                        <th style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:.6;">Method</th>
                        <th style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:.6;">Amount</th>
                        <th style="font-size:10px;font-weight:700;text-transform:uppercase;opacity:.6;">Reason</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $job->refunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ref): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td class="px-4 text-secondary"><?php echo e($ref->created_at->format('d M Y')); ?></td>
                    <td><span class="badge rounded-pill bg-warning text-dark" style="font-size:10px;"><?php echo e($ref->refund_method); ?></span></td>
                    <td class="fw-bold text-danger">-£<?php echo e(number_format($ref->amount,2)); ?></td>
                    <td class="text-secondary"><?php echo e($ref->reason ?? '—'); ?></td>
                    <td class="text-end pe-3">
                        <form method="POST" action="<?php echo e(route('refunds.destroy',$ref)); ?>" class="d-inline" onsubmit="return confirm('Remove?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>


<div class="col-lg-4">
<div class="show-card card summary-sticky">
    <div class="card-body p-4">

        
        <div class="text-center pb-3 mb-3 border-bottom">
            <div class="text-secondary small mb-1">Total Due</div>
            <div class="bill-total <?php echo e($job->isPaidInFull()?'text-success':'text-primary'); ?>">
                £<?php echo e(number_format($job->totalAfterDiscount(),2)); ?>

            </div>
        </div>

        
        <div class="d-flex flex-column gap-2 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary small">Subtotal</span>
                <span class="fw-semibold">£<?php echo e(number_format($job->subtotal(),2)); ?></span>
            </div>
            <?php if($job->discountAmount()>0): ?>
            <div class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background:rgba(220,53,69,.06);">
                <span class="text-secondary small">🏷️ Discount<?php echo e($job->discount_type==='percent'?' ('.$job->discount_value.'%)':''); ?></span>
                <span class="text-danger fw-semibold small">-£<?php echo e(number_format($job->discountAmount(),2)); ?></span>
            </div>
            <?php endif; ?>
            <?php if($job->voucher_amount>0): ?>
            <div class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background:rgba(25,135,84,.06);">
                <span class="text-secondary small">🎟️ <?php echo e($job->voucher_code); ?></span>
                <span class="text-success fw-semibold small">-£<?php echo e(number_format($job->voucher_amount,2)); ?></span>
            </div>
            <?php endif; ?>
            <?php if($job->totalPaid()>0): ?>
            <div class="d-flex justify-content-between align-items-center p-2 rounded-2" style="background:rgba(25,135,84,.06);">
                <span class="text-secondary small">✅ Paid</span>
                <span class="text-success fw-bold">£<?php echo e(number_format($job->totalPaid(),2)); ?></span>
            </div>
            <?php endif; ?>
            <?php if($job->cashRefunded()>0): ?>
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-secondary small">↩️ Refunded</span>
                <span class="text-danger small fw-semibold">-£<?php echo e(number_format($job->cashRefunded(),2)); ?></span>
            </div>
            <?php endif; ?>
        </div>

        
        <?php if($job->isPaidInFull()): ?>
        <div class="text-center py-3 mb-3 rounded-3 fw-bold" style="background:rgba(25,135,84,.1);border:2px solid rgba(25,135,84,.3);color:#198754;font-size:15px;">✅ Fully Paid</div>
        <?php else: ?>
        <div class="d-flex align-items-center justify-content-between p-3 mb-3 rounded-3" style="background:rgba(220,53,69,.08);border:1px solid rgba(220,53,69,.2);">
            <span class="fw-bold">Balance Due</span>
            <span class="fw-bold text-danger" style="font-size:22px;font-family:'Syne',sans-serif;">£<?php echo e(number_format($job->balanceDue(),2)); ?></span>
        </div>
        <?php endif; ?>

        
        <div class="d-flex gap-2 mb-3">
            <button type="button" class="btn-trigger flex-fill" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="bi bi-credit-card-2-front"></i> 💳 Payment
            </button>
            <button type="button" class="btn-trigger btn-trigger-disc flex-fill"
                data-bs-toggle="modal" data-bs-target="#discountModal"
                id="btn-disc-trigger">
                <i class="bi bi-tag"></i> 🏷️ Discount
            </button>
        </div>
        <form method="POST" action="<?php echo e(route('jobs.destroy',$job)); ?>" onsubmit="return confirm('Permanently delete this job and all its data?')">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit" class="btn btn-outline-secondary w-100" style="border-radius:12px;padding:10px;font-weight:600;color:#6c757d;">
                <i class="bi bi-archive me-2"></i>Remove Job
            </button>
        </form>
    </div>
</div>
</div>
</div>


<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div><h5 class="modal-title fw-bold">💳 Add Payment</h5>
                <div class="text-secondary small mt-1">Balance: <strong class="<?php echo e($job->isPaidInFull()?'text-success':'text-danger'); ?>">£<?php echo e(number_format($job->balanceDue(),2)); ?></strong></div></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('payments.store',$job)); ?>" id="pay-form"><?php echo csrf_field(); ?>
                <div class="modal-body px-4 pt-3 pb-2">
                    <div class="payments-modal-section">
                        
                        <div class="d-flex gap-2 mb-3 flex-wrap">
                            <?php $__currentLoopData = ['Cash'=>'💵','Card'=>'💳','Trade'=>'🔄']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t=>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="pay-pill" id="pp-<?php echo e($t); ?>" onclick="pmtPill('<?php echo e($t); ?>',this)">
                                <input type="radio" name="payment_type" value="<?php echo e($t); ?>" class="d-none">
                                <div class="pay-icon"><?php echo e($i); ?></div><div class="pay-label"><?php echo e($t); ?></div>
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <label class="pay-pill" id="pp-Split" onclick="pmtPill('Split',this)">
                                <input type="radio" name="payment_type" value="Split" class="d-none">
                                <div class="pay-icon">✂️</div><div class="pay-label">Split</div>
                            </label>
                        </div>

                        
                        <div id="pmt-single">
                            <div class="row g-2">
                                <div class="col-7"><label class="fl">Amount (£)</label><input type="number" id="pmt-amount" name="amount" class="form-control" step="0.01" min="0.01" value="<?php echo e(number_format($job->balanceDue(),2)); ?>"></div>
                                <div class="col-5"><label class="fl">Note</label><input type="text" id="pmt-note" name="notes" class="form-control" placeholder="Optional..."></div>
                            </div>
                        </div>

                        
                        <div id="pmt-split" style="display:none;">
                            <div class="rounded-3 p-3 mb-2" style="background:rgba(13,110,253,.06);border:1px solid rgba(13,110,253,.15);">
                                <div class="fw-semibold small mb-2 text-primary">1st Payment</div>
                                <div class="row g-2">
                                    <div class="col-5"><label class="fl">Method</label><select class="form-select form-select-sm no-ts" id="s1-type"><option value="Cash">💵 Cash</option><option value="Card">💳 Card</option><option value="Trade">🔄 Trade</option></select></div>
                                    <div class="col-4"><label class="fl">Amount £</label><input type="number" class="form-control form-control-sm" id="s1-amt" step="0.01" min="0" placeholder="0.00"></div>
                                    <div class="col-3"><label class="fl">Note</label><input type="text" class="form-control form-control-sm" id="s1-note" placeholder="..."></div>
                                </div>
                            </div>
                            <div class="rounded-3 p-3" style="background:rgba(25,135,84,.06);border:1px solid rgba(25,135,84,.15);">
                                <div class="fw-semibold small mb-2 text-success">2nd Payment</div>
                                <div class="row g-2">
                                    <div class="col-5"><label class="fl">Method</label><select class="form-select form-select-sm no-ts" id="s2-type"><option value="Card">💳 Card</option><option value="Cash">💵 Cash</option><option value="Trade">🔄 Trade</option></select></div>
                                    <div class="col-4"><label class="fl">Amount £</label><input type="number" class="form-control form-control-sm" id="s2-amt" step="0.01" min="0" placeholder="0.00"></div>
                                    <div class="col-3"><label class="fl">Note</label><input type="text" class="form-control form-control-sm" id="s2-note" placeholder="..."></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-success fw-bold w-100 py-3" style="border-radius:12px;" onclick="submitPayment()">💾 Record Payment</button>
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
                        <label class="fl mb-2">Payment Type *</label>
                        <div class="d-flex gap-2">
                            <?php $__currentLoopData = ['Cash'=>'💵','Card'=>'💳','Trade'=>'🔄']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t=>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="pay-pill ep-pill" id="ep-pill-<?php echo e($t); ?>" onclick="epSelectPill('<?php echo e($t); ?>',this)" style="flex:1;">
                                <input type="radio" name="payment_type" value="<?php echo e($t); ?>" class="d-none">
                                <div class="pay-icon"><?php echo e($i); ?></div><div class="pay-label"><?php echo e($t); ?></div>
                            </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="mb-3"><label class="fl">Amount £ *</label><input type="number" name="amount" id="ep-amount" class="form-control" step="0.01" min="0.01" required></div>
                    <div><label class="fl">Note</label><input type="text" name="notes" id="ep-notes" class="form-control"></div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2"><button class="btn btn-primary fw-bold w-100" style="border-radius:12px;">💾 Update</button></div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="editSplitModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-bold">✂️ Edit Split Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit-split-form" method="POST"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <input type="hidden" name="payment_type" value="Split">
                <div class="modal-body px-4 pt-3 pb-2">
                    <div class="rounded-3 p-3 mb-2" style="background:rgba(13,110,253,.06);border:1px solid rgba(13,110,253,.15);">
                        <div class="fw-semibold small mb-2 text-primary">1st Payment</div>
                        <div class="row g-2">
                            <div class="col-5"><label class="fl">Method</label>
                                <select class="form-select form-select-sm no-ts" id="es1-type">
                                    <option value="Cash">💵 Cash</option><option value="Card">💳 Card</option><option value="Trade">🔄 Trade</option>
                                </select>
                            </div>
                            <div class="col-4"><label class="fl">Amount £</label><input type="number" class="form-control form-control-sm" id="es1-amt" step="0.01" min="0" placeholder="0.00"></div>
                            <div class="col-3"><label class="fl">Note</label><input type="text" class="form-control form-control-sm" id="es1-note" placeholder="..."></div>
                        </div>
                    </div>
                    <div class="rounded-3 p-3" style="background:rgba(25,135,84,.06);border:1px solid rgba(25,135,84,.15);">
                        <div class="fw-semibold small mb-2 text-success">2nd Payment</div>
                        <div class="row g-2">
                            <div class="col-5"><label class="fl">Method</label>
                                <select class="form-select form-select-sm no-ts" id="es2-type">
                                    <option value="Card">💳 Card</option><option value="Cash">💵 Cash</option><option value="Trade">🔄 Trade</option>
                                </select>
                            </div>
                            <div class="col-4"><label class="fl">Amount £</label><input type="number" class="form-control form-control-sm" id="es2-amt" step="0.01" min="0" placeholder="0.00"></div>
                            <div class="col-3"><label class="fl">Note</label><input type="text" class="form-control form-control-sm" id="es2-note" placeholder="..."></div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="amount" id="es-total">
                    <input type="hidden" name="notes"  id="es-notes-json">
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="button" class="btn btn-primary fw-bold w-100 py-3" style="border-radius:12px;" onclick="submitEditSplit()">💾 Update Split Payment</button>
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
                        <label class="pay-pill" id="disc-pill-none"    onclick="discSelectType('',this)"       style="flex:1;"><div class="pay-icon" style="font-size:14px;">✕</div><div class="pay-label">None</div></label>
                        <label class="pay-pill" id="disc-pill-percent" onclick="discSelectType('percent',this)" style="flex:1;"><div class="pay-icon" style="font-size:14px;">%</div><div class="pay-label">Percent</div></label>
                        <label class="pay-pill" id="disc-pill-fixed"   onclick="discSelectType('fixed',this)"   style="flex:1;"><div class="pay-icon" style="font-size:14px;">£</div><div class="pay-label">Fixed</div></label>
                    </div>
                    <div id="disc-value-wrap" style="<?php echo e($job->discount_type ? '' : 'display:none;'); ?>">
                        <div class="input-group mt-2">
                            <span class="input-group-text fw-bold" id="disc-prefix"><?php echo e($job->discount_type==='percent' ? '%' : '£'); ?></span>
                            <input type="number" class="form-control form-control-lg" id="disc-val"
                                step="0.01" min="0" placeholder="0"
                                value="<?php echo e($job->discount_type ? $job->discount_value : ''); ?>"
                                style="font-size:22px;font-weight:700;" oninput="updateDiscPrev()">
                        </div>
                        <div class="text-primary small mt-1" id="disc-prev"></div>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm w-100 mt-2 fw-semibold" onclick="saveDiscount()" style="border-radius:8px;">
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
                    <?php if($job->voucher_code): ?>
                    <div class="d-flex align-items-center justify-content-between p-2 mb-2 rounded-2" style="background:rgba(25,135,84,.12);border:1px solid rgba(25,135,84,.25);">
                        <span class="text-success small fw-semibold">✅ <strong><?php echo e($job->voucher_code); ?></strong> — -£<?php echo e(number_format($job->voucher_amount,2)); ?></span>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" style="font-size:11px;border-radius:6px;" onclick="removeVoucher()">✕ Remove</button>
                    </div>
                    <?php endif; ?>
                    <div id="show-voucher-list" class="mb-2" style="max-height:140px;overflow-y:auto;border:1px solid rgba(25,135,84,.2);border-radius:8px;display:none;"></div>
                    <div id="show-voucher-list-empty" class="text-secondary small text-center py-2 mb-2" style="display:none;">No vouchers available for this customer</div>
                    <div class="input-group">
                        <span class="input-group-text" style="background:rgba(25,135,84,.08);border-color:rgba(25,135,84,.3);">🎟️</span>
                        <input type="text" class="form-control text-uppercase fw-bold" id="voucher-input-modal"
                            placeholder="ENTER CODE..."
                            style="letter-spacing:.08em;border-color:rgba(25,135,84,.3);"
                            oninput="this.value=this.value.toUpperCase()" value="">
                        <button class="btn btn-success fw-bold px-3" type="button" onclick="applyVoucherCode()">Apply</button>
                    </div>
                    <div id="voucher-msg" class="mt-2 small" style="display:none;"></div>
                </div>

            </div>
            <div class="modal-footer border-0 px-4 pb-3 pt-1">
                <button type="button" class="btn btn-outline-secondary w-100" style="border-radius:10px;" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<?php
$vouchersJson = $vouchers->map(fn($v) => [
    'code'        => $v->code,
    'customer_id' => $v->customer_id,
    'label'       => $v->type === 'percent' ? $v->value.'% off' : '£'.number_format($v->value, 2).' off',
    'min_spend'   => (float)($v->min_spend ?? 0),
    'expires'     => $v->expires_at ? $v->expires_at->format('d M Y') : '',
    'personal'    => (bool)$v->customer_id,
])->values()->toJson();
?>
<script id="show-vouchers-json" type="application/json"><?php echo $vouchersJson; ?></script>


<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0"><h5 class="modal-title fw-bold"><i class="bi bi-arrow-return-left me-2 text-warning"></i>Issue Refund</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="<?php echo e(route('refunds.store',$job)); ?>"><?php echo csrf_field(); ?>
                <div class="modal-body px-4 pt-3">
                    <div class="d-flex justify-content-between p-3 mb-3 rounded-3" style="background:var(--bs-tertiary-bg);">
                        <span class="text-secondary small">Total Paid</span><span class="fw-bold text-success">£<?php echo e(number_format($job->totalPaid(),2)); ?></span>
                    </div>
                    <div class="mb-3"><label class="fl">Method *</label><select name="refund_method" class="form-select no-ts" required><option value="Cash">💵 Cash</option><option value="Card">💳 Card</option><option value="Store Credit">🏪 Store Credit</option></select></div>
                    <div class="mb-3"><label class="fl">Amount £ *</label><input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="<?php echo e($job->totalPaid()); ?>" value="<?php echo e(number_format($job->totalPaid(),2)); ?>" required></div>
                    <div><label class="fl">Reason</label><input type="text" name="reason" class="form-control" placeholder="e.g. Faulty repair..."></div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2"><button class="btn btn-warning fw-bold w-100 py-3" style="border-radius:12px;">↩️ Issue Refund</button></div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <div><h5 class="modal-title fw-bold"><i class="bi bi-envelope me-2"></i>Send Receipt</h5><div class="text-secondary small"><?php echo e($job->customer->name); ?> · Job #<?php echo e($job->id); ?></div></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div id="email-loading" class="modal-body text-center py-5 text-secondary"><div class="spinner-border spinner-border-sm me-2"></div>Loading...</div>
            <div id="email-error" class="modal-body px-4" style="display:none;"><div class="alert alert-danger mb-0">Could not load template.</div></div>
            <form id="email-form-inner" method="POST" action="<?php echo e(route('jobs.send-email',$job)); ?>" style="display:none;"><?php echo csrf_field(); ?>
                <div class="modal-body px-4 pt-3">
                    <div class="mb-3"><label class="fl">To *</label><input type="email" name="to" id="email-to" class="form-control" required></div>
                    <div class="mb-3"><label class="fl">Subject *</label><input type="text" name="subject" id="email-subject" class="form-control" required></div>
                    <div><label class="fl d-flex justify-content-between"><span>Message *</span><a href="<?php echo e(route('settings.index')); ?>" target="_blank" class="text-primary" style="font-size:11px;text-transform:none;">Edit template</a></label>
                        <textarea name="body" id="email-body" class="form-control" rows="10" required style="font-family:monospace;font-size:12px;"></textarea></div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-2">
                    <button type="submit" class="btn btn-primary fw-bold"><i class="bi bi-send me-1"></i>Send</button>
                    <a href="<?php echo e(route('jobs.receipt',$job)); ?>" target="_blank" class="btn btn-outline-secondary">🧾 Preview</a>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// ── Payment pills + Split ─────────────────────────────────────────
let currentPmtType = '';
function pmtPill(type, el) {
    currentPmtType = type;
    document.querySelectorAll('#paymentModal .pay-pill').forEach(p=>p.classList.remove('active'));
    el.classList.add('active');
    el.querySelector('input[type=radio]').checked = true;
    const isSplit = type==='Split';
    document.getElementById('pmt-single').style.display = isSplit?'none':'block';
    document.getElementById('pmt-split').style.display  = isSplit?'block':'none';
    if (isSplit) { document.getElementById('pmt-amount').value=''; }
    else { document.getElementById('s1-amt').value=''; document.getElementById('s2-amt').value=''; }
}

function submitPayment() {
    if (!currentPmtType) { alert('Please select a payment method.'); return; }
    if (currentPmtType === 'Split') {
        const a1 = parseFloat(document.getElementById('s1-amt').value)||0;
        const a2 = parseFloat(document.getElementById('s2-amt').value)||0;
        if (a1<=0 && a2<=0) { alert('Enter at least one split amount.'); return; }
        document.getElementById('pmt-amount').value = (a1+a2).toFixed(2);
        document.getElementById('pmt-note').value   = JSON.stringify([
            {type:document.getElementById('s1-type').value,amount:a1,notes:document.getElementById('s1-note').value},
            {type:document.getElementById('s2-type').value,amount:a2,notes:document.getElementById('s2-note').value}
        ]);
        document.querySelector('input[name="payment_type"][value="Split"]').checked = true;
    }
    document.getElementById('pay-form').submit();
}

// ── Edit split payment ────────────────────────────────────────────
function openEditSplit(id, amount, notesJson) {
    document.getElementById('edit-split-form').action = '/payments/' + id;
    try {
        const splits = JSON.parse(notesJson);
        const s1 = splits[0] || {};
        const s2 = splits[1] || {};
        // Set 1st
        const t1 = document.getElementById('es1-type');
        t1.value = s1.type || 'Cash';
        if (t1.tomselect) t1.tomselect.setValue(s1.type || 'Cash');
        document.getElementById('es1-amt').value  = s1.amount || '';
        document.getElementById('es1-note').value = s1.notes  || '';
        // Set 2nd
        const t2 = document.getElementById('es2-type');
        t2.value = s2.type || 'Card';
        if (t2.tomselect) t2.tomselect.setValue(s2.type || 'Card');
        document.getElementById('es2-amt').value  = s2.amount || '';
        document.getElementById('es2-note').value = s2.notes  || '';
    } catch(e) {
        document.getElementById('es1-amt').value = amount;
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editSplitModal')).show();
}

function submitEditSplit() {
    const a1   = parseFloat(document.getElementById('es1-amt').value)  || 0;
    const a2   = parseFloat(document.getElementById('es2-amt').value)  || 0;
    const t1   = document.getElementById('es1-type').value;
    const t2   = document.getElementById('es2-type').value;
    const n1   = document.getElementById('es1-note').value;
    const n2   = document.getElementById('es2-note').value;
    if (a1 <= 0 && a2 <= 0) { alert('Enter at least one amount.'); return; }
    document.getElementById('es-total').value      = (a1 + a2).toFixed(2);
    document.getElementById('es-notes-json').value = JSON.stringify([
        {type:t1, amount:a1, notes:n1},
        {type:t2, amount:a2, notes:n2}
    ]);
    document.getElementById('edit-split-form').submit();
}

// ── Direct thermal print receipt ─────────────────────────────────
function printReceipt() {
    const btn = document.getElementById('receipt-btn');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading...';

    let frame = document.getElementById('receipt-print-frame');
    if (!frame) {
        frame = document.createElement('iframe');
        frame.id = 'receipt-print-frame';
        frame.style.cssText = 'position:fixed;left:-9999px;top:-9999px;width:0;height:0;border:0;';
        document.body.appendChild(frame);
    }

    frame.src = '<?php echo e(route("jobs.receipt", $job)); ?>';

    frame.onload = function() {
        try {
            frame.contentWindow.focus();
            frame.contentWindow.print();
        } catch(e) {
            // Fallback: open in new tab if cross-origin issue
            window.open('<?php echo e(route("jobs.receipt", $job)); ?>', '_blank');
        }
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-printer me-1"></i>Print Receipt';
    };
}
(function() {
    const card  = document.querySelector('.summary-sticky');
    const col   = document.querySelector('.col-lg-4');
    if (card && col && window.innerWidth >= 992) {
        function updateFixed() {
            if (window.innerWidth < 992) { card.classList.remove('is-fixed'); return; }
            const colTop = col.getBoundingClientRect().top;
            if (colTop <= 80) {
                card.classList.add('is-fixed');
            } else {
                card.classList.remove('is-fixed');
            }
        }
        window.addEventListener('scroll', updateFixed, { passive: true });
        window.addEventListener('resize', updateFixed, { passive: true });
        updateFixed();
    }
})();
function epSelectPill(type, el) {
    document.querySelectorAll('.ep-pill').forEach(p=>p.classList.remove('active'));
    el.classList.add('active');
    el.querySelector('input[type=radio]').checked = true;
}

function openEditPayment(id, type, amount, notes) {
    document.getElementById('edit-payment-form').action = '/payments/' + id;
    document.getElementById('ep-amount').value = parseFloat(amount).toFixed(2);
    document.getElementById('ep-notes').value  = notes || '';
    // Highlight correct pill
    document.querySelectorAll('.ep-pill').forEach(p => p.classList.remove('active'));
    const pill = document.getElementById('ep-pill-' + type);
    if (pill) {
        pill.classList.add('active');
        pill.querySelector('input[type=radio]').checked = true;
    }
    bootstrap.Modal.getOrCreateInstance(document.getElementById('editPaymentModal')).show();
}

// ── Discount modal ────────────────────────────────────────────────
let currentDiscType = '<?php echo e($job->discount_type ?? ""); ?>';

const SHOW_VOUCHERS = (function(){
    try {
        var el = document.getElementById('show-vouchers-json');
        return el ? JSON.parse(el.textContent || '[]') : [];
    } catch(e) { return []; }
})();

function openDiscountModal() {
    try {
        var pills = document.querySelectorAll('#discountModal .pay-pill');
        for (var i = 0; i < pills.length; i++) { pills[i].classList.remove('active'); }
        var activePill = document.getElementById('disc-pill-' + (currentDiscType || 'none'));
        if (activePill) { activePill.classList.add('active'); }
        var wrap = document.getElementById('disc-value-wrap');
        if (wrap) { wrap.style.display = currentDiscType ? 'block' : 'none'; }
        updateDiscPrefix();
        updateDiscPrev();
        renderShowVoucherList();
    } catch(e) { console.error('discount modal setup:', e); }
}

document.getElementById('discountModal').addEventListener('show.bs.modal', openDiscountModal);

function discSelectType(type, el) {
    currentDiscType = type;
    document.querySelectorAll('#discountModal .pay-pill').forEach(p=>p.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('disc-value-wrap').style.display = type ? 'block' : 'none';
    if (!type) document.getElementById('disc-val').value = '';
    updateDiscPrefix();
    updateDiscPrev();
}

function updateDiscPrefix() {
    const pfx = document.getElementById('disc-prefix');
    const lbl = document.getElementById('disc-value-label');
    if (pfx) pfx.textContent = currentDiscType==='percent' ? '%' : '£';
    if (lbl) lbl.textContent = currentDiscType==='percent' ? 'Percentage Off' : 'Amount Off';
}

function updateDiscPrev() {
    const subtotal = <?php echo e($job->subtotal()); ?>;
    const val = parseFloat(document.getElementById('disc-val')?.value)||0;
    let disc = 0;
    if (currentDiscType==='percent') disc = subtotal*val/100;
    else if (currentDiscType==='fixed') disc = Math.min(val, subtotal);
    const prev = document.getElementById('disc-prev');
    if (prev) { prev.textContent = disc > 0 ? ('Saves £' + disc.toFixed(2) + ' on £<?php echo e(number_format($job->subtotal(),2)); ?> subtotal') : ''; }
}
document.getElementById('disc-val')?.addEventListener('input', updateDiscPrev);

function renderShowVoucherList() {
    var list  = document.getElementById('show-voucher-list');
    var empty = document.getElementById('show-voucher-list-empty');
    if (!list) { return; }
    var cid = '<?php echo e($job->customer_id); ?>';
    var filtered = SHOW_VOUCHERS.filter(function(v) {
        var vc = v.customer_id;
        if (!vc || vc === 'null') { return true; }
        if (!cid) { return false; }
        return String(vc) === String(cid);
    });
    list.innerHTML = '';
    if (filtered.length === 0) {
        list.style.display = 'none';
        if (empty) { empty.style.display = ''; }
    } else {
        list.style.display = '';
        if (empty) { empty.style.display = 'none'; }
        filtered.forEach(function(v, i) {
            var div = document.createElement('div');
            div.style.cssText = 'padding:10px 14px;cursor:pointer;border-bottom:' + (i < filtered.length-1 ? '1px solid var(--bs-border-color)' : 'none') + ';transition:background .15s;';
            div.onmouseenter = function() { div.style.background = 'rgba(13,110,253,.06)'; };
            div.onmouseleave = function() { div.style.background = ''; };
            div.onclick = function() {
                var inp = document.getElementById('voucher-input-modal');
                if (inp) { inp.value = v.code; }
                applyVoucherCode();
            };
            var badge = v.personal ? '<span class="badge bg-info text-dark ms-1" style="font-size:9px;">Personal</span>' : '<span class="badge bg-secondary ms-1" style="font-size:9px;">All</span>';
            var meta = [v.label, v.min_spend > 0 ? ('· min £' + parseFloat(v.min_spend).toFixed(2)) : '', v.expires ? ('· exp ' + v.expires) : ''].filter(Boolean).join(' ');
            div.innerHTML = '<div class="d-flex align-items-center justify-content-between"><div><code class="fw-bold" style="font-size:13px;">' + v.code + '</code><div class="text-secondary" style="font-size:11px;">' + meta + ' ' + badge + '</div></div></div>';
            list.appendChild(div);
        });
    }
}

// Save discount (% or £) — posts to dedicated route, doesn't touch voucher
function saveDiscount() {
    const val  = document.getElementById('disc-val').value || 0;
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route("jobs.update-discount", $job)); ?>';
    form.innerHTML = `
        <input name="_token"         value="<?php echo e(csrf_token()); ?>">
        <input name="discount_type"  value="${currentDiscType}">
        <input name="discount_value" value="${val}">
    `;
    document.body.appendChild(form);
    form.submit();
}

// Apply voucher code — allows replacing existing voucher
async function applyVoucherCode() {
    const code  = document.getElementById('voucher-input-modal').value.trim();
    const msgEl = document.getElementById('voucher-msg');
    if (!code) return;
    msgEl.style.display='block'; msgEl.textContent='Checking...'; msgEl.className='mt-2 small text-secondary';
    try {
        const res = await fetch('<?php echo e(route("jobs.check-voucher")); ?>', {
            method:'POST',
            headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>','Accept':'application/json'},
            body: JSON.stringify({code, subtotal:<?php echo e($job->subtotal()); ?>, customer_id:<?php echo e($job->customer_id ?? 'null'); ?>})
        });
        const data = await res.json();
        if (data.valid) {
            msgEl.className='mt-2 small text-success';
            msgEl.textContent='✅ '+data.message+' — applying...';
            const f = document.createElement('form');
            f.method='POST'; f.action='<?php echo e(route("jobs.apply-voucher",$job)); ?>';
            f.innerHTML=`<input name="_token" value="<?php echo e(csrf_token()); ?>"><input name="voucher_code" value="${code.toUpperCase()}">`;
            document.body.appendChild(f); f.submit();
        } else {
            msgEl.className='mt-2 small text-danger'; msgEl.textContent='❌ '+data.message;
        }
    } catch(e) { msgEl.className='mt-2 small text-danger'; msgEl.textContent='Network error.'; }
}

// Remove existing voucher
function removeVoucher() {
    if (!confirm('Remove the applied voucher?')) return;
    const f = document.createElement('form');
    f.method='POST'; f.action='<?php echo e(route("jobs.remove-voucher",$job)); ?>';
    f.innerHTML=`<input name="_token" value="<?php echo e(csrf_token()); ?>">`;
    document.body.appendChild(f); f.submit();
}


// ── Email modal ───────────────────────────────────────────────────
document.getElementById('emailModal').addEventListener('show.bs.modal', async function() {
    document.getElementById('email-loading').style.display='block';
    document.getElementById('email-form-inner').style.display='none';
    document.getElementById('email-error').style.display='none';
    try {
        const res = await fetch('<?php echo e(route("jobs.email-preview",$job)); ?>');
        const d   = await res.json();
        document.getElementById('email-to').value      = d.customer_email;
        document.getElementById('email-subject').value = d.subject;
        document.getElementById('email-body').value    = d.body;
        document.getElementById('email-loading').style.display='none';
        document.getElementById('email-form-inner').style.display='block';
    } catch(e) {
        document.getElementById('email-loading').style.display='none';
        document.getElementById('email-error').style.display='block';
    }
});

// Init discount pill
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('disc-pill-'+(currentDiscType||'none'))?.classList.add('active');
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/jobs/show.blade.php ENDPATH**/ ?>