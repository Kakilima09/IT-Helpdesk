

<?php $notifys = auth()->user()->unreadNotifications()->paginate(2); $badgecount = auth()->user()->unreadNotifications->count(); ?>
<a class="nav-link icon ps-0 ms-0" data-bs-toggle="dropdown" id="badgecount">
	<?php echo $__env->make('includes.admin.badgecount', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</a>
<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated p-0 notification-dropdown-container">
	<div class="header-dropdown-list message-menu" id="message-menu">
		<div class="dropdown-header border-bottom d-flex justify-content-between">

			<div id="markasreadcount">
				<?php echo $__env->make('includes.admin.markasreadcount', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			</div>
		</div>
		<div id="notifyreading">
			<?php echo $__env->make('includes.admin.allnotify', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		</div>

	</div>
	<div class="text-center p-2">
		<a href="<?php echo e(route('notificationpage')); ?>" class="smark-all"><?php echo e(lang('See All Notifications', 'notification')); ?> </a>
	</div>
</div>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/admin/notification.blade.php ENDPATH**/ ?>