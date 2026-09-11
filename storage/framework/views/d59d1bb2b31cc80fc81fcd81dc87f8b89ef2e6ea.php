<?php
    $badgecount = auth()->guard('customer')->user()
        ? auth()->guard('customer')->user()->unreadNotifications()
            ->where('data->papd_id', '!=', null)
            ->count()
        : 0;
?>

<?php if($badgecount > 0): ?>
    <span class="badge badge-success badge-counter pulse-success side-badge papd-badge"><?php echo e($badgecount); ?></span>
<?php else: ?>
    <span class="badge badge-gray papd-badge">0</span>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/user/badgecountpapd.blade.php ENDPATH**/ ?>