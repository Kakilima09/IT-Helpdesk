<!-- Header-->
<div class="landingmain-header header">
	<div class="horizontal-main landing-header clearfix sticky">
		<div class="horizontal-mainwrapper container clearfix">
			<div class="d-flex">
				<a href="javascript:void(0)" class="animated-arrow horizontal-navtoggle"><span></span></a>
				<div class="headerlanding-logo">
					<a class="header-brand" href="{{ route('papd.index') }}">
						@php
							$logo = $title->image ?? 'logo-white.png';
							$darkLogo = $title->image1 ?? 'logo.png';
						@endphp
						<img src="{{ asset('uploads/logo/logo/'.$logo) }}" class="header-brand-img desktop-lgo" alt="{{ $logo }}">
						<img src="{{ asset('uploads/logo/darklogo/'.$darkLogo) }}" class="header-brand-img light-logo" alt="{{ $darkLogo }}">
					</a>
				</div>

				@if(Auth::guard('customer')->check())
				<div class="d-flex order-lg-2 my-auto ms-auto d-lg-none d-block">
					<button class="navbar-toggler nav-link icon navresponsive-toggler vertical-icon ms-auto collapsed"
						type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4"
						aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
						<i class="fe fe-more-vertical header-icons navbar-toggler-icon"></i>
					</button>
					<div class="mb-0 navbar navbar-expand-lg navbar-nav-right responsive-navbar navbar-dark p-0">
						<div class="navbar-collapse collapse" id="navbarSupportedContent-4">
							<ul class="d-flex ms-auto landing-header-right p-0 mb-0">

								@include('includes.user.papd.allnotify')

								<li class="dropdown header-flags">
									<a href="#" class="text-capitalize dropdown-toggle nav-link icon fs-16 p-0 bg-transparent text-dark mx-5"
										data-bs-toggle="dropdown" aria-expanded="false">
										<span class="">{{ getLangName() }}</span>
									</a>
									<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-capitalize">
										@foreach(getLanguageslist() as $lang)
										<a href="{{ langURL($lang->languagecode) }}" class="dropdown-item d-flex fs-13">
											<span class="">{{ $lang->languagename }}</span>
										</a>
										@endforeach
									</div>
								</li>

								<li class="dropdown profile-dropdown">
									<a href="#" class="nav-link p-0 mt-1 leading-none user-img" data-bs-toggle="dropdown">
										<span>
											@php
												$avatar = Auth::guard('customer')->user()->image ?? 'user-profile.png';
											@endphp
											<img src="{{ asset('uploads/profile/'.$avatar) }}" class="avatar avatar-md bradius rounded-circle" alt="avatar">
										</span>
									</a>
									<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
										<div class="p-3 text-center border-bottom">
											<a href="#" class="text-center user pb-0 font-weight-bold">{{ Auth::guard('customer')->user()->username }}</a>
											<p class="text-center user-semi-title">{{ Auth::guard('customer')->user()->email }}</p>
										</div>
										<a class="dropdown-item d-flex" href="{{ route('papd.index') }}">
											<i class="feather feather-grid me-3 fs-16 my-auto"></i>
											<div class="mt-1">{{ lang('PAPD Dashboard', 'Menu') }}</div>
										</a>
										<a class="dropdown-item d-flex" href="{{ route('client.profile') }}">
											<i class="feather feather-user me-3 fs-16 my-auto"></i>
											<div class="mt-1">{{ lang('Profile', 'Menu') }}</div>
										</a>
										<a class="dropdown-item d-flex" href="{{ route('papd.create') }}">
											<i class="ri-ticket-2-line me-3 fs-16 my-auto"></i>
											<div class="mt-1">{{ lang('Buat PAPD', 'Menu') }}</div>
										</a>
										<form id="logout-form" action="{{ route('client.logout') }}" method="POST">
											@csrf
											<button type="submit" class="dropdown-item d-flex">
												<i class="feather feather-power me-3 fs-16 my-auto"></i>
												<div class="mt-1">{{ lang('Logout', 'Menu') }}</div>
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
						@if(setting('defaultlogin_on') == 'off')
						<li>
							<a href="{{ route('papd.index') }}">{{ lang('Home', 'Menu') }}</a>
						</li>
						@endif
						@if (setting('KNOWLEDGE_ENABLE') == 'yes')
						<li>
							<a href="{{ url('/knowledge') }}" class="sub-icon">{{ lang('Knowledge', 'Menu') }}</a>
						</li>
						@endif
						@if (setting('FAQ_ENABLE') == 'yes')
						<li>
							<a href="{{ url('/faq') }}" class="sub-icon">{{ lang('FAQ’s', 'Menu') }}</a>
						</li>
						@endif
						@if (setting('CONTACT_ENABLE') == 'yes')
						<li>
							<a href="{{ url('/contact-us') }}">{{ lang('Contact Us', 'Menu') }}</a>
						</li>
						@endif
						@foreach ($page as $pages)
							@if($pages->status == '1' && ($pages->viewonpages == 'both' || $pages->viewonpages == 'header'))
							<li>
								<a href="{{ url('page/' . $pages->pageslug) }}">{{ $pages->pagename }}</a>
							</li>
							@endif
						@endforeach

						@if (Auth::guard('customer')->check())

							@include('includes.user.papd.allnotify')

							<li class="dropdown header-flags text-capitalize">
								<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
									<span class="">{{ getLangName() }}</span>
								</a>
								<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-capitalize">
									@foreach(getLanguageslist() as $lang)
									<a href="{{ langURL($lang->languagecode) }}" class="dropdown-item d-flex fs-13">
										<span class="">{{ $lang->languagename }}</span>
									</a>
									@endforeach
								</div>
							</li>

							<li class="dropdown profile-dropdown">
								<a href="#" class="nav-link pe-1 ps-0 py-0 mt-1 leading-none" data-bs-toggle="dropdown">
									<span>
										@php
											$avatar = Auth::guard('customer')->user()->image ?? 'user-profile.png';
										@endphp
										<img src="{{ asset('uploads/profile/'.$avatar) }}" class="avatar avatar-md bradius" alt="avatar">
									</span>
								</a>
								<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
									<div class="p-3 text-center border-bottom">
										<a href="#" class="text-center user pb-0 font-weight-bold">{{ Auth::guard('customer')->user()->username }}</a>
										<p class="text-center user-semi-title">{{ Auth::guard('customer')->user()->email }}</p>
									</div>
									<a class="dropdown-item d-flex" href="{{ route('papd.index') }}">
										<i class="feather feather-grid me-3 fs-16 my-auto"></i>
										<div class="mt-1">{{ lang('PAPD Dashboard', 'Menu') }}</div>
									</a>
									<a class="dropdown-item d-flex" href="{{ route('client.profile') }}">
										<i class="feather feather-user me-3 fs-16 my-auto"></i>
										<div class="mt-1">{{ lang('Profile', 'Menu') }}</div>
									</a>
									<a class="dropdown-item d-flex" href="{{ route('papd.create') }}">
										<i class="ri-ticket-2-line me-3 fs-16 my-auto"></i>
										<div class="mt-1">{{ lang('Buat PAPD', 'Menu') }}</div>
									</a>
									<form id="logout-form" action="{{ route('client.logout') }}" method="POST">
										@csrf
										<button type="submit" class="dropdown-item d-flex">
											<i class="feather feather-power me-3 fs-16 my-auto"></i>
											<div class="mt-1">{{ lang('Logout', 'Menu') }}</div>
										</button>
									</form>
								</div>
							</li>

						@else

							<!-- Guest -->
							@if (setting('REGISTER_POPUP') == 'yes')
							<li><a href="#" data-bs-toggle="modal" data-bs-target="#loginmodal">{{ lang('Login', 'Menu') }}</a></li>
							@if(setting('REGISTER_DISABLE') == 'on')
							<li><a href="#" data-bs-toggle="modal" data-bs-target="#registermodal">{{ lang('Register', 'Menu') }}</a></li>
							@endif
							@else
							<li><a href="{{ url('customer/login') }}">{{ lang('Login', 'Menu') }}</a></li>
							@if(setting('REGISTER_DISABLE') == 'on')
							<li><a href="{{ url('customer/register') }}">{{ lang('Register', 'Menu') }}</a></li>
							@endif
							@endif

							<li class="dropdown header-flags text-capitalize">
								<a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
									<span class="">{{ getLangName() }}</span>
								</a>
								<div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow animated text-capitalize">
									@foreach(getLanguageslist() as $lang)
									<a href="{{ langURL($lang->languagecode) }}" class="dropdown-item d-flex fs-13">
										<span class="">{{ $lang->languagename }}</span>
									</a>
									@endforeach
								</div>
							</li>

							@if(setting('GUEST_TICKET') == 'yes')
							<li>
								<span class="menu-btn">
									<a class="btn btn-secondary m-0" href="{{ route('papd.create') }}">
										<i class="fa fa-paper-plane-o me-1"></i>
										{{ lang('Buat PAPD', 'Menu') }}
									</a>
								</span>
							</li>
							@endif

						@endif
					</ul>
				</nav>
			</div>
		</div>
	</div>
</div>
<!--Header-->