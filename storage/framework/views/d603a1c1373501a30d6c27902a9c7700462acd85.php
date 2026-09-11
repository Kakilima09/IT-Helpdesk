<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" >
	<head>
    	<?php echo $__env->make('includes.styles', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


		<?php if(setting('GOOGLE_ANALYTICS_ENABLE') == 'yes'): ?>

		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(setting('GOOGLE_ANALYTICS')); ?>"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', '<?php echo e(setting('GOOGLE_ANALYTICS')); ?>');
		</script>

		<?php endif; ?>

	</head>

	<body class="<?php echo e(getIsRtl()); ?>

	<?php if(setting('SPRUKOADMIN_C') == 'off'): ?>
		<?php if(setting('DARK_MODE') == 1): ?> dark-mode <?php endif; ?>
	<?php else: ?>
		<?php if(Auth::guard('customer')->check()): ?>
			<?php if(Auth::guard('customer')->check() && Auth::guard('customer')->user()->custsetting->darkmode == 1): ?> dark-mode <?php endif; ?>
		<?php else: ?>
			<?php if(setting('DARK_MODE') == 1): ?> dark-mode <?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>">

        <?php if(setting('ANNOUNCEMENT_USER') == 'non_login_users' && Auth::guard('customer')->check() == false || setting('ANNOUNCEMENT_USER') == 'all_users'): ?>
            <div class="uhelp-announcement-alertgroup">
                <?php $__currentLoopData = $announcement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if($anct->status == 1): ?>
                        <div class="alert" role="alert" style="background: linear-gradient(to right, <?php echo e($anct->primary_color); ?>, <?php echo e($anct->secondary_color); ?>);">
                            <div class="container">
                                <button type="submit" class="btn-close ms-5 float-end text-white notifyclose" data-id="<?php echo e($anct->id); ?>">×</button>
                                <div class="d-flex align-items-top">
                                    <div class="uhelp-announcement me-2">
                                        <svg class="svg-info" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                    </div>
                                    <div class="text-default d-flex align-items-top">
                                        <div class="notice-heading d-flex align-items-top flex-fill">
                                            <div>
                                                <span class="fs-15 font-weight-bold text-white flex-fill"><?php echo e($anct->title); ?></span>
                                                <span class="text-white opacity-50 mx-2"><i class="ti ti-minus"></i></span>
                                                <span class="mb-0 text-white uhelp-alert-content alert-notice"><?php echo $anct->notice; ?>

                                                    <?php if($anct->buttonon == 1): ?>
                                                    <a class="btn btn-sm ms-2 text-white text-decoration-underline" href="<?php echo e($anct->buttonurl); ?>" target="_blank"><?php echo e($anct->buttonname); ?></a>
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ancts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $announceDay = explode(',', $ancts->announcementday);
                    $now = today()->format('D');

                    ?>
                    <?php $__currentLoopData = $announceDay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announceDays): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($ancts->status == 1 && $announceDays == $now): ?>
                            <div class="alert alert-days" role="alert" style="background: linear-gradient(to right, <?php echo e($ancts->primary_color); ?>, <?php echo e($ancts->secondary_color); ?>);">
                                <div class="container">
                                    <button type="submit" class="btn-close ms-5 float-end text-white notifyclose" data-id="<?php echo e($ancts->id); ?>">×</button>
                                    <div class="d-flex align-items-top">
                                        <div class="uhelp-announcement me-2">
                                            <svg class="svg-info" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                        </div>
                                        <div class="text-default d-flex align-items-top">
                                            <div class="notice-heading d-flex align-items-top flex-fill">
                                                <div>
                                                    <span class="fs-15 font-weight-bold text-white flex-fill"><?php echo e($ancts->title); ?></span>
                                                    <span class="text-white opacity-50 mx-2"><i class="ti ti-minus"></i></span>
                                                    <span class="mb-0 text-white uhelp-alert-content alert-notice"><?php echo $ancts->notice; ?>

                                                        <?php if($ancts->buttonon == 1): ?>
                                                        <a class="btn btn-sm ms-2 text-white text-decoration-underline" href="<?php echo e($ancts->buttonurl); ?>" target="_blank"><?php echo e($ancts->buttonname); ?></a>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <?php if(setting('ANNOUNCEMENT_USER') == 'only_login_user' && Auth::guard('customer')->check() == true): ?>
                <div class="uhelp-announcement-alertgroup">
                    <?php $__currentLoopData = $announcement; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anct): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($anct->status == 1): ?>
                            <div class="alert" role="alert" style="background: linear-gradient(to right, <?php echo e($anct->primary_color); ?>, <?php echo e($anct->secondary_color); ?>);">
                                <div class="container">
                                    <button type="submit" class="btn-close ms-5 float-end text-white notifyclose" data-id="<?php echo e($anct->id); ?>">×</button>
                                    <div class="d-flex align-items-top">
                                        <div class="uhelp-announcement me-2">
                                            <svg class="svg-info" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                        </div>
                                        <div class="text-default d-flex align-items-top">
                                            <div class="notice-heading d-flex align-items-top flex-fill">
                                                <div>
                                                    <span class="fs-15 font-weight-bold text-white flex-fill"><?php echo e($anct->title); ?></span>
                                                    <span class="text-white opacity-50 mx-2"><i class="ti ti-minus"></i></span>
                                                    <span class="mb-0 text-white uhelp-alert-content alert-notice"><?php echo $anct->notice; ?>

                                                        <?php if($anct->buttonon == 1): ?>
                                                        <a class="btn btn-sm ms-2 text-white text-decoration-underline" href="<?php echo e($anct->buttonurl); ?>" target="_blank"><?php echo e($anct->buttonname); ?></a>
                                                        <?php endif; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php $__currentLoopData = $announcements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ancts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                        $announceDay = explode(',', $ancts->announcementday);
                        $now = today()->format('D');

                        ?>
                        <?php $__currentLoopData = $announceDay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $announceDays): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($ancts->status == 1 && $announceDays == $now): ?>
                                <div class="alert alert-days" role="alert" style="background: linear-gradient(to right, <?php echo e($ancts->primary_color); ?>, <?php echo e($ancts->secondary_color); ?>);">
                                    <div class="container">
                                            <button type="submit" class="btn-close ms-5 float-end text-white notifyclose" data-id="<?php echo e($ancts->id); ?>">×</button>
                                            <div class="d-flex align-items-top">
                                                <div class="uhelp-announcement me-2">
                                                    <svg class="svg-info" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                                </div>
                                                <div class="text-default d-flex align-items-top">
                                                    <div class="notice-heading d-flex align-items-top flex-fill">
                                                        <div>
                                                            <span class="fs-15 font-weight-bold text-white flex-fill"><?php echo e($ancts->title); ?></span>
                                                            <span class="text-white opacity-50 mx-2"><i class="ti ti-minus"></i></span>
                                                            <span class="mb-0 text-white uhelp-alert-content alert-notice"><?php echo $ancts->notice; ?>

                                                                <?php if($ancts->buttonon == 1): ?>
                                                                <a class="btn btn-sm ms-2 text-white text-decoration-underline" href="<?php echo e($ancts->buttonurl); ?>" target="_blank"><?php echo e($ancts->buttonname); ?></a>
                                                                <?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

				<?php echo $__env->make('includes.user.mobileheader', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				<?php echo $__env->make('includes.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

				<div class="page page-1">
					<div class="page-main">

							<?php echo $__env->yieldContent('content'); ?>

					</div>
				</div>

				<?php echo $__env->make('includes.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    	<?php echo $__env->make('includes.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		<?php if(auth()->guard()->guest()): ?>
		<?php if(customcssjs('CUSTOMCHATENABLE') == 'enable'): ?>
		<?php if(customcssjs('CUSTOMCHATUSER') == 'public'): ?>
		<?php echo customcssjs('CUSTOMCHAT') ?>
		<?php endif; ?>
		<?php endif; ?>
		<?php else: ?>
		<?php if(customcssjs('CUSTOMCHATENABLE') == 'enable'): ?>
		<?php if(customcssjs('CUSTOMCHATUSER') == 'user'): ?>
		<?php if(Auth::guard('customer')->check() && Auth::guard('customer')->user()): ?>
		<?php echo customcssjs('CUSTOMCHAT') ?>
		<?php endif; ?>
		<?php endif; ?>
		<?php if(customcssjs('CUSTOMCHATUSER') == 'public'): ?>
		<?php echo customcssjs('CUSTOMCHAT') ?>
		<?php endif; ?>
		<?php endif; ?>
	<?php endif; ?>
	<?php if(Session::has('error')): ?>
		<script>
			toastr.error("<?php echo Session::get('error'); ?>");
		</script>
	<?php elseif(Session::has('success')): ?>
		<script>
			toastr.success("<?php echo Session::get('success'); ?>");
		</script>
	<?php elseif(Session::has('info')): ?>
		<script>
			toastr.info("<?php echo Session::get('info'); ?>");
		</script>
	<?php elseif(Session::has('warning')): ?>
		<script>
			toastr.warning("<?php echo Session::get('warning'); ?>");
		</script>
	<?php endif; ?>

    <script type="text/javascript">
        "use strict";
        (function($){
            let notifyClose = document.querySelectorAll('.notifyclose');
            notifyClose.forEach(ele => {
                if(ele){
                    let id = ele.getAttribute('data-id');
                    if(getCookie(id)){
                        ele.closest('.alert').classList.add('d-none');
                    }else{
                        ele.addEventListener('click', setCookie);
                    }
                }
            })


            function setCookie($event) {
                console.log('set');
                const d = new Date();
                let id = $event.currentTarget.getAttribute('data-id');
                d.setTime(d.getTime() + (30 * 60 * 1000));
                let expires = "expires=" + d.toUTCString();
                document.cookie = id + "=" + 'announcement_close' + ";" + expires + ";path=/";
                $event.currentTarget.closest('.alert').classList.add('d-none');
            }

            function getCookie(cname) {
                let name = cname + "=";
                let decodedCookie = decodeURIComponent(document.cookie);
                let ca = decodedCookie.split(';');
                for(let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                    }
                }
                return '';
            }
        })(jQuery);
    </script>

			<?php if(setting('REGISTER_POPUP') == 'yes'): ?>
			<?php if(!Auth::guard('customer')->check()): ?>

			<?php echo $__env->make('user.auth.modalspopup.register', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<?php echo $__env->make('user.auth.modalspopup.login', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<?php echo $__env->make('user.auth.modalspopup.forgotpassword', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<?php endif; ?>
			<?php endif; ?>

			<?php if(setting('GUEST_TICKET') == 'yes'): ?>

				<?php echo $__env->make('guestticket.guestmodal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

			<?php endif; ?>

			<?php echo $__env->yieldContent('modal'); ?>

	</body>
</html>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/layouts/master.blade.php ENDPATH**/ ?>