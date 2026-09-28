<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" >
	<head>
    	@include('includes.admin.styles')

		@if(setting('GOOGLE_ANALYTICS_ENABLE') == 'yes')
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id={{setting('GOOGLE_ANALYTICS')}}"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){dataLayer.push(arguments);}
			gtag('js', new Date());

			gtag('config', '{{setting('GOOGLE_ANALYTICS')}}');
		</script>
		@endif

	</head>

	<body class="app sidebar-mini
	{{getIsRtl()}}
	@if(setting('SPRUKOADMIN_P') == 'off')
		@if(setting('DARK_MODE') == 1) dark-mode @endif
	@else
	@if(Auth::check() && Auth::user()->darkmode == 1) dark-mode @endif

	@endif
	@if(setting('sidemenu_icon_style') == 'on')
	icon-overlay sidenav-toggled
	@endif
	">

    <div class="page">
        <div class="page-main">
            @include('includes.admin.verticalmenuga')
            <div class="app-content main-content">
                <div class="side-app">
                    @include('includes.admin.menuga')

                    @if(setting('MAINTENANCE_MODE') == 'on')

                        <div class="alert alert-danger sprukoclosebtn mt-5 fs-15">
                            <i class="fa fa-hourglass-half fa-spin me-2 fs-15" aria-hidden="true"></i>
                            {{ lang('This application is in maintenance mode. We are performing scheduled maintenance.') }}
                        </div>

                    @endif

                    @yield('content')

                </div>
            </div><!-- end app-content-->
        </div>
        @include('includes.admin.footer')

    </div>

    @include('includes.admin.scripts')

	@if (Session::has('error'))
		<script>
			toastr.error("{!! Session::get('error') !!}");
		</script>
	@elseif(Session::has('success'))
		<script>
			toastr.success("{!! Session::get('success') !!}");
		</script>
	@elseif(Session::has('info'))
		<script>
			toastr.info("{!! Session::get('info') !!}");
		</script>
	@elseif(Session::has('warning'))
		<script>
			toastr.warning("{!! Session::get('warning') !!}");
		</script>
	@endif

		@yield('modal')

	</body>
</html>