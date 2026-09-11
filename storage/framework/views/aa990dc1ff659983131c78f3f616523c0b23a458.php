<?php $__env->startSection('styles'); ?>

    <!-- INTERNAL Data table css -->
    <link href="<?php echo e(asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/plugins/datatable/responsive.bootstrap5.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/plugins/datatable/buttonbootstrap.min.css')); ?>" rel="stylesheet" />

    <!-- INTERNAL Sweet-Alert css -->
    <link href="<?php echo e(asset('assets/plugins/sweet-alert/sweetalert.css')); ?>" rel="stylesheet" />

    <style>
        .uhelp-reply-badge {
        right: 14px;
        bottom: 10px;
        z-index: 1;
        }
        .pulse-badge  {
        animation: pulse 1s linear infinite;
        }
        .pulse-badge.disabled {
        color: #b5c0df;
        animation: none;
        }
        @-webkit-keyframes pulse {
        0% {
        color: rgba(13, 205, 148, 0);
        }

        50% {
        color: rgba(13, 205, 148, 1);
        }

        100% {
        color: rgba(13, 205, 148, 0);
        }
        }

        @keyframes  pulse {
        0% {
        -moz-color: rgba(13, 205, 148, 0);
        color: rgba(13, 205, 148, 0);
        }

        50% {
        -moz-color: rgba(13, 205, 148, 1);
        color: rgba(13, 205, 148, 1);
        }

        100% {
        -moz-color: rgba(13, 205, 148, 0);
        color: rgba(13, 205, 148, 0);
        }
        }
    </style>

<?php $__env->stopSection(); ?>

    <?php $__env->startSection('content'); ?>

    <!--- Custom notification -->
    <?php
        $mailnotify = auth()->user()->unreadNotifications()->where('data->status', 'mail')->get();

    ?>
    <?php if($mailnotify->isNotEmpty()): ?>
        <div class="alert alert-warning-light br-13 mt-6 align-items-center border-0 d-flex" role="alert">
            <div class="d-flex">
                <svg class="alt-notify me-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path fill="#eec466"
                        d="M19,20H5a3.00328,3.00328,0,0,1-3-3V7A3.00328,3.00328,0,0,1,5,4H19a3.00328,3.00328,0,0,1,3,3V17A3.00328,3.00328,0,0,1,19,20Z" />
                    <path fill="#e49e00"
                        d="M22,7a3.00328,3.00328,0,0,0-3-3H5A3.00328,3.00328,0,0,0,2,7V8.061l9.47852,5.79248a1.00149,1.00149,0,0,0,1.043,0L22,8.061Z" />
                </svg>
            </div>
            <ul class="notify vertical-scroll5 custom-ul ht-0 me-5">
                <?php if(auth()->user()): ?>
                    <?php $__empty_1 = true; $__currentLoopData = $mailnotify; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>

                        <?php if($notification->data['status'] == 'mail'): ?>
                            <li class="item">
                                <p class="fs-13 mb-0"><?php echo e($notification->data['mailsubject']); ?>

                                    <?php echo e(Str::limit($notification->data['mailtext'], '400', '...')); ?> <a href="<?php echo e(route('admin.notiication.view', $notification->id)); ?>"
                                    class="ms-3 text-blue mark-as-read"><?php echo e(lang('Read more')); ?></a></p>
                            </li>
                        <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
            <div class="d-flex ms-6 sprukocnotify">
                <button class="btn-close ms-2 mt-0 text-warning" data-bs-dismiss="alert"
                    aria-hidden="true">×</button>
            </div>
        </div>
    <?php endif; ?>
    <!--- End Custom notification -->

    <!--Page header-->
    <div class="page-header d-xl-flex d-block">
        <div class="page-leftheader">
            <h4 class="page-title"><span class="font-weight-normal text-muted ms-2"><?php echo e(lang('Dashboard', 'menu')); ?></span></h4>
        </div>
        <div class="page-rightheader ms-md-auto">
            <div class="d-flex align-items-end flex-wrap my-auto end-content breadcrumb-end">
                <div class="d-flex breadcrumb-res">
                    <div class="header-datepicker me-3">
                        <div class="input-group">
                            <div class="input-group-text">
                                    <i class="feather feather-calendar"></i>
                            </div>
                            <!-- <input class="form-control fc-datepicker pb-0 pt-0" value="<?php echo e(now(Auth::user()->timezone)->format(setting('date_format'))); ?>" type="text" disabled> -->
                            <span class="form-control fc-datepicker pb-0 pt-1"><?php echo e(now(Auth::user()->timezone)->format(setting('date_format'))); ?></span>
                        </div>
                    </div>
                    <div class="header-datepicker picker2 me-3">
                        <div class="input-group">
                            <div class="input-group-text">
                                    <i class="feather feather-clock"></i>
                            </div><!-- input-group-text -->
                            <span id="tpBasic" placeholder="" class="form-control input-small pb-0 pt-1" >

                                <?php echo e(\Carbon\Carbon::now(Auth::user()->timezone)->format(setting('time_format'))); ?>


                            </span>

                        </div>
                    </div><!-- wd-150 -->
                </div>
            </div>
        </div>
    </div>
    <!--End Page header-->

    <!--Dashboard List-->
    <h6 class="fw-semibold mb-3">
        <?php echo e(lang('Global Tickets', 'menu')); ?>

    </h6>
    <div class="row row-cols-xxl-5">
        <div class="col-xxl-2 col-xl-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(url('/admin/alltickets')); ?>">
                        <div class="d-flex">
                            <div class="icon2 bg-primary-transparent my-auto me-3">
                                <i class="las la-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1"><?php echo e(lang('All Tickets')); ?> </p>
                                <h5 class="mb-0"><?php echo e($totaltickets); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(route('admin.recenttickets')); ?>">
                        <div class="d-flex">
                            <div class="icon2 bg-secondary-transparent my-auto me-3">
                                <i class="las la-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1"><?php echo e(lang('Recent Tickets')); ?> </p>
                                <h5 class="mb-0"><?php echo e($recentticketcount); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(url('/admin/activeticket')); ?>">
                        <div class="d-flex">
                            <div class="icon2 bg-success-transparent my-auto me-3">
                                <i class="las la-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1"><?php echo e(lang('Active Tickets')); ?> </p>
                                <h5 class="mb-0"><?php echo e($totalactivetickets); ?></h5>
                                <?php if($replyrecent > 0): ?>
                                    <span class="position-absolute uhelp-reply-badge pulse-badge" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Un-Answered"><i class="fa fa-commenting me-1"></i><?php echo e($replyrecent); ?></span>
                                <?php else: ?>
                                    <span class="position-absolute uhelp-reply-badge pulse-badge disabled" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Un-Answered"><i class="fa fa-commenting me-1"></i>0</span>
                                <?php endif; ?>

                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(route('admin.suspendedtickets')); ?>">
                        <div class="d-flex">
                            <div class="icon2 bg-warning-transparent my-auto me-3">
                                <i class="las la-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1"><?php echo e(lang('Suspended Tickets')); ?></p>
                                <h5 class="mb-0"><?php echo e($suspendedticketcount); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xxl-2 col-xl-6 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(url('/admin/closedticket')); ?>">
                        <div class="d-flex">
                            <div class="icon2 bg-danger-transparent my-auto me-3">
                                <i class="las la-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1"><?php echo e(lang('Closed Tickets')); ?> </p>
                                <h5 class="mb-0"><?php echo e($totalclosedtickets); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <h6 class="fw-semibold mb-3">
        <?php echo e(lang('Self Tickets')); ?>

    </h6>
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(route('admin.selfassignticketview')); ?>">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <svg class="ticket-new primary svg-primary" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                                    <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3
                                        c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1
                                        c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859
                                        C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859
                                        C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                                    <rect width="2" height="2" x="41.963" y="27"></rect>
                                    <rect width="2" height="2" x="41.963" y="31"></rect>
                                    <rect width="2" height="2" x="41.963" y="19"></rect>
                                    <rect width="2" height="2" x="41.963" y="35"></rect>
                                    <rect width="2" height="2" x="41.963" y="23"></rect>
                                    <rect width="2" height="2" x="41.963" y="39"></rect>
                                </svg>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1">
                                    <?php echo e(lang('Self assigned Tickets')); ?></p>
                                    <h5 class="mb-0"><?php echo e($selfassigncount); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(url('/admin/myassignedtickets')); ?>">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <svg class="ticket-new bg-success-transparent svg-success" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                                    <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3
                                        c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1
                                        c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859
                                        C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859
                                        C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                                    <rect width="2" height="2" x="41.963" y="27"></rect>
                                    <rect width="2" height="2" x="41.963" y="31"></rect>
                                    <rect width="2" height="2" x="41.963" y="19"></rect>
                                    <rect width="2" height="2" x="41.963" y="35"></rect>
                                    <rect width="2" height="2" x="41.963" y="23"></rect>
                                    <rect width="2" height="2" x="41.963" y="39"></rect>
                                </svg>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1">
                                    <?php echo e(lang('My Assigned Tickets')); ?></p>
                                    <h5 class="mb-0"><?php echo e($myassignedticketcount); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(route('admin.myclosedtickets')); ?>">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <svg class="ticket-new bg-danger-transparent svg-danger" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                                    <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3
                                        c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1
                                        c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859
                                        C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859
                                        C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                                    <rect width="2" height="2" x="41.963" y="27"></rect>
                                    <rect width="2" height="2" x="41.963" y="31"></rect>
                                    <rect width="2" height="2" x="41.963" y="19"></rect>
                                    <rect width="2" height="2" x="41.963" y="35"></rect>
                                    <rect width="2" height="2" x="41.963" y="23"></rect>
                                    <rect width="2" height="2" x="41.963" y="39"></rect>
                                </svg>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1">
                                    <?php echo e(lang('Closed Tickets')); ?></p>
                                    <h5 class="mb-0"><?php echo e($myclosedticketcount); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-sm-6">
            <div class="card">
                <div class="card-body p-4">
                    <a href="<?php echo e(route('admin.mysuspendtickets')); ?>">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <svg class="ticket-new bg-warning-transparent svg-warning" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 60 60" viewBox="0 0 60 60">
                                    <path d="M54,15H6c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1c1.6542969,0,3,1.3457031,3,3s-1.3457031,3-3,3
                                        c-0.5522461,0-1,0.4477539-1,1v10c0,0.5522461,0.4477539,1,1,1h48c0.5522461,0,1-0.4477539,1-1V34c0-0.5522461-0.4477539-1-1-1
                                        c-1.6542969,0-3-1.3457031-3-3s1.3457031-3,3-3c0.5522461,0,1-0.4477539,1-1V16C55,15.4477539,54.5522461,15,54,15z M53,25.1005859
                                        C50.7207031,25.5649414,49,27.5854492,49,30s1.7207031,4.4350586,4,4.8994141V43h-9.0371094h-2H7v-8.1005859
                                        C9.2792969,34.4350586,11,32.4145508,11,30s-1.7207031-4.4350586-4-4.8994141V17h34.9628906h2H53V25.1005859z"></path>
                                    <rect width="2" height="2" x="41.963" y="27"></rect>
                                    <rect width="2" height="2" x="41.963" y="31"></rect>
                                    <rect width="2" height="2" x="41.963" y="19"></rect>
                                    <rect width="2" height="2" x="41.963" y="35"></rect>
                                    <rect width="2" height="2" x="41.963" y="23"></rect>
                                    <rect width="2" height="2" x="41.963" y="39"></rect>
                                </svg>
                            </div>
                            <div>
                                <p class="fs-14 font-weight-semibold mb-1">
                                    <?php echo e(lang('Suspend Tickets')); ?></p>
                                    <h5 class="mb-0"><?php echo e($suspendticketcount); ?></h5>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--Dashboard List-->


    <!-- Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom-0">
                    <h4 class="card-title"><?php echo e(lang('Recent tickets')); ?></h4>
                </div>
                <div class="card-body overflow-scroll">
                    <div class="">
                        <div class="data-table-btn">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Ticket Delete')): ?>

                            <button id="massdelete" class="btn btn-outline-light btn-sm mb-4 "><i class="fe fe-trash"></i><span><?php echo e(lang('Delete')); ?></span></button>
                            <?php endif; ?>

                            <button id="refreshdata" class="btn btn-outline-light btn-sm mb-4 "><i class="fe fe-refresh-cw"></i> </button>
                        </div>
                        <div class="sprukoloader-img"><i class="fa fa-spinner fa-spin"></i><span><?php echo e(lang('Loading....')); ?></span></div>
                        <div class="dashboardtabledata">
                            <table class="table table-bordered border-bottom text-nowrap" id="supportticket-dashe">
                                <thead>
                                    <tr>
                                        <th class="wpx-40 text-center"><?php echo e(lang('Sl.No')); ?></th>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Ticket Delete')): ?>

                                        <th class="wpx-40 text-center">
                                            <input type="checkbox"  id="customCheckAll">
                                            <label  for="customCheckAll"></label>
                                        </th>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->denies('Ticket Delete')): ?>

                                        <th class="wpx-40 text-center">
                                            <input type="checkbox"  id="customCheckAll" disabled>
                                            <label  for="customCheckAll"></label>
                                        </th>
                                        <?php endif; ?>
                                        <th class="ticket-dets">
                                            <?php echo e(lang('Ticket Details')); ?>

                                        </th>
                                        <th><?php echo e(lang('User')); ?></th>
                                        <th ><?php echo e(lang('Status')); ?></th>
                                        <th>
                                            <?php echo e(lang('Assign To')); ?>

                                        </th>
                                        <th><?php echo e(lang('Actions')); ?></th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Row -->





    <!--Dashboard List-->

    <?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>

<!-- INTERNAL Vertical-scroll js-->
<script src="<?php echo e(asset('assets/plugins/vertical-scroll/jquery.bootstrap.newsbox.js')); ?>"></script>

<!-- INTERNAL Data tables -->
<script src="<?php echo e(asset('assets/plugins/datatable/js/jquery.dataTables.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/datatable/dataTables.responsive.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/datatable/responsive.bootstrap5.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/datatable/datatablebutton.min.js')); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/datatable/buttonbootstrap.min.js')); ?>"></script>


<!-- INTERNAL Index js-->
<script src="<?php echo e(asset('assets/js/support/support-sidemenu.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/select2.js')); ?>"></script>

<!-- INTERNAL Sweet-Alert js-->
<script src="<?php echo e(asset('assets/plugins/sweet-alert/sweetalert.min.js')); ?>"></script>

<!-- INTERNAL Apexchart js-->
<script src="<?php echo e(asset('assets/plugins/apexchart/apexcharts.js')); ?>"></script>

<script type="text/javascript">

    "use strict";

    (function($)  {

        var SITEURL = '<?php echo e(url('')); ?>',
        timeurl = '<?php echo e(route('timeupdate')); ?>';
        $('#tpBasic').load(timeurl);
        setInterval(() => {

            $('#tpBasic').load(timeurl);

        }, 1000);

        <?php if(Auth::user()->usetting != null): ?>
        <?php if(Auth::user()->usetting->ticket_refresh == 1): ?>

        // Auto Refresh Datatable js
        setInterval(function(){
            $('.sprukoloader-img').fadeIn();
            $('.dashboardtabledata').load('<?php echo e(route('admin.dashboardtabledata')); ?>', ()=>{
                $('.sprukoloader-img').fadeOut();
            });

        },30000);

        <?php endif; ?>
        <?php endif; ?>

        // csrf field
        $.ajaxSetup({
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.sprukoloader-img').fadeIn();

        // // Datatable
            $('.dashboardtabledata').load('<?php echo e(route('admin.dashboardtabledata')); ?>', ()=>{
            $('.sprukoloader-img').fadeOut();
            });

            $('#refreshdata').on('click', function(e){
            e.preventDefault();;
            $('.sprukoloader-img').fadeIn();
            $('.dashboardtabledata').load('<?php echo e(route('admin.dashboardtabledata')); ?>', ()=>{
                $('.sprukoloader-img').fadeOut();
            });
            })

        // TICKET DELETE SCRIPT
        $('body').on('click', '#show-delete', function () {
                var _id = $(this).data("id");
                swal({
                    title: `<?php echo e(lang('Are you sure you want to continue?', 'alerts')); ?>`,
                    text: "<?php echo e(lang('This might erase your records permanently', 'alerts')); ?>",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                            type: "get",
                            url: SITEURL + "/admin/delete-ticket/"+_id,
                        success: function (data) {
                            toastr.success(data.success);
                            location.reload();
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        },
                    });
                }
            });

        });
        // TICKET DELETE SCRIPT END

        // when user click its get modal popup to assigned the ticket
        $('body').on('click', '#assigned', function () {
            var assigned_id = $(this).data('id');
            $('.select2_modalassign').select2({
                dropdownParent: ".sprukosearch",
                minimumResultsForSearch: '',
                placeholder: "Search",
                width: '100%'
            });
            $.get('admin/assigned/' + assigned_id , function (data) {
                $('#AssignError').html('');
                $('#assigned_id').val(data.assign_data.id);
                $(".modal-title").text('<?php echo e(lang('Assign To Agent')); ?>');
                $('#username').html(data.table_data);
                $('#addassigned').modal('show');
            });
        });

        // Assigned Submit button
        $('body').on('submit', '#assigned_form', function (e) {
            e.preventDefault();
            var actionType = $('#btnsave').val();
            var fewSeconds = 2;
            $('#btnsave').html('Sending..');
            $('#btnsave').prop('disabled', true);
                setTimeout(function(){
                    $('#btnsave').prop('disabled', false);
                }, fewSeconds*1000);
            var formData = new FormData(this);
            $.ajax({
                type:'POST',
                url: SITEURL + "/admin/assigned/create",
                data: formData,
                cache:false,
                contentType: false,
                processData: false,

                success: (data) => {

                    $('#AssignError').html('');
                    $('#assigned_form').trigger("reset");
                    $('#addassigned').modal('hide');
                    $('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
                    $('#assigned').html('gfhffh');
                    location.reload();
                    toastr.success(data.success);
                },
                error: function(data){
                    $('#AssignError').html('');
                    $('#AssignError').html(data.responseJSON.errors.assigned_user_id);
                    $('#btnsave').html('<?php echo e(lang('Save Changes')); ?>');
                }
            });
        });

        // Remove the assigned from the ticket
        $('body').on('click', '#btnremove', function () {
            var asid = $(this).data("id");
            swal({
                title: `<?php echo e(lang('Are you sure you want to unassign this agent?', 'alerts')); ?>`,
                text: "<?php echo e(lang('This agent may no longer exist for this ticket.', 'alerts')); ?>",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {

                    $.ajax({
                        type: "get",
                        url: SITEURL + "/admin/assigned/update/"+asid,
                        success: function (data) {
                        location.reload();
                        toastr.success(data.success);

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
                    title: `<?php echo e(lang('Are you sure you want to continue?', 'alerts')); ?>`,
                    text: "<?php echo e(lang('This might erase your records permanently', 'alerts')); ?>",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url:"<?php echo e(url('admin/ticket/delete/tickets')); ?>",
                            method:"GET",
                            data:{id:id},
                            success:function(data)
                            {
                                location.reload();
                                toastr.success(data.success);

                            },
                            error:function(data){

                            }
                        });
                    }
                });
            }
            else{
                toastr.error('<?php echo e(lang('Please select at least one check box.', 'alerts')); ?>');
            }

        });

        // $('#supportticket-dashe').dataTable({

        // 	language: {
        // 		searchPlaceholder: search,
        // 		sSearch: '',

        // 	},
        // 	order:[],
        // 	columnDefs: [
        // 		{ "orderable": false, "targets":[ 0,1,6] }
        // 	],
        // });

        let prev = <?php echo json_encode(lang("Previous")); ?>;
        let next = <?php echo json_encode(lang("Next")); ?>;
        let nodata = <?php echo json_encode(lang("No data available in table")); ?>;
        let noentries = <?php echo json_encode(lang("No entries to show")); ?>;
        let showing = <?php echo json_encode(lang("showing page")); ?>;
        let ofval = <?php echo json_encode(lang("of")); ?>;
        let maxRecordfilter = <?php echo json_encode(lang("- filtered from ")); ?>;
        let maxRecords = <?php echo json_encode(lang("records")); ?>;
        let entries = <?php echo json_encode(lang("entries")); ?>;
        let show = <?php echo json_encode(lang("Show")); ?>;
        let search = <?php echo json_encode(lang("Search...")); ?>;
        // Datatable
        $('#supportticket-dashe').dataTable({
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

        // Checkbox checkall
        $('#customCheckAll').on('click', function() {
            $('.checkall').prop('checked', this.checked);
        });

        $('body').on('click','#selfassigid', function(e){

            e.preventDefault();

            let id = $(this).data('id');

            $.ajax({
                method:'POST',
                url: '<?php echo e(route('admin.selfassign')); ?>',
                data: {
                    id : id,
                },
                success: (data) => {
                    toastr.success(data.success);
                    location.reload();
                },
                error: function(data){

                }
            });
        })

        $(".vertical-scroll5").bootstrapNews({
            newsPerPage: 1,
            autoplay: true,
            pauseOnHover: true,
            navigation: false,
            direction: 'down',
            newsTickerInterval: 2500,
            onToDo: function () {
                //console.log(this);
            }
        });

    })(jQuery);

</script>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('modal'); ?>

<?php echo $__env->make('admin.modalpopup.assignmodal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.adminmaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/admin/superadmindashboard/dashboard.blade.php ENDPATH**/ ?>