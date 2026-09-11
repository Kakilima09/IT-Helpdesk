<?php $notifys = auth()->guard('customer')->user()->unreadNotifications()->paginate(2); $badgecount = auth()->guard('customer')->user()->unreadNotifications->count(); ?>
<li class="dropdown me-3 header-message">
    <a class="nav-link icon p-0 mt-1 badgecount" data-bs-toggle="dropdown" >
        <?php echo $__env->make('includes.user.badgecountpapd', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow p-0 notification-dropdown-container">
        <div class="dropdown-header border-bottom d-flex justify-content-between">
            
            <div class="markasreadcount">
                <?php echo $__env->make('includes.user.markasreadcountpapd', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
        </div>
        <div class="notifyreading">
            
            <?php echo $__env->make('includes.user.allnotifypapd', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
        <div class=" text-center p-2">
            <a href="<?php echo e(route('papd.notifications')); ?>" class="cmark-all"><?php echo e(lang('See All Notifications', 'notification')); ?> </a>
        </div>
    </div>

</li><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/user/notifynoticationpapd.blade.php ENDPATH**/ ?>