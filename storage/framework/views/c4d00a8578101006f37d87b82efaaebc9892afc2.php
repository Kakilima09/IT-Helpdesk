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


		<?php if(str_replace('_', '-', app()->getLocale()) == 'عربى'): ?>

		<!-- Bootstrap css -->
		<link href="<?php echo e(asset('assets/plugins/bootstrap/css/bootstrap.rtl.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<?php else: ?>

		<!-- Bootstrap css -->
		<link href="<?php echo e(asset('assets/plugins/bootstrap/css/bootstrap.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<?php endif; ?>

		<!-- Style css -->
		<link href="<?php echo e(asset('assets/css/style.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/css/updatestyles.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/css/dark.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
		<link href="<?php echo e(asset('assets/css/skin-modes.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!-- Animate css -->
		<link href="<?php echo e(asset('assets/css/animated.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!--Sidemenu css -->
		<link href="<?php echo e(asset('assets/css/sidemenu.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!-- P-scroll bar css-->
		<link href="<?php echo e(asset('assets/plugins/p-scrollbar/p-scrollbar.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!---Icons css-->
		<link href="<?php echo e(asset('assets/css/icons.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!-- Select2 css -->
		<link href="<?php echo e(asset('assets/plugins/select2/select2.min.css')); ?>" rel="stylesheet" />

		<!--INTERNAL Toastr css -->
		<link href="<?php echo e(asset('assets/plugins/toastr/toastr.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<!--INTERNAL Ratings css -->
		<link href="<?php echo e(asset('assets/plugins/ratings/jquerystarrating.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />

		<?php echo $__env->yieldContent('styles'); ?>

		<!-- Color Changes -->
		<style>
			:root {
		--primary:<?php echo setting('theme_color') ?>;
		--secondary:<?php echo setting('theme_color_dark') ?>;
			}

		</style>
		<?php if(setting('GOOGLEFONT_DISABLE') == 'off'): ?>

		<!-- Google Fonts -->
		<style>
			@import  url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
		</style>
		<?php endif; ?>

		<!-- Custom Css -->
		<style>

			<?php echo customcssjs('CUSTOMCSS'); ?>

		</style>

		<!-- Jquery js-->
		<script src="<?php echo e(asset('assets/plugins/jquery/jquery.min.js')); ?>?v=<?php echo time(); ?>"></script>
<?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/admin/styles.blade.php ENDPATH**/ ?>