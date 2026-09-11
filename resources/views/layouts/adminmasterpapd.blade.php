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
            @include('includes.admin.verticalmenupapd')
            <div class="app-content main-content">
                <div class="side-app">
                    @include('includes.admin.menupapd')

                    @if(setting('MAINTENANCE_MODE') == 'on')
                        <div class="alert alert-danger sprukoclosebtn mt-5 fs-15">
                            <i class="fa fa-hourglass-half fa-spin me-2 fs-15" aria-hidden="true"></i>
                            {{ lang('This application is in maintenance mode. We are performing scheduled maintenance.') }}
                        </div>
                    @endif

                    @if(setting('mail_host') == 'smtp.mailtrap.io' && Auth::user()->getRoleNames()[0] == 'superadmin')
                        <div class="alert alert-warning sprukoclosebtn mt-5 fs-15">
                            <i class="fa fa-exclamation-triangle me-2 fs-18" aria-hidden="true"></i>
                            {{ lang('It is necessary to set up your email settings first in order to send and receive emails.') }}
                            <div class="">
                                <a href="{{route('email.setting.alert')}}" class="btn btn-dark btn-sm mt-2" target="_blank"> <i class="fa fa-cogs me-2 fs-15" aria-hidden="true"></i>{{lang('Email Setup')}} </a>
                                <a href="https://youtu.be/2jwH9P9-R4E" class="btn btn-dark btn-sm mt-2" target="_blank"> <i class="fa fa-link me-2 fs-15" aria-hidden="true"></i>{{lang('Setup Reference ')}}</a>
                            </div>
                        </div>
                    @endif

                    @php
                        $cronset = \App\Models\Setting::where('key','cronjob_set')->first();
                        $cronworking = $cronset->updated_at->addMinutes(1) >= \Carbon\Carbon::now();
                    @endphp

                    @if($cronworking != true && Auth::user()->getRoleNames()[0] == 'superadmin')
                        <div class="alert alert-info sprukoclosebtn mt-5 fs-15">
                            <i class="fa fa-exclamation-triangle me-2 fs-18" aria-hidden="true"></i>
                            {{ lang('It is necessary to set up your cron job first in order for the auto functions to work.') }}
                            <div class="">
                                <a href="https://youtu.be/uqkZsQdU_TE" class="btn btn-dark btn-sm mt-2" target="_blank"> <i class="fa fa-link me-2 fs-15" aria-hidden="true"></i>{{lang('Setup Reference ')}}</a>
                            </div>
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

    <script type="text/javascript">
        // Mark as read per item
        function sendMarkRequest(id = null) {
            return $.ajax("{{ route('admin.markNotification') }}", {
                method: 'GET',
                data: { id: id }
            });
        }
    
        $(document).on('click', '.mark-as-read', function(e) {
            e.preventDefault();
            var $this = $(this);
            var id = $this.data('id');
            var request = sendMarkRequest(id);
            request.done(function() {
                $this.closest('.dropdown-item').fadeOut(300, function() {
                    $(this).remove();
                    // Update badge count
                    var badgeContainer = $('#badgecountpapd .badge');
                    var count = parseInt(badgeContainer.text()) - 1;
                    if (count > 0) {
                        badgeContainer.text(count);
                    } else {
                        badgeContainer.removeClass('badge-success badge-counter pulse-success side-badge').addClass('badge-gray').text('0');
                    }
                    // Update header count
                    var headerCount = $('#markasreadpapdcount .font-weight-semibold');
                    var newCount = parseInt(headerCount.text().match(/\d+/)) - 1;
                    if (newCount > 0) {
                        headerCount.text('Notifikasi PAPD (' + newCount + ')');
                    } else {
                        headerCount.text('Notifikasi PAPD (0)');
                        $('#markasreadpapdcount .mark-read-papd').replaceWith('<span class="mark-read-none fs-13">{{ lang("Mark all as read", "notification") }}</span>');
                    }
                    // If no items left, reload to show empty message
                    if ($('.mark-as-read').length === 0) {
                        location.reload();
                    }
                });
            });
        });
    
        // Mark all read
        $(document).on('click', '.mark-read-papd', function(e) {
            e.preventDefault();
            var $this = $(this);
            var url = $this.data('url');
    
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function() {
                    $this.text('Memproses...').css('opacity', 0.5);
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        location.reload();
                    } else {
                        toastr.error(response.message || 'Gagal menandai semua sebagai sudah dibaca.');
                    }
                },
                error: function() {
                    toastr.error('Terjadi kesalahan. Silakan coba lagi.');
                },
                complete: function() {
                    $this.text('{{ lang("Mark all as read", "notification") }}').css('opacity', 1);
                }
            });
        });

        // document.addEventListener('DOMContentLoaded', function() {
        //     document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(btn) {
        //         var target = btn.getAttribute('data-bs-target');
        //         if (target && !document.querySelector(target)) {
        //             btn.removeAttribute('data-bs-toggle');
        //             btn.removeAttribute('data-bs-target');
        //         }
        //     });
        // });
    </script>
</body>
</html>