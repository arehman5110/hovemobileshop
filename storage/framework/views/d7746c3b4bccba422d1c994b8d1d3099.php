<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?php echo e($sale->id); ?></title>
    <style>
        * { margin:0;padding:0;box-sizing:border-box; }
        body { font-family:'Courier New',monospace;font-size:12px;background:#fff;color:#000;width:300px;margin:0 auto;padding:10px; }
        .center { text-align:center; }
        .bold { font-weight:bold; }
        .line { border-top:1px dashed #000;margin:8px 0; }
        .row { display:flex;justify-content:space-between;margin-bottom:3px; }
        .big { font-size:18px;font-weight:bold; }
        .logo { font-size:20px;font-weight:bold;letter-spacing:2px; }
        @media print {
            body { width:80mm; }
            .no-print { display:none; }
        }
    </style>
</head>
<body>
<div class="center" style="margin-bottom:10px;">
    <div class="logo">📱 MOBILE SHOP</div>
    <div style="font-size:11px;">Repair Tracker</div>
    <div style="font-size:10px;margin-top:4px;"><?php echo e(now()->format('d/m/Y H:i')); ?></div>
</div>

<div class="line"></div>

<div style="margin-bottom:4px;">
    <div><span class="bold">Receipt #<?php echo e($sale->id); ?></span></div>
    <div>Customer: <?php echo e($sale->customerLabel()); ?></div>
    <?php if($sale->user): ?><div>Served by: <?php echo e($sale->user->name); ?></div><?php endif; ?>
</div>

<div class="line"></div>

<?php $__currentLoopData = $sale->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="row">
    <span><?php echo e($item->name); ?> x<?php echo e($item->quantity); ?></span>
    <span>£<?php echo e(number_format($item->total,2)); ?></span>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<div class="line"></div>

<div class="row"><span>Subtotal</span><span>£<?php echo e(number_format($sale->subtotal,2)); ?></span></div>
<?php if($sale->discount_value > 0): ?>
<div class="row"><span>Discount</span><span>-£<?php echo e(number_format($sale->discount_type==='percent'?$sale->subtotal*$sale->discount_value/100:$sale->discount_value,2)); ?></span></div>
<?php endif; ?>
<?php if($sale->voucher_amount > 0): ?>
<div class="row"><span>Voucher (<?php echo e($sale->voucher_code); ?>)</span><span>-£<?php echo e(number_format($sale->voucher_amount,2)); ?></span></div>
<?php endif; ?>

<div class="line"></div>
<div class="row bold big"><span>TOTAL</span><span>£<?php echo e(number_format($sale->total,2)); ?></span></div>
<div class="line"></div>

<div class="row"><span>Payment</span><span><?php echo e($sale->payment_method); ?></span></div>
<div class="row"><span>Paid</span><span>£<?php echo e(number_format($sale->paid,2)); ?></span></div>
<?php if($sale->change_given > 0): ?>
<div class="row bold"><span>Change</span><span>£<?php echo e(number_format($sale->change_given,2)); ?></span></div>
<?php endif; ?>

<div class="line"></div>
<div class="center" style="margin-top:8px;">
    <div>Thank you for your business!</div>
    <div style="font-size:10px;margin-top:4px;"><?php echo e(config('app.url')); ?></div>
</div>

<div class="no-print" style="text-align:center;margin-top:20px;">
    <button onclick="window.print()" style="padding:8px 24px;font-size:14px;cursor:pointer;border-radius:8px;border:1px solid #ccc;">🖨️ Print</button>
    <button onclick="window.close()" style="padding:8px 24px;font-size:14px;cursor:pointer;border-radius:8px;border:1px solid #ccc;margin-left:8px;">Close</button>
</div>

<script>window.onload = function(){ window.print(); }</script>
</body>
</html>
<?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/pos/receipt.blade.php ENDPATH**/ ?>