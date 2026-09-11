<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="ltr">
	<head>

		<!-- Meta data -->
		<meta charset="UTF-8">
		<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
		<meta content="<?php echo e($seopage->description ? $seopage->description :''); ?>" name="description">
		<meta content="<?php echo e($seopage->author ? $seopage->author :''); ?>" name="author">
		<meta name="keywords" content="<?php echo e($seopage->keywords ? $seopage->keywords :''); ?>"/>
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

		<!-- Title -->
		<title><?php echo e($title->title); ?></title>

		<?php if($title->image4 == null): ?>

		<!--Favicon -->
		<link rel="icon" href="<?php echo e(asset('uploads/logo/favicons/favicon.ico')); ?>" type="image/x-icon"/>
		<?php else: ?>

		<!--Favicon -->
		<link rel="icon" href="<?php echo e(asset('uploads/logo/favicons/'.$title->image4)); ?>" type="image/x-icon"/>
		<?php endif; ?>

		<?php if(getIsRtl() == 'rtl'): ?>

		<!-- Bootstrap css -->
		<link href="<?php echo e(asset('assets/plugins/bootstrap/css/bootstrap.rtl.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<?php else: ?>

		<!-- Bootstrap css -->
		<link href="<?php echo e(asset('assets/plugins/bootstrap/css/bootstrap.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<?php endif; ?>

		<!-- Style css -->
		<link href="<?php echo e(asset('assets/css/style.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/css/dark.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/css/skin-modes.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/css/updatestyles.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!-- Animate css -->
		<link href="<?php echo e(asset('assets/css/animated.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!---Icons css-->
		<link href="<?php echo e(asset('assets/css/icons.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!--INTERNAL Toastr css -->
		<link href="<?php echo e(asset('assets/plugins/toastr/toastr.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!-- Jquery js-->
		<script src="<?php echo e(asset('assets/plugins/jquery/jquery.min.js')); ?>?v=<?php echo time(); ?>"></script>

		<?php echo $__env->yieldContent('styles'); ?>

		<style>
			:root {
		--primary:<?php echo setting('theme_color') ?>;
		--secondary:<?php echo setting('theme_color_dark') ?>;
			}

		</style>

		<style>

			<?php echo customcssjs('CUSTOMCSS'); ?>

		</style>

		<?php if(setting('GOOGLEFONT_DISABLE') == 'off'): ?>

		<style>
			@import  url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

		</style>

		<?php endif; ?>

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

	<body class="<?php if(setting('DARK_MODE') == 1): ?> dark-mode <?php endif; ?> <?php echo e(getIsRtl()); ?>">
        <?php if(setting('ANNOUNCEMENT_USER') == 'non_login_users' || setting('ANNOUNCEMENT_USER') == 'all_users'): ?>
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
        <?php endif; ?>

		<div class="page login-bg1">
            <div class="page-single">
                <div class="container">
                    <div class="row justify-content-center py-4">
                        <div class="col-sm-12">
                            <div class="card authentication-card py-5 mx-auto">
                                <div class="pt-0 pb-1 text-center">

                                    <a class="header-brand ms-0" href="<?php echo e(url('/')); ?>">
                                        <?php if($title->image !== null): ?>

                                        <img src="<?php echo e(asset('uploads/logo/logo/'.$title->image)); ?>" class="header-brand-img custom-logo-dark"
                                            alt="<?php echo e($title->image); ?>">
                                        <?php else: ?>
                                        <img src="<?php echo e(asset('uploads/logo/logo/logo-white.png')); ?>" class="header-brand-img custom-logo-dark"
                                            alt="logo">
                                        <?php endif; ?>
                                        <?php if($title->image1 !== null): ?>

                                            <img src="<?php echo e(asset('uploads/logo/darklogo/'.$title->image1)); ?>" class="header-brand-img custom-logo"
                                            alt="<?php echo e($title->image1); ?>">
                                        <?php else: ?>

                                        <img src="<?php echo e(asset('uploads/logo/darklogo/logo.png')); ?>" class="header-brand-img custom-logo"
                                            alt="logo">

                                        <?php endif; ?>

                                    </a>
                                </div>

                                <?php echo $__env->yieldContent('content'); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


		<!-- Bootstrap4 js-->
		<script src="<?php echo e(asset('assets/plugins/bootstrap/popper.min.js')); ?>?v=<?php echo time(); ?>"></script>
		<script src="<?php echo e(asset('assets/plugins/bootstrap/js/bootstrap.min.js')); ?>?v=<?php echo time(); ?>"></script>

		<script>

			<?php echo customcssjs('CUSTOMJS') ?>
		</script>



		<!--INTERNAL Toastr js -->
		<script src="<?php echo e(asset('assets/plugins/toastr/toastr.min.js')); ?>?v=<?php echo time(); ?>"></script>

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
			<?php echo $__env->yieldContent('scripts'); ?>

			<?php echo $__env->yieldContent('modal'); ?>
	</body>
</html>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/layouts/custommaster.blade.php ENDPATH**/ ?>