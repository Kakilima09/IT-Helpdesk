<!-- Header GA-->
<div class="landingmain-header header">
	<div class="horizontal-main landing-header clearfix sticky">
		<div class="horizontal-mainwrapper container clearfix">
			<div class="d-flex">
				<a href="javascript:void(0)" class="animated-arrow horizontal-navtoggle"><span></span></a>
				<div class="headerlanding-logo">
					<a class="header-brand" href="{{ route('ga.index') }}">
						@if ($title->image !== null)

						<img src="{{asset('uploads/logo/logo/'.$title->image)}}" class="header-brand-img desktop-lgo"
							alt="{{$title->image}}">
						@else
						<img src="{{asset('uploads/logo/logo/logo-white.png')}}" class="header-brand-img desktop-lgo"
							alt="logo">
						@endif
						@if ($title->image1 !== null)

						<img src="{{asset('uploads/logo/darklogo/'.$title->image1)}}"
							class="header-brand-img light-logo" alt="{{$title->image1}}">
						@else

						<img src="{{asset('uploads/logo/darklogo/logo.png')}}" class="header-brand-img light-logo"
							alt="logo">

						@endif
					</a>
				</div>

				@if(Auth::guard('customer')->check())
				<div class="d-flex order-lg-2 my-auto ms-auto d-lg-none d-block">
					<button class="navbar-toggler nav-link icon navresponsive-toggler vertical-icon ms-auto collapsed"
						type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-ga"
						aria-controls="navbarSupportedContent-ga" aria-expanded="false" aria-label="Toggle navigation">
						<i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i> </button>
					<div class="mb-0 navbar navbar-expand-lg navbar-nav-right responsive-navbar navbar-dark p-0">
						<div class="navbar-collapse collapse" id="navbarSupportedContent-ga">
							<ul class="d-flex ms-auto landing-header-right p-0 mb-0">
								<li class="dropdown profile-dropdown">
									<a href="#" class="nav-link p-0 mt-1 leading-none user-img"
										data-bs-toggle="dropdown">
										<span>

											@if (Auth::guard('customer')->user()->image == null)

											<img src="{{asset('uploads/profile/user-profile.png')}}"
												class="avatar avatar-md bradius rounded-circle" alt="default">

											@else

											<img src="{{asset('uploads/profile/'.Auth::guard('customer')->user()->image)}}"
												alt="{{Auth::guard('customer')->user()->image}}"
												class="avatar avatar-md bradius rounded-circle">

											@endif

										</span>
									</a>
									<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
										<div class="p-3 text-center border-bottom">
											<a href="#"
												class="text-center user pb-0 font-weight-bold">{{Auth::guard('customer')->user()->username}}</a>
											<p class="text-center user-semi-title">
												{{Auth::guard('customer')->user()->email}}
											</p>
										</div>
										<a class="dropdown-item d-flex" href="{{ route('ga.index') }}">
											<i class="feather feather-grid me-3 fs-16 my-auto"></i>
											<div class="mt-1">{{lang('GA Dashboard', 'Menu')}}</div>
										</a>
										<a class="dropdown-item d-flex" href="{{ route('ga.create') }}">
											<i class="ri-file-list-3-line me-3 fs-16 my-auto"></i>
											<div class="mt-1">{{lang('Form GA', 'Menu')}}</div>
										</a>
										<a class="dropdown-item d-flex" href="{{ route('user.ticket-selection') }}">
											<i class="feather feather-layout me-3 fs-16 my-auto"></i>
											<div class="mt-1">{{lang('IT Helpdesk', 'Menu')}}</div>
										</a>
										<form id="logout-form" action="{{route('client.logout')}}" method="POST">

											@csrf

											<button type="submit" class="dropdown-item d-flex">
												<i class="feather feather-power me-3 fs-16 my-auto"></i>
												<div class="mt-1">{{lang('Logout', 'Menu')}}</div>
											</button>
										</form>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
				@endif

				<nav class="horizontalMenu clearfix order-lg-2 my-auto ms-auto">
					<ul class="horizontalMenu-list custom-ul">
						<li>
							<a href="{{ route('ga.index') }}" class="{{ request()->routeIs('ga.index') ? 'active' : '' }}">{{lang('GA Dashboard', 'Menu')}}</a>
						</li>
						<li>
							<a href="{{ route('ga.create') }}" class="{{ request()->routeIs('ga.create') ? 'active' : '' }}">{{lang('Form GA', 'Menu')}}</a>
						</li>
						<li>
							<a href="{{ route('user.ticket-selection') }}">{{lang('IT Helpdesk', 'Menu')}}</a>
						</li>
						@if (Auth::guard('customer')->check())

						<li class="dropdown profile-dropdown">
							<a href="#" class="nav-link pe-1 ps-0 py-0 mt-1 leading-none" data-bs-toggle="dropdown">
								<span>
									@if (Auth::guard('customer')->user()->image == null)

									<img src="{{asset('uploads/profile/user-profile.png')}}"
										class="avatar avatar-md bradius rounded-circle" alt="default">
									@else

									<img src="{{asset('uploads/profile/'.Auth::guard('customer')->user()->image)}}"
										alt="{{Auth::guard('customer')->user()->image}}"
										class="avatar avatar-md bradius">
									@endif

								</span>
							</a>
							<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
								<div class="p-3 text-center border-bottom">
									<a href="#"
										class="text-center user pb-0 font-weight-bold">{{Auth::guard('customer')->user()->username}}</a>
									<p class="text-center user-semi-title">{{Auth::guard('customer')->user()->email}}
									</p>
								</div>

								<a class="dropdown-item d-flex" href="{{ route('ga.index') }}">
									<i class="feather feather-grid me-3 fs-16 my-auto"></i>
									<div class="mt-1">{{lang('GA Dashboard', 'Menu')}}</div>
								</a>
								<a class="dropdown-item d-flex" href="{{ route('ga.create') }}">
									<i class="ri-file-list-3-line me-3 fs-16 my-auto"></i>
									<div class="mt-1">{{lang('Form GA', 'Menu')}}</div>
								</a>
								<a class="dropdown-item d-flex" href="{{ route('user.ticket-selection') }}">
									<i class="feather feather-layout me-3 fs-16 my-auto"></i>
									<div class="mt-1">{{lang('IT Helpdesk', 'Menu')}}</div>
								</a>
								<form id="logout-form" action="{{route('client.logout')}}" method="POST">
									@csrf

									<button type="submit" class="dropdown-item d-flex">
										<i class="feather feather-power me-3 fs-16 my-auto"></i>
										<div class="mt-1">{{lang('Logout', 'Menu')}}</div>
									</button>
								</form>

							</div>
						</li>
						@endif
					</ul>
				</nav>
			</div>
		</div>
	</div>
</div>
<!--Header GA-->