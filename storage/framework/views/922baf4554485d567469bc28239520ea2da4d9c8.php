<!-- Mobile Header -->
<div class="support-mobile-header clearfix">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <!-- Toggle Button -->
            <a class="animated-arrow horizontal-navtoggle me-2">
                <span></span>
            </a>
            
            <!-- Logo -->
            <span class="smallogo me-auto">
                <?php
                    $logo = $title->image ?? 'logo-white.png';
                    $darkLogo = $title->image1 ?? 'logo.png';
                    $mobileLogo = $title->image2 ?? 'icon.png';
                    $mobileDarkLogo = $title->image3 ?? 'icon-white.png';
                ?>
                <img src="<?php echo e(asset('uploads/logo/logo/'.$logo)); ?>" class="header-brand-img dark-logo" alt="logo">
                <img src="<?php echo e(asset('uploads/logo/darklogo/'.$darkLogo)); ?>" class="header-brand-img desktop-lgo" alt="dark-logo">
                <img src="<?php echo e(asset('uploads/logo/icon/'.$mobileLogo)); ?>" class="header-brand-img mobile-logo" alt="mobile-logo">
                <img src="<?php echo e(asset('uploads/logo/darkicon/'.$mobileDarkLogo)); ?>" class="header-brand-img darkmobile-logo" alt="mobile-dark-logo">
            </span>

            <!-- Right Icons -->
            <div class="d-flex align-items-center">
                <!-- Language -->
                <div class="dropdown header-flags text-uppercase me-2">
                    <a href="#" class="nav-link p-0 dropdown-toggle" data-bs-toggle="dropdown">
                        <span class=""><?php echo e(app()->getLocale()); ?></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-uppercase">
                        <?php $__currentLoopData = getLanguageslist(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lang): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(langURL($lang->languagecode)); ?>" class="dropdown-item d-flex fs-13">
                                <span class=""><?php echo e($lang->languagename); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <?php if(Auth::guard('customer')->check()): ?>
                    <!-- Notifikasi PAPD -->
                    <?php echo $__env->make('includes.user.allnotifypapd', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    <!-- Profile -->
                    <div class="dropdown profile-dropdown ms-2">
                        <a href="#" class="nav-link p-0 leading-none" data-bs-toggle="dropdown">
                            <span>
                                <?php
                                    $avatar = Auth::guard('customer')->user()->image ?? 'user-profile.png';
                                ?>
                                <img src="<?php echo e(asset('uploads/profile/'.$avatar)); ?>" class="avatar avatar-sm bradius rounded-circle" alt="avatar" style="width: 32px; height: 32px; object-fit: cover;">
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <div class="p-3 text-center border-bottom">
                                <a href="#" class="text-center user pb-0 font-weight-bold"><?php echo e(Auth::guard('customer')->user()->username); ?></a>
                                <p class="text-center user-semi-title small"><?php echo e(Auth::guard('customer')->user()->email); ?></p>
                            </div>
                            <a class="dropdown-item d-flex" href="<?php echo e(route('papd.index')); ?>">
                                <i class="feather feather-grid me-3 fs-16 my-auto"></i>
                                <div class="mt-1"><?php echo e(lang('PAPD Dashboard', 'Menu')); ?></div>
                            </a>
                            <a class="dropdown-item d-flex" href="<?php echo e(route('client.profile')); ?>">
                                <i class="feather feather-user me-3 fs-16 my-auto"></i>
                                <div class="mt-1"><?php echo e(lang('Profile', 'Menu')); ?></div>
                            </a>
                            <a class="dropdown-item d-flex" href="<?php echo e(route('papd.create')); ?>">
                                <i class="ri-ticket-2-line me-3 fs-16 my-auto"></i>
                                <div class="mt-1"><?php echo e(lang('Buat PAPD', 'Menu')); ?></div>
                            </a>
                            <form id="logout-form" action="<?php echo e(route('client.logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item d-flex">
                                    <i class="feather feather-power me-3 fs-16 my-auto"></i>
                                    <div class="mt-1"><?php echo e(lang('Logout', 'Menu')); ?></div>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- /Mobile Header --><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/user/mobileheaderpapd.blade.php ENDPATH**/ ?>