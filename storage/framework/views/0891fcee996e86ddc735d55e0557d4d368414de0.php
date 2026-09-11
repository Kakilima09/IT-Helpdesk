<!-- Mobile Header -->
<div class="support-mobile-header clearfix">
	<div class="">
		<div class="d-flex">
			<a class="animated-arrow horizontal-navtoggle"><span></span></a>
			<span class="smallogo">

				
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

			</span>

			<div class="d-flex ms-auto pe-3">
				<div  class="dropdown header-flags text-uppercase">
					<a href="#" class="nav-link pe-1 ps-0  dropdown-toggle" data-bs-toggle="dropdown">
						<span class=""><?php echo e(app()->getLocale()); ?> </span>
					</a>
					<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-uppercase">
						

					</div>
				</div>

				<?php if(Auth::guard('customer')->check()): ?>

					<?php echo $__env->make('includes.user.mobilenotification', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

						<div class="dropdown profile-dropdown">
							<a href="#" class="nav-link pe-1 ps-0 leading-none" data-bs-toggle="dropdown">
								<span>
									<?php if(Auth::guard('customer')->check() && Auth::guard('customer')->user()->image == null): ?>

									<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>" class="avatar avatar-md bradius rounded-circle" alt="default">
									<?php else: ?>

									<img src="<?php echo e(asset('uploads/profile/'.Auth::user()->image)); ?>" alt="<?php echo e(Auth::guard('customer')->user()->image); ?>" class="avatar avatar-md bradius">
									<?php endif; ?>

								</span>
							</a>
							<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
								<div class="p-3 text-center border-bottom">
									<a href="#" class="text-center user pb-0 font-weight-bold"><?php echo e(Auth::guard('customer')->user()->username); ?></a>
									<p class="text-center user-semi-title"><?php echo e(Auth::guard('customer')->user()->email); ?></p>
								</div>
								<a class="dropdown-item d-flex" href="<?php echo e(route('client.profile')); ?>">
									<i class="feather feather-user me-3 fs-16 my-auto"></i>
									<div class="mt-1">Profile</div>
								</a>
								<a class="dropdown-item d-flex" href="<?php echo e(route('activeticket')); ?>">
									<i class="ri-ticket-2-line me-3 fs-16 my-auto"></i>
									<div class="mt-1">Tickets</div>
								</a>
								<form id="logout-form" action="<?php echo e(route('client.logout')); ?>" method="POST">
									<?php echo csrf_field(); ?>

									<button type="submit" class="dropdown-item d-flex">
										<i class="feather feather-power me-3 fs-16 my-auto"></i>
										<div class="mt-1">Log Out</div>
									</button>
							</form>
							</div>
						</div>

				<?php endif; ?>

			</div>


		</div>
	</div>
</div>
<!-- /Mobile Header -->
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/user/mobileheader.blade.php ENDPATH**/ ?>