<!--Vertical Menu GA-->
<div class="col-xl-3">
	<div class="card">
		<div class="card-body text-center item-user">
			<div class="profile-pic">
				<div class="profile-pic-img">
					<span class="bg-success dots" data-bs-toggle="tooltip" data-placement="top" title=""
						data-bs-original-title="{{lang('Online')}}"></span>
					@php
						$user = Auth::guard('customer')->user();
						$userImage = $user ? ($user->image ?? null) : null;
					@endphp
					@if($userImage)
						<img src="{{asset('uploads/profile/'.$userImage)}}" class="brround avatar-xxl"
							alt="{{$userImage}}">
					@else
						<img src="{{asset('uploads/profile/user-profile.png')}}" class="brround avatar-xxl" alt="default">
					@endif
				</div>
				<a href="#" class="text-dark">
					<h5 class="mt-3 mb-1 font-weight-semibold2">{{ $user ? ($user->firstname ?? 'User') : 'Guest' }}</h5>
				</a>
				<small class="text-muted ">{{ $user ? ($user->email ?? '') : '' }}</small>
			</div>
		</div>
		<div class="support-sidebar">
			<ul class="side-menu custom-ul">
				<li>
					<a class="side-menu__item {{ request()->routeIs('ga.index') ? 'active' : '' }}" href="{{ route('ga.index') }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z"/></svg>
						<span class="side-menu__label">{{lang('GA Dashboard', 'Menu')}}</span>
					</a>
				</li>
				<li>
					<a class="side-menu__item {{ request()->routeIs('ga.create') ? 'active' : '' }}" href="{{ route('ga.create') }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M19,13h-6v6h-2v-6H5v-2h6V5h2v6h6V13z"/></svg>
						<span class="side-menu__label">{{lang('Form GA', 'Menu')}}</span>
					</a>
				</li>
				<li>
					<a class="side-menu__item {{ request()->routeIs('user.ticket-selection') ? 'active' : '' }}" href="{{ route('user.ticket-selection') }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM4 18V6h2v12H4zm18 0H8V6h14v12z"/></svg>
						<span class="side-menu__label">{{lang('Pilih Layanan', 'Menu')}}</span>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<!-- Bussiness Hour -->
	@if(setting('businesshoursswitch') == 'on')
	<div class="card p-3 pricing-card border d-flex notify-days-toggle">
		<div class="d-md-flex d-block">
			<div  class="support-img1">
				@if(setting('supporticonimage') != null)
					<img src="{{asset('uploads/support/'. setting('supporticonimage'))}}" class="rounded-circle" alt="img" width="50" height="50">
				@else
					<img src="{{asset('assets/images/support/support.png')}}" alt="img" width="50" height="50">
				@endif
			</div>
			<div class="card-header text-justified flex-1 pt-0 ps-md-3 ps-0 pb-0 ">
				<p class="fs-18 font-weight-semibold mb-1">{{setting('businesshourstitle')}}
					@foreach(bussinesshour() as $bussiness)
						@if(now()->timezone(setting('default_timezone'))->format('D') == $bussiness->weeks)
							@if(strtotime($bussiness->starttime) <= strtotime(now()->timezone(setting('default_timezone'))->format('h:i A')) && strtotime($bussiness->endtime) >= strtotime(now()->timezone(setting('default_timezone'))->format('h:i A'))|| $bussiness->starttime == "24H")
								@if($bussiness->starttime != "24H")
									<span class="ms-3 badge bg-success text-white  mt-1 fs-12 font-weight-normal">{{lang('online')}}</span>
								@else
									<span class="ms-3 badge bg-success text-white  mt-1 fs-12 font-weight-normal">{{lang('online')}}</span>
								@endif
							@else
								@if($bussiness->starttime != "24H")
									<span class="ms-3 badge bg-danger text-white  mt-1 fs-12 font-weight-normal">{{lang('offline')}}</span>
								@else
									<span class="ms-3 badge bg-danger text-white  mt-1 fs-12 font-weight-normal">{{lang('offline')}}</span>
								@endif
							@endif
						@endif
					@endforeach
				</p>
				<p class="fs-13 mb-0 text-muted">{{setting('businesshourssubtitle')}}</p>
			</div>
			<div class="my-4 ms-auto">
				<span class="fe fe-chevron-down float-end notify-arrow"></span>
			</div>
		</div>
		<div class="card-body  pt-0 pb-0 px-4 notify-days-container">
			<ul class="custom-ul text-justify pricing-body text-muted ps-0 mb-4">
				@foreach(bussinesshour() as $bussiness)
				@if($bussiness->weeks != null)
				<li class="mb-2">
					<div class="row br-5 notify-days-cal align-items-center p-2 br-5 border text-center {{now()->timezone(setting('default_timezone'))->format('D') == $bussiness->weeks ? 'bg-success-transparent' : '' }}">
						<div class="col-xxl-3 col-xl-3 col-sm-12 ps-0">
							<span class="badge {{now()->timezone(setting('default_timezone'))->format('D') == $bussiness->weeks ? 'bg-success' : 'bg-info' }}   fs-13 font-weight-normal  w-100 ">{{lang($bussiness->weeks)}}</span>
						</div>
						<div class="col-xxl-3 col-xl-4 col-sm-12">
							@if(now()->timezone(setting('default_timezone'))->format('D') == $bussiness->weeks)
								<span class="{{$bussiness->status != 'Closed' ? 'text-success' : 'text-success' }} fs-12 ms-2">{{lang('Today')}}</span>
							@endif
						</div>
						<div class="col-xxl-6 col-xl-5 col-sm-12 px-0">
							@if($bussiness->status == "Closed")
								<span class="text-danger fs-12 ms-2">{{lang($bussiness->status)}}</span>
							@else
								<span class="ms-0 fs-13">{{$bussiness->starttime}}
								@if($bussiness->starttime !== null && $bussiness->endtime != null )
									<span class="fs-10 mx-1">- </span>
								@endif
								</span>
								@if($bussiness->starttime !== null && $bussiness->endtime )
									<span class="ms-0">{{$bussiness->endtime}}</span>
								@endif
							@endif
						</div>
					</div>
				</li>
				@endif
				@endforeach
			</ul>
		</div>
	</div>
	@endif
	<!-- End Bussiness Hour -->
	</div>
	<!--Vertical Menu GA-->
	
	<script type="text/javascript">
	'use strict';
	let notifyToggl = document.querySelector('.notify-days-toggle');
	let notifyCont = document.querySelector('.notify-days-container');
	if(notifyToggl){
		notifyToggl.addEventListener('click', ()=>{
			notifyCont.classList.toggle('show-days');
			notifyToggl.querySelector('.notify-arrow').classList.toggle('hide-container')
		})
	}
	</script>