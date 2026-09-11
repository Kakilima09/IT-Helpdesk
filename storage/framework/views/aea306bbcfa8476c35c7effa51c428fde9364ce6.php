<?php
    $user = Auth::guard('customer')->user();
    $papdUnreadCount = $user ? $user->unreadNotifications()->where('data->papd_id', '!=', null)->count() : 0;
?>

<?php if($papdUnreadCount == 0): ?>
    <span class="mark-read-none fs-13"><?php echo e(lang('Mark all as read', 'notification')); ?></span>
<?php else: ?>
    <span class="mark-read-papd text-primary fs-13" style="cursor:pointer;" data-url="<?php echo e(route('papd.markAllRead')); ?>">
        <?php echo e(lang('Mark all as read', 'notification')); ?>

    </span>
<?php endif; ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/user/markasreadcountpapd.blade.php ENDPATH**/ ?>