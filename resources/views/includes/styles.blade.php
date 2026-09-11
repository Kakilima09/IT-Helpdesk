<!-- Meta data -->
<meta charset="UTF-8">
<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
<meta content="{{$seopage->description ? $seopage->description :''}}" name="description">
<meta content="{{$seopage->author ? $seopage->author :''}}" name="author">
<meta name="keywords" content="{{$seopage->keywords ? $seopage->keywords :''}}" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Title -->
<title>{{$title->title}}</title>

@if ($title->image4 == null)

<!--Favicon -->
<link rel="icon" href="{{asset('uploads/logo/favicons/favicon.ico')}}" type="image/x-icon"/>
@else

<!--Favicon -->
<link rel="icon" href="{{asset('uploads/logo/favicons/'.$title->image4)}}" type="image/x-icon"/>
@endif


@if(str_replace('_', '-', app()->getLocale()) == 'عربى')

<!-- Bootstrap css -->
<link href="{{asset('assets/plugins/bootstrap/css/bootstrap.rtl.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />
@else

<!-- Bootstrap css -->
<link href="{{asset('assets/plugins/bootstrap/css/bootstrap.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />
@endif

<!-- Style css -->
<link href="{{asset('assets/css/style.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />
<link href="{{asset('assets/css/updatestyles.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />
<link href="{{asset('assets/css/dark.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />

<!-- Animate css -->
<link href="{{asset('assets/css/animated.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />

<!-- P-scroll bar css-->
<link href="{{asset('assets/plugins/p-scrollbar/p-scrollbar.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />

<!---Icons css-->
<link href="{{asset('assets/css/icons.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />

<!-- Select2 css -->
<link href="{{asset('assets/plugins/select2/select2.min.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />

<!--INTERNAL Toastr css -->
<link href="{{asset('assets/plugins/toastr/toastr.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />

<!-- INTERNAL Sweet-Alert css -->
<link href="{{asset('assets/plugins/sweet-alert/sweetalert.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />

@yield('styles')

<!-- Color Setting -->
<style>
	:root {
		--primary: @php echo setting('theme_color') @endphp;
		--secondary: @php echo setting('theme_color_dark') @endphp;
	}
</style>

<style>
	/* Mobile Header */
	@media (max-width: 991.98px) {
		.support-mobile-header {
			display: block !important;
			padding: 8px 0;
			background: #fff;
			border-bottom: 1px solid #e9ecef;
		}
		.support-mobile-header .smallogo img {
			max-height: 35px;
			width: auto;
		}
		.support-mobile-header .avatar {
			width: 30px !important;
			height: 30px !important;
		}
		.support-mobile-header .dropdown-menu {
			min-width: 200px !important;
		}
	}
	
	@media (min-width: 992px) {
		.support-mobile-header {
			display: none !important;
		}
	}
	
	/* Fix untuk horizontal menu di mobile */
	.horizontalMenu-list {
		list-style: none;
		margin: 0;
		padding: 0;
	}
	
	@media (max-width: 991.98px) {
		.horizontalMenu > .horizontalMenu-list {
			display: none;
			position: absolute;
			top: 60px;
			left: 0;
			right: 0;
			background: #fff;
			padding: 15px 20px;
			box-shadow: 0 5px 15px rgba(0,0,0,0.1);
			z-index: 999;
			max-height: 80vh;
			overflow-y: auto;
		}
		.horizontalMenu > .horizontalMenu-list.open {
			display: block !important;
		}
		.horizontalMenu-list li {
			display: block !important;
			width: 100%;
			padding: 5px 0;
		}
		.horizontalMenu-list li a {
			display: block !important;
			padding: 8px 12px !important;
		}
	}
	</style>

<!-- Custom css -->
<style>

	<?php echo customcssjs('CUSTOMCSS'); ?>

</style>

@if(setting('GOOGLEFONT_DISABLE') == 'off')

<!-- Google Fonts -->
<style>

	@import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');

</style>

@endif

<!-- Jquery js-->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}?v=<?php echo time(); ?>"></script>
