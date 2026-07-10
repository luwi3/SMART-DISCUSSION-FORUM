<?php if(Auth::user()->role === 'Administrator'): ?>
    <?php echo $__env->make('dashboards.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php elseif(Auth::user()->role === 'Lecturer'): ?>
    <?php echo $__env->make('dashboards.lecturer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php else: ?>
    <?php echo $__env->make('dashboards.student', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?><?php /**PATH C:\Users\dell\Herd\first-app\resources\views/dashboard.blade.php ENDPATH**/ ?>