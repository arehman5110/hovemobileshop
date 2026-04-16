
<?php
    $selectedStatus = $selected ?? old('status', 'In Progress');
    $fieldName      = $name ?? 'status';
    $extraClass     = $class ?? '';
?>
<select class="form-select <?php echo e($extraClass); ?>" name="<?php echo e($fieldName); ?>">
    <?php $__currentLoopData = \App\Helpers\JobStatus::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($s); ?>" <?php echo e($selectedStatus === $s ? 'selected' : ''); ?>>
        <?php echo e(\App\Helpers\JobStatus::config($s)['icon']); ?> <?php echo e($s); ?>

    </option>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</select>
<?php /**PATH D:\Claud AI\new18\mobileshop\resources\views/jobs/_status_select.blade.php ENDPATH**/ ?>