<!--app header-->
<div class="app-header header header-main">
	<div class="container-fluid">
		<div class="d-flex align-items-center justify-content-between">
			<a class="header-brand mt-0" href="<?php echo e(url('admin')); ?>">
			
			<?php if($title->image == null): ?>

			<img src="<?php echo e(asset('uploads/logo/logo/logo-white.png')); ?>" class="header-brand-img dark-logo" alt="logo">
			<?php else: ?>

			<img src="<?php echo e(asset('uploads/logo/logo/'.$title->image)); ?>" class="header-brand-img dark-logo" alt="logo">
			<?php endif; ?>

			
			<?php if($title->image1 == null): ?>

			<img src="<?php echo e(asset('uploads/logo/darklogo/logo.png')); ?>" class="header-brand-img desktop-lgo" alt="dark-logo">
			<?php else: ?>

			<img src="<?php echo e(asset('uploads/logo/darklogo/'.$title->image1)); ?>" class="header-brand-img desktop-lgo" alt="dark-logo">
			<?php endif; ?>

			
			<?php if($title->image2 == null): ?>

			<img src="<?php echo e(asset('uploads/logo/icon/icon.png')); ?>" class="header-brand-img mobile-logo" alt="mobile-logo">
			<?php else: ?>

			<img src="<?php echo e(asset('uploads/logo/icon/'.$title->image2)); ?>" class="header-brand-img mobile-logo" alt="mobile-logo">
			<?php endif; ?>

			
			<?php if($title->image3 == null): ?>

			<img src="<?php echo e(asset('uploads/logo/darkicon/icon-white.png')); ?>" class="header-brand-img darkmobile-logo" alt="mobile-dark-logo">
			<?php else: ?>

			<img src="<?php echo e(asset('uploads/logo/darkicon/'.$title->image3)); ?>" class="header-brand-img darkmobile-logo" alt="mobile-dark-logo">
			<?php endif; ?>

			</a>
			<div class="app-sidebar__toggle me-md-5 ms-md-2 mx-0" data-bs-toggle="sidebar">
				<a class="open-toggle" href="#">
					<i class="feather feather-menu"></i>
				</a>
				<a class="close-toggle" href="#">
					<i class="feather feather-x"></i>
				</a>
			</div>
			<div class="header-buttons-main mt-0">
				<a class="btn btn-outline-light header-buttons text-center" href="<?php echo e(url('/admin/createticket')); ?>"><i class="fa fa-paper-plane-o pe-lg-2"></i><span class="d-m-none"><?php echo e(lang('Create Ticket', 'Menu')); ?></span></a>
				<a class="btn btn-outline-light header-buttons text-center visitsite ms-2" href="<?php echo e(route('home')); ?>" target="_blank"><i class="fe fe-home pe-lg-2"></i><span class="d-m-none"><?php echo e(lang('Home Page', 'Menu')); ?></span></a>

			</div><!-- SEARCH -->
			<div class="d-flex order-lg-2 my-auto ms-sm-auto dropdown-container align-items-center">

				<div class="dropdown header-flags ms-1">

					<a href="#" class="text-capitalize dropdown-toggle" data-bs-toggle="dropdown">
						<?php echo e(getLangName()); ?>



					</a>
					<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-capitalize">

						<?php $__currentLoopData = getLanguageslist(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

							<a href="<?php echo e(langURL($lang->languagecode)); ?>" class="dropdown-item d-flex fs-13 <?php echo e(getLangName() == $lang->languagename ? 'active':''); ?>">
							<span class=""><?php echo e($lang->languagename); ?></span>
							</a>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

					</div>
				</div>

				<div class="dropdown header-fullscreen">
					<a class="nav-link icon full-screen-link pe-0 me-0">
						<i class="feather feather-maximize fullscreen-button fullscreen header-icons"></i>
						<i class="feather feather-minimize fullscreen-button exit-fullscreen header-icons"></i>
					</a>
				</div>
				<?php echo $__env->make('includes.admin.notification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				<div class="dropdown profile-dropdown ps-0 ps-lg-2">
					<a href="#" class="nav-link pe-1 ps-0 leading-none" data-bs-toggle="dropdown">
						<span>
							<?php if(Auth::user()->image == null): ?>

								<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>" class="avatar avatar-md bradius rounded-circle" alt="default">
							<?php else: ?>

								<img src="<?php echo e(asset('uploads/profile/'.Auth::user()->image)); ?>" class="avatar avatar-md bradius rounded-circle " alt="<?php echo e(Auth::user()->image); ?>">
							<?php endif; ?>

						</span>
					</a>
					<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated">
						<div class="p-3 text-center border-bottom">
							<a href="<?php echo e(url('/admin/profile')); ?>" class="text-center user pb-0 font-weight-bold"><?php echo e(Auth::user()->name); ?></a>
							<?php if(!empty(Auth::user()->getRoleNames()[0])): ?>
							<p class="text-center user-semi-title"><?php echo e(Auth::user()->getRoleNames()[0]); ?></p>
							<?php endif; ?>
						</div>
						<a class="dropdown-item d-flex" href="<?php echo e(url('/admin/profile')); ?>">
							<i class="feather feather-user me-3 fs-16 my-auto"></i>
							<div class="mt-1"><?php echo e(lang('Profile', 'Menu')); ?></div>
						</a>
						<form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST">
							<?php echo csrf_field(); ?>

							<button type="submit" class="dropdown-item d-flex">
								<i class="feather feather-power me-3 fs-16 my-auto"></i>
								<div class="mt-1" ><?php echo e(lang('Logout', 'Menu')); ?></div>
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!--/app header-->
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/admin/menu.blade.php ENDPATH**/ ?>