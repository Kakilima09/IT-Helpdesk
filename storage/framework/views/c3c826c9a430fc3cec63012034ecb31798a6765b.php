<!-- Header-->
<div class="landingmain-header home-page">
	<div class="horizontal-main landing-header clearfix sticky">
		<div class="horizontal-mainwrapper container clearfix">
			<div class="d-flex">
				<a href="javascript:void(0)" class="animated-arrow horizontal-navtoggle"><span></span></a>
				<div class="headerlanding-logo">
					<a class="header-brand" href="<?php echo e(url('/')); ?>">
                        <?php if($title->image !== null): ?>

                        <img src="<?php echo e(asset('uploads/logo/logo/'.$title->image)); ?>" class="header-brand-img desktop-lgo"
                            alt="<?php echo e($title->image); ?>">
                        <?php else: ?>
                        <img src="<?php echo e(asset('uploads/logo/logo/logo-white.png')); ?>" class="header-brand-img desktop-lgo"
                            alt="logo">
                        <?php endif; ?>
                        <?php if($title->image1 !== null): ?>

                        <img src="<?php echo e(asset('uploads/logo/darklogo/'.$title->image1)); ?>"
                            class="header-brand-img light-logo" alt="<?php echo e($title->image1); ?>">
                        <?php else: ?>

                        <img src="<?php echo e(asset('uploads/logo/darklogo/logo.png')); ?>" class="header-brand-img light-logo"
                            alt="logo">

                        <?php endif; ?>

					</a>

				</div>
				<?php if(Auth::guard('customer')->check()): ?>
				<div class="d-flex order-lg-2 my-auto ms-auto d-lg-none d-block">
					<button class="navbar-toggler nav-link icon navresponsive-toggler vertical-icon ms-auto collapsed"
						type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4"
						aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
						<i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i> </button>
					<div class="mb-0 navbar navbar-expand-lg navbar-nav-right responsive-navbar navbar-dark p-0">
						<div class="navbar-collapse collapse" id="navbarSupportedContent-4">
							<ul class="d-flex ms-auto landing-header-right p-0 mb-0">

								<?php echo $__env->make('includes.user.notifynotication', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

								<li class="dropdown header-flags text-capitalize">
									<a href="#"
										class="text-capitalize dropdown-toggle nav-link icon fs-16 p-0 bg-transparent text-dark mx-5"
										data-bs-toggle="dropdown" aria-expanded="false">
										<span class=""><?php echo e(getLangName()); ?> </span>
									</a>
									<div
										class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-capitalize">
										<?php $__currentLoopData = getLanguageslist(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

										<a href="<?php echo e(langURL($lang->languagecode)); ?>" class="dropdown-item d-flex fs-13">
											<span class=""><?php echo e($lang->languagename); ?></span>
										</a>
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

									</div>
								</li>

								<li class="dropdown profile-dropdown">
									<a href="#" class="nav-link p-0 mt-1 leading-none user-img rounded-pill"
										data-bs-toggle="dropdown">
										<span>

											<?php if(Auth::guard('customer')->user()->image == null): ?>

											<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>"
												class="avatar avatar-md bradius rounded-circle" alt="default">

											<?php else: ?>

											<img src="<?php echo e(asset('uploads/profile/'.Auth::guard('customer')->user()->image)); ?>"
												alt="<?php echo e(Auth::guard('customer')->user()->image); ?>"
												class="avatar avatar-md bradius rounded-circle">

											<?php endif; ?>

										</span>
									</a>
									<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
										<div class="p-3 text-center border-bottom">
											<a href="#"
												class="text-center user pb-0 font-weight-bold"><?php echo e(Auth::guard('customer')->user()->username); ?></a>
											<p class="text-center user-semi-title">
												<?php echo e(Auth::guard('customer')->user()->email); ?>

											</p>
										</div>
										<a class="dropdown-item d-flex" href="<?php echo e(url('customer/')); ?>">
											<i class="feather feather-grid me-3 fs-16 my-auto"></i>
											<div class="mt-1"><?php echo e(lang('Dashboard', 'Menu')); ?></div>
										</a>
										<a class="dropdown-item d-flex" href="<?php echo e(url('customer/profile')); ?>">
											<i class="feather feather-user me-3 fs-16 my-auto"></i>
											<div class="mt-1"><?php echo e(lang('Profile', 'Menu')); ?></div>
										</a>
										<a class="dropdown-item d-flex" href="<?php echo e(url('customer/activeticket')); ?>">
											<i class="ri-ticket-2-line me-3 fs-16 my-auto"></i>
											<div class="mt-1"><?php echo e(lang('Tickets', 'Menu')); ?></div>
										</a>
										<form id="logout-form" action="<?php echo e(route('client.logout')); ?>" method="POST">

											<?php echo csrf_field(); ?>

											<button type="submit" class="dropdown-item d-flex">
												<i class="feather feather-power me-3 fs-16 my-auto"></i>
												<div class="mt-1"><?php echo e(lang('Logout', 'Menu')); ?></div>
											</button>
										</form>
									</div>
								</li>


							</ul>
						</div>
					</div>
				</div>
				<?php endif; ?>

				<nav class="horizontalMenu clearfix order-lg-2 my-auto ms-auto">
					<ul class="horizontalMenu-list custom-ul">
						<?php if(setting('defaultlogin_on') == 'off'): ?>

						<li>
							<a class="active" href="<?php echo e(route('client.dashboard')); ?>"><?php echo e(lang('Home', 'Menu')); ?></a>
						</li>

						<?php endif; ?>

						<?php if(setting('KNOWLEDGE_ENABLE') == 'yes'): ?>

						<li>
							<a href="<?php echo e(url('/knowledge')); ?>" class="sub-icon"><?php echo e(lang('Knowledge', 'Menu')); ?> </a>
						</li>
						<?php endif; ?>
						<?php if(setting('FAQ_ENABLE') == 'yes'): ?>

						<li>
							<a href="<?php echo e(url('/faq')); ?>" class="sub-icon"><?php echo e(lang('FAQ’s', 'Menu')); ?> </a>
						</li>
						<?php endif; ?>

						<?php if(setting('CONTACT_ENABLE') == 'yes'): ?>
						<li>
							<a href="<?php echo e(url('/contact-us')); ?>"><?php echo e(lang('Contact Us', 'Menu')); ?></a>
						</li>
						<?php endif; ?>
						<?php $__currentLoopData = $page; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pages): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php if($pages->status == '1'): ?>
						<?php if($pages->viewonpages == 'both' || $pages->viewonpages == 'header'): ?>

						<li>
							<a href="<?php echo e(url('page/' .$pages->pageslug)); ?>"><?php echo e($pages->pagename); ?></a>
						</li>

						<?php endif; ?>
						<?php endif; ?>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

						<?php if(Auth::guard('customer')->check()): ?>

						<?php echo $__env->make('includes.user.notifynotication', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

						<li class="dropdown header-flags">
							<a href="#" class="text-capitalize dropdown-toggle" data-bs-toggle="dropdown"
								aria-expanded="false">
								<span class=""><?php echo e(getLangName()); ?> </span>
							</a>
							<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-capitalize">
								<?php $__currentLoopData = getLanguageslist(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

								<a href="<?php echo e(langURL($lang->languagecode)); ?>" class="dropdown-item d-flex fs-13">
									<span class=""><?php echo e($lang->languagename); ?></span>
								</a>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

							</div>
						</li>

						<li class="dropdown profile-dropdown">
							<a href="#" class="nav-link p-0 mt-1 leading-none user-img rounded-pill" data-bs-toggle="dropdown">
								<span>

									<?php if(Auth::guard('customer')->user()->image == null): ?>

									<img src="<?php echo e(asset('uploads/profile/user-profile.png')); ?>"
										class="avatar avatar-md bradius rounded-circle" alt="default">

									<?php else: ?>

									<img src="<?php echo e(asset('uploads/profile/'.Auth::guard('customer')->user()->image)); ?>"
										alt="<?php echo e(Auth::guard('customer')->user()->image); ?>"
										class="avatar avatar-md bradius rounded-circle">

									<?php endif; ?>

								</span>
							</a>
							<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
								<div class="p-3 text-center border-bottom">
									<a href="#"
										class="text-center user pb-0 font-weight-bold"><?php echo e(Auth::guard('customer')->user()->username); ?></a>
									<p class="text-center user-semi-title"><?php echo e(Auth::guard('customer')->user()->email); ?>

									</p>
								</div>
								<a class="dropdown-item d-flex" href="<?php echo e(url('customer/')); ?>">
									<i class="feather feather-grid me-3 fs-16 my-auto"></i>
									<div class="mt-1"><?php echo e(lang('Dashboard', 'Menu')); ?></div>
								</a>
								<a class="dropdown-item d-flex" href="<?php echo e(url('customer/profile')); ?>">
									<i class="feather feather-user me-3 fs-16 my-auto"></i>
									<div class="mt-1"><?php echo e(lang('Profile', 'Menu')); ?></div>
								</a>
								<a class="dropdown-item d-flex" href="<?php echo e(url('customer/activeticket')); ?>">
									<i class="ri-ticket-2-line me-3 fs-16 my-auto"></i>
									<div class="mt-1"><?php echo e(lang('Tickets', 'Menu')); ?></div>
								</a>
								<form id="logout-form" action="<?php echo e(route('client.logout')); ?>" method="POST">

									<?php echo csrf_field(); ?>

									<button type="submit" class="dropdown-item d-flex">
										<i class="feather feather-power me-3 fs-16 my-auto"></i>
										<div class="mt-1"><?php echo e(lang('Logout', 'Menu')); ?></div>
									</button>
								</form>
							</div>
						</li>

						<?php else: ?>

						<?php if(setting('REGISTER_POPUP') == 'yes'): ?>

						<li><a href="#" data-bs-toggle="modal" data-bs-target="#loginmodal"><?php echo e(lang('Login',
								'Menu')); ?></a></li>

						<?php if(setting('REGISTER_DISABLE') == 'on'): ?>

						<li><a href="#" data-bs-toggle="modal" data-bs-target="#registermodal"><?php echo e(lang('Register',
								'Menu')); ?></a></li>

						<?php endif; ?>
						<?php else: ?>

						<li><a href="<?php echo e(url('customer/login')); ?>"><?php echo e(lang('Login', 'Menu')); ?></a></li>
						<?php if(setting('REGISTER_DISABLE') == 'on'): ?>

						<li><a href="<?php echo e(url('customer/register')); ?>"><?php echo e(lang('Register', 'Menu')); ?></a></li>
						<?php endif; ?>
						<?php endif; ?>

						<li class="dropdown header-flags">
							<a href="#" class="text-capitalize dropdown-toggle" data-bs-toggle="dropdown"
								aria-expanded="false">
								<span class=""><?php echo e(getLangName()); ?> </span>
							</a>
							<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-capitalize">
								<?php $__currentLoopData = getLanguageslist(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

								<a href="<?php echo e(langURL($lang->languagecode)); ?>" class="dropdown-item d-flex fs-13">
									<span class=""><?php echo e($lang->languagename); ?></span>
								</a>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

							</div>
						</li>

						<?php if(setting('GUEST_TICKET') == 'yes'): ?>

						<li>
							<span class="menu-btn">
								<a class="btn btn-secondary m-0" href="<?php echo e(url('/guest/openticket')); ?>">
									<i class="fa fa-paper-plane-o me-1"></i>
									<?php echo e(lang('Submit Ticket', 'Menu')); ?>

								</a>
							</span>
						</li>
						<?php endif; ?>
						<?php endif; ?>

					</ul>
				</nav>
			</div>
		</div>
	</div>
</div>
<!--Header-->
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/menu.blade.php ENDPATH**/ ?>