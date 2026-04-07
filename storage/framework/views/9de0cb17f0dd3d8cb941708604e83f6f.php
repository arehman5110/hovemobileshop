
<?php
    $cfg = \App\Helpers\JobStatus::config($status ?? '');
?>
<span class="badge <?php echo e($cfg['badge']); ?> rounded-pill" style="font-size:11px;">
    <i class="bi <?php echo e($cfg['bi']); ?> me-1"></i><?php echo e($status); ?>

</span>
<?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/jobs/_status_badge.blade.php ENDPATH**/ ?>