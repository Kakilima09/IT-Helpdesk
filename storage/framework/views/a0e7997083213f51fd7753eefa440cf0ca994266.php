<i class="feather feather-bell header-icon"></i>
<!-- Counter - Alerts -->
<?php $badgecount = auth()->guard('customer')->user()->unreadNotifications->count() ?>
<?php if($badgecount == '0'): ?>

<span class="badge badge-gray">0</span>
<?php else: ?>
<span class="badge badge-success badge-counter pulse-success side-badge"><?php echo e($badgecount); ?></span>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/user/badgecount.blade.php ENDPATH**/ ?>