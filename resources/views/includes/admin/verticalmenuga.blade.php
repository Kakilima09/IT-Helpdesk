<!--aside open-->
<aside class="app-sidebar">
    <div class="app-sidebar__logo">
        <a class="header-brand" href="{{ route('admin.ga.dashboard') }}">
            {{--Logo--}}
            @if ($title->image == null)

            <img src="{{asset('uploads/logo/logo/logo-white.png')}}" class="header-brand-img dark-logo" alt="logo">
            @else

            <img src="{{asset('uploads/logo/logo/'.$title->image)}}" class="header-brand-img dark-logo" alt="logo">
            @endif

            {{--Dark-Logo--}}
            @if ($title->image1 == null)

            <img src="{{asset('uploads/logo/darklogo/logo.png')}}" class="header-brand-img desktop-lgo" alt="dark-logo">
            @else

            <img src="{{asset('uploads/logo/darklogo/'.$title->image1)}}" class="header-brand-img desktop-lgo"
                alt="dark-logo">
            @endif

            {{--Mobile-Logo--}}
            @if ($title->image2 == null)

            <img src="{{asset('uploads/logo/icon/icon.png')}}" class="header-brand-img mobile-logo" alt="mobile-logo">
            @else

            <img src="{{asset('uploads/logo/icon/'.$title->image2)}}" class="header-brand-img mobile-logo"
                alt="mobile-logo">
            @endif

            {{--Mobile-Dark-Logo--}}
            @if ($title->image3 == null)

            <img src="{{asset('uploads/logo/darkicon/icon-white.png')}}" class="header-brand-img darkmobile-logo"
                alt="mobile-dark-logo">
            @else

            <img src="{{asset('uploads/logo/darkicon/'.$title->image3)}}" class="header-brand-img darkmobile-logo"
                alt="mobile-dark-logo">
            @endif

        </a>
    </div>
    <div class="app-sidebar3">
        <div class="app-sidebar__user">
            <div class="dropdown user-pro-body text-center">
                <div class="user-pic">
                    @if (Auth::user()->image == null)

                    <img src="{{asset('uploads/profile/user-profile.png')}}" class="avatar-xxl rounded-circle mb-1"
                        alt="default">
                    @else

                    <img src="{{asset('uploads/profile/'.Auth::user()->image)}}" class="avatar-xxl rounded-circle mb-1"
                        alt="{{Auth::user()->image}}">
                    @endif

                </div>
                <div class="user-info">
                    <h5 class=" mb-2">{{Auth::user()->name}}</h5>
                    @if(!empty(Auth::user()->getRoleNames()[0]))

                    <span class="text-muted app-sidebar__user-name text-sm">{{ Auth::user()->getRoleNames()[0]}}</span>
                    @endif
                    @php
                    use App\Models\Employeerating;
                      if(Auth::check() && Auth::user()->id){
                           $avgrating1 = Employeerating::where('user_id', Auth::id())->where('rating', '1')->count();
                           $avgrating2 = Employeerating::where('user_id', Auth::id())->where('rating', '2')->count();
                           $avgrating3 = Employeerating::where('user_id', Auth::id())->where('rating', '3')->count();
                           $avgrating4 = Employeerating::where('user_id', Auth::id())->where('rating', '4')->count();
                           $avgrating5 = Employeerating::where('user_id', Auth::id())->where('rating', '5')->count();

                           $avgr = ((5*$avgrating5) + (4*$avgrating4) + (3*$avgrating3) + (2*$avgrating2) + (1*$avgrating1));
                           $avggr = ($avgrating1 + $avgrating2 + $avgrating3 + $avgrating4 + $avgrating5);

                           if($avggr == 0){
                               $avggr = 1;
                               $avg1 = $avgr/$avggr;
                           }else{
                               $avg1 = $avgr/$avggr;
                           }



                       }
                   @endphp

                    <div class="allprofilerating pt-1" data-rating="{{$avg1}}"></div>
                </div>
            </div>
        </div>
        <ul class="side-menu custom-ul">

            <!-- Dashboard GA -->
            <li class="slide">
                <a class="side-menu__item {{ $current_page == 'ga-dashboard' ? 'active' : '' }}"
                href="{{ route('admin.ga.dashboard') }}">
                    <svg class="sidemenu_icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24"
                        width="24px" fill="#000000">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path
                            d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z" />
                    </svg>
                    <span class="side-menu__label">{{lang('GA Dashboard', 'Menu')}}</span>
                </a>
            </li>

            <!-- GA Requests -->
            <li class="slide">
                <a class="side-menu__item {{ $current_page == 'ga-admin' ? 'active' : '' }}"
                href="{{ route('admin.ga.index') }}">
                    <svg class="sidemenu_icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24"
                        width="24px" fill="#000000">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path
                            d="M19 5v2h-4V5h4M9 5v6H5V5h4m10 8v6h-4v-6h4M9 17v2H5v-2h4M21 3h-8v6h8V3zM11 3H3v10h8V3zm10 8h-8v10h8V11zm-10 4H3v6h8v-6z" />
                    </svg>
                    <span class="side-menu__label">{{lang('GA Requests', 'Menu')}}</span>
                </a>
            </li>

            <!-- Kategori Barang -->
            <li class="slide">
                <a class="side-menu__item {{ $current_page == 'ga-categories' ? 'active' : '' }}"
                href="{{ route('admin.ga.categories.index') }}">
                    <svg class="sidemenu_icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24"
                        width="24px" fill="#000000">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path
                            d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM4 6h4v12H4V6zm16 12h-4V6h4v12z" />
                    </svg>
                    <span class="side-menu__label">{{lang('Kategori Barang', 'Menu')}}</span>
                </a>
            </li>

            <!-- Profile -->
            <li class="slide">
                <a class="side-menu__item {{ $current_page == 'ga-admin-profile' ? 'active' : '' }}"
                href="{{ url('/admin/profile') }}">
                    <svg class="sidemenu_icon" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24"
                        width="24px" fill="#000000">
                        <path d="M0 0h24v24H0V0z" fill="none" />
                        <path
                            d="M12 6c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2m0 9c2.7 0 5.8 1.29 6 2v1H6v-.99c.2-.72 3.3-2.01 6-2.01m0-11C9.79 4 8 5.79 8 8s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0 9c-2.67 0-8 1.34-8 4v3h16v-3c0-2.66-5.33-4-8-4z" />
                    </svg>
                    <span class="side-menu__label">{{lang('Profile', 'Menu')}}</span>
                </a>
            </li>

        </ul>
    </div>
</aside>