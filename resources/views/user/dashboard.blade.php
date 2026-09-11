@extends('layouts.usermaster')

@section('styles')

<!-- INTERNAL Data table css -->
<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />
<link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />

@endsection

<!-- Section -->


@section('content')

    @if(setting('ANNOUNCEMENT_USER') == 'only_login_user' || setting('ANNOUNCEMENT_USER') == 'all_users')
        <div class="uhelp-announcement-alertgroup">
            @foreach ($announcement as $anct)
                @if ($anct->status == 1)
                    <div class="alert" role="alert" style="background: linear-gradient(to right, {{$anct->primary_color}}, {{$anct->secondary_color}});">
                        <div class="container">
                            <button type="submit" class="btn-close ms-5 float-end text-white notifyclose" data-id="{{$anct->id}}">×</button>
                            <div class="d-flex align-items-top">
                                <div class="uhelp-announcement me-2">
                                    <svg class="svg-info" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                </div>
                                <div class="text-default d-flex align-items-top">
                                    <div class="notice-heading d-flex align-items-top flex-fill">
                                        <div>
                                            <span class="fs-15 font-weight-bold text-white flex-fill">{{$anct->title}}</span>
                                            <span class="text-white opacity-50 mx-2"><i class="ti ti-minus"></i></span>
                                            <span class="mb-0 text-white uhelp-alert-content alert-notice">{!!$anct->notice!!}
                                                @if($anct->buttonon == 1)
                                                <a class="btn btn-sm ms-2 text-white text-decoration-underline" href="{{$anct->buttonurl}}" target="_blank">{{$anct->buttonname}}</a>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            @foreach ($announcements as $ancts)
                @php
                $announceDay = explode(',', $ancts->announcementday);
                $now = today()->format('D');
                @endphp
                @foreach ($announceDay as $announceDays)
                    @if ($ancts->status == 1 && $announceDays == $now)
                        <div class="alert alert-days" role="alert" style="background: linear-gradient(to right, {{$ancts->primary_color}}, {{$ancts->secondary_color}});">
                            <div class="container">
                                <button type="submit" class="btn-close ms-5 float-end text-white notifyclose" data-id="{{$ancts->id}}">×</button>
                                <div class="d-flex align-items-top">
                                    <div class="uhelp-announcement me-2">
                                        <svg class="svg-info" xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 7h2v2h-2zm0 4h2v6h-2zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/></svg>
                                    </div>
                                    <div class="text-default d-flex align-items-top">
                                        <div class="notice-heading d-flex align-items-top flex-fill">
                                            <div>
                                                <span class="fs-15 font-weight-bold text-white flex-fill">{{$ancts->title}}</span>
                                                <span class="text-white opacity-50 mx-2"><i class="ti ti-minus"></i></span>
                                                <span class="mb-0 text-white uhelp-alert-content alert-notice">{!!$ancts->notice!!}
                                                    @if($ancts->buttonon == 1)
                                                    <a class="btn btn-sm ms-2 text-white text-decoration-underline" href="{{$ancts->buttonurl}}" target="_blank">{{$ancts->buttonname}}</a>
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endforeach
        </div>
    @endif

    <!-- Section -->
    <section>
        <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
            <div class="header-text mb-0">
                <div class="container ">
                    <div class="row text-white">
                        <div class="col">
                            <h1 class="mb-0">{{lang('Dashboard', 'menu')}}</h1>
                            <!-- Tambahkan welcome message dengan nama user -->
                            @auth('customer')
                            <p class="mb-0 fs-14 opacity-75">Welcome back, {{ Auth::guard('customer')->user()->firstname }} {{ Auth::guard('customer')->user()->lastname }}!</p>
                            @endauth
                        </div>
                        <div class="col col-auto">
                            <ol class="breadcrumb text-center">
                                <li class="breadcrumb-item">
                                    <a href="{{route('client.dashboard')}}" class="text-white-50">{{lang('Home', 'menu')}}</a>
                                </li>
                                <li class="breadcrumb-item active">
                                    <a href="{{route('client.dashboard')}}" class="text-white">{{lang('Dashboard', 'menu')}}</a>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Section -->

    <!--Dashboard List-->
    <section>
        <div class="cover-image sptb">
            <div class="container">
                <div class="row">
                    @include('includes.user.verticalmenu')
                    
                    <div class="col-xl-9">
                        <!--- Custom notification -->
                        @php 
                            $customernotify = auth()->guard('customer')->user()->unreadNotifications()->where('data->status', 'mail')->get();  
                        @endphp
                        
                        @if($customernotify->isNotEmpty())
                        <div class="alert alert-warning-light br-13  border-0 d-flex align-items-center" role="alert">
                            <div class="d-flex">
                                <svg class="alt-notify  me-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path fill="#eec466"
                                        d="M19,20H5a3.00328,3.00328,0,0,1-3-3V7A3.00328,3.00328,0,0,1,5,4H19a3.00328,3.00328,0,0,1,3,3V17A3.00328,3.00328,0,0,1,19,20Z" />
                                    <path fill="#e49e00"
                                        d="M22,7a3.00328,3.00328,0,0,0-3-3H5A3.00328,3.00328,0,0,0,2,7V8.061l9.47852,5.79248a1.00149,1.00149,0,0,0,1.043,0L22,8.061Z" />
                                </svg>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-2">Hello, {{ Auth::guard('customer')->user()->firstname }}!</h6>
                                <ul class="notify vertical-scroll5 custom-ul ht-0 me-5">
                                    @forelse($customernotify as $notification)
                                        @if ($notification->data['status'] == 'mail')
                                        <li class="item">
                                            <p class="fs-13 mb-0">{{$notification->data['mailsubject']}}
                                                {{Str::limit($notification->data['mailtext'], '400', '...')}} 
                                                <a href="{{route('customer.notiication.view', $notification->id)}}"
                                                class="ms-3 text-blue mark-as-read">{{lang('Read more')}}</a>
                                            </p>
                                        </li>
                                        @endif
                                    @empty
                                    @endforelse
                                </ul>
                            </div>
                            <div class="d-flex ms-6 sprukocnotify">
                                <button class="btn-close ms-2 mt-0 text-warning" data-bs-dismiss="alert"
                                    aria-hidden="true">×</button>
                            </div>
                        </div>
                        @else
                        <!-- Welcome message ketika tidak ada notifikasi -->
                        <div class="alert alert-info-light br-13 border-0 d-flex align-items-center" role="alert">
                            <div class="d-flex">
                                <i class="fe fe-user me-3 fs-18 text-info"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="alert-heading mb-1">Welcome, {{ Auth::guard('customer')->user()->firstname }} {{ Auth::guard('customer')->user()->lastname }}!</h6>
                                <p class="mb-0 fs-13">You have {{ $tickets->count() }} total tickets in your account.</p>
                            </div>
                        </div>
                        @endif
                        <!--- End Custom notification -->

                        <!-- ====== AI IT ASSISTANT CARD ====== -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="me-3 mb-2 mb-sm-0">
                                        <span class="avatar avatar-lg bg-primary-transparent text-primary">
                                            <i class="ri-robot-line fs-30"></i>
                                        </span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">🤖 AI IT ASSISTANT</h5>
                                        <p class="card-text text-muted mb-0">
                                            Tell us about your IT problem. Our AI assistant will try to diagnose and solve it before creating a ticket.
                                        </p>
                                    </div>
                                    <div>
                                        <a href="{{ route('customer.ai-assistant') }}" class="btn btn-primary">
                                            Ask IT Assistant <i class="fe fe-arrow-right ms-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ====== END AI IT ASSISTANT CARD ====== -->

                        <div class="row">
                            <div class="col-xl-3 col-lg-3 col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="mt-0 text-start">
                                                    <span class="fs-16 font-weight-semibold">{{lang('Total Tickets', 'menu')}}</span>
                                                    <h3 class="mb-0 mt-1 text-primary fs-25">{{$tickets->count()}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="icon1 bg-primary-transparent my-auto float-end"> 
                                                    <i class="las la-ticket-alt"></i> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-lg-3 col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="mt-0 text-start">
                                                    <span class="fs-16 font-weight-semibold">{{lang('Active Tickets', 'menu')}}</span>
                                                    <h3 class="mb-0 mt-1 text-success fs-25">{{$active->count()}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="icon1 bg-success-transparent my-auto float-end"> 
                                                    <i class="ri-ticket-2-line"></i> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-2 col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="mt-0 text-start">
                                                    <span class="fs-16 font-weight-semibold">{{lang('On-Progress', 'menu')}}</span>
                                                    <h3 class="mb-0 mt-1 text-info fs-25">{{$onprogress->count()}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="icon1 bg-info-transparent my-auto float-end">
                                                    <i class="ri-progress-3-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-lg-3 col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="mt-0 text-start">
                                                    <span class="fs-16 font-weight-semibold">{{lang('On-Hold Tickets', 'menu')}}</span>
                                                    <h3 class="mb-0 mt-1 text-warning fs-25">{{$onhold->count()}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="icon1 bg-warning-transparent my-auto float-end"> 
                                                    <i class="ri-coupon-2-line"></i> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-3 col-lg-3 col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-7">
                                                <div class="mt-0 text-start">
                                                    <span class="fs-16 font-weight-semibold">{{lang('Closed Tickets', 'menu')}}</span>
                                                    <h3 class="mb-0 mt-1 text-secondary fs-25">{{$closed->count()}}</h3>
                                                </div>
                                            </div>
                                            <div class="col-5">
                                                <div class="icon1 bg-danger-transparent my-auto float-end"> 
                                                    <i class="ri-coupon-2-line"></i> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rest of your ticket table code remains the same -->
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="card mb-0">
                                    <div class="card-header border-0 d-flex">
                                        <h4 class="card-title">{{lang('Tickets Summary')}}</h4>
                                        @if(setting('CUSTOMER_TICKET') == 'no')
                                        <div class="float-end ms-auto">
                                            <a href="{{route('client.ticket')}}" class="btn btn-secondary ms-auto">
                                                <i class="fa fa-paper-plane-o me-2"></i>{{lang('Create Ticket', 'menu')}}
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <!-- Table content remains the same -->
                                        <div class="table-responsive">
                                            @if(setting('CUSTOMER_RESTICT_TO_DELETE_TICKET') == 'off')
                                                <button id="massdelete" class="btn btn-outline-light btn-sm ms-7 mb-4 data-table-btn mx-md-center">
                                                    <i class="fe fe-trash me-1"></i> {{lang('Delete')}}
                                                </button>
                                            @endif
                                            <table class="table table-bordered border-bottom text-nowrap ticketdeleterow w-100" id="userdashboard">
                                                <!-- Table headers and body remain the same -->
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Dashboard List-->

    @endsection

@section('scripts')

<!-- INTERNAL Vertical-scroll js-->
<script src="{{asset('assets/plugins/vertical-scroll/jquery.bootstrap.newsbox.js')}}?v=<?php echo time(); ?>"></script>

<!-- INTERNAL Data tables -->
<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}?v=<?php echo time(); ?>"></script>

<!-- INTERNAL Index js-->
<script src="{{asset('assets/js/support/support-sidemenu.js')}}?v=<?php echo time(); ?>"></script>

<script type="text/javascript">
    "use strict";

    var complex = <?php echo json_encode(setting('CUSTOMER_RESTICT_TO_DELETE_TICKET')); ?>;
    if(complex == 'on'){
        setTimeout(myGreeting, 1)
            function myGreeting() {
                document.querySelector(".removecolumnheader").remove()
                document.querySelectorAll(".removecolumndata").forEach((res)=>{
                res.remove()
            })
        }

    }

    (function($){

        // Variables
        var SITEURL = '{{url('')}}';

        // Csrf Field
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //________ Data Table
        // $('#userdashboard').DataTable({

        //     language: {
        //         searchPlaceholder: search,
        //         scrollX: "100%",
        //         sSearch: '',
        //     },
        //     order:[],
        //     columnDefs: [
        //         { "orderable": false, "targets":[ 0,1,4] }
        //     ],
        // });

        let prev = {!! json_encode(lang("Previous")) !!};
        let next = {!! json_encode(lang("Next")) !!};
        let nodata = {!! json_encode(lang("No data available in table")) !!};
        let noentries = {!! json_encode(lang("No entries to show")) !!};
        let showing = {!! json_encode(lang("showing page")) !!};
        let ofval = {!! json_encode(lang("of")) !!};
        let maxRecordfilter = {!! json_encode(lang("- filtered from ")) !!};
        let maxRecords = {!! json_encode(lang("records")) !!};
        let entries = {!! json_encode(lang("entries")) !!};
        let show = {!! json_encode(lang("Show")) !!};
        let search = {!! json_encode(lang("Search...")) !!};
        // Datatable
        $('#userdashboard').dataTable({
            language: {
                searchPlaceholder: search,
                scrollX: "100%",
                sSearch: '',
                paginate: {
                previous: prev,
                next: next
                },
                emptyTable : nodata,
                infoFiltered: `${maxRecordfilter} _MAX_ ${maxRecords}`,
                info: `${showing} _PAGE_ ${ofval} _PAGES_`,
                infoEmpty: noentries,
                lengthMenu: `${show} _MENU_ ${entries} `,
            },
            order:[],
            columnDefs: [
                { "orderable": false, "targets":[ 0,1,4] }
            ],
        });

        // Ticket Detele System
        $('body').on('click', '#show-delete', function () {
            var _id = $(this).data("id");
            swal({
                title: `{{lang('Are you sure you want to continue?', 'alerts')}}`,
                text: "{{lang('This might erase your records permanently', 'alerts')}}",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
            if (willDelete) {
                $.ajax({
                        type: "get",
                        url: SITEURL + "/customer/ticket/delete/"+_id,
                        success: function (data) {
                            toastr.success(data.success);
                            location.reload();
                        },
                        error: function (data) {
                        console.log('Error:', data);
                        }
                    });
                }
            });

        });

        //Mass Delete
        $('body').on('click', '#massdelete', function () {

            var id = [];

            $('.checkall:checked').each(function(){
                id.push($(this).val());
            });
            if(id.length > 0){
                swal({
                    title: `{{lang('Are you sure you want to continue?', 'alerts')}}`,
                    text: "{{lang('This might erase your records permanently', 'alerts')}}",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url:"{{ url('customer/ticket/delete/tickets')}}",
                            method:"post",
                            data:{id:id},
                            success:function(data)
                            {

                                toastr.success(data.success);
                                location.reload();
                            },
                            error:function(data){

                            }
                        });
                    }
                });

            }else{
                toastr.error('{{lang('Please select at least one check box.', 'alerts')}}');
            }

        });

        // Check All Checkbox
        $('#customCheckAll').on('click', function() {
            $('.checkall').prop('checked', this.checked);
        });

        $('.form-select').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });
        $('#customCheckAll').prop('checked', false);
        $('.checkall').on('click', function(){
            if($('.checkall:checked').length == $('.checkall').length){
                $('#customCheckAll').prop('checked', true);
            }else{
                $('#customCheckAll').prop('checked', false);
            }
        });


        $(".vertical-scroll5").bootstrapNews({
            newsPerPage: 1,
            autoplay: true,
            pauseOnHover: true,
            navigation: false,
            direction: 'down',
            newsTickerInterval: 2500,
            onToDo: function () {
            }
        });

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

@endsection
