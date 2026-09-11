

<?php $__env->startSection('styles'); ?>
<!-- INTERNAL Data table css -->
<link href="<?php echo e(asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
<link href="<?php echo e(asset('assets/plugins/datatable/responsive.bootstrap5.css')); ?>?v=<?php echo time(); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<!-- Section Banner -->
<section>
    <div class="bannerimg cover-image" data-bs-image-src="<?php echo e(asset('assets/images/photos/banner1.jpg')); ?>">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="mb-0"><?php echo e(lang('Dashboard PAPD')); ?></h1>
                        <?php if(auth()->guard('customer')->check()): ?>
                        <p class="mb-0 fs-14 opacity-75">Welcome back, <?php echo e(Auth::guard('customer')->user()->firstname ?? ''); ?> <?php echo e(Auth::guard('customer')->user()->lastname ?? ''); ?>!</p>
                        <?php endif; ?>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="<?php echo e(route('papd.index')); ?>" class="text-white-50"><?php echo e(lang('Home', 'menu')); ?></a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="<?php echo e(route('papd.index')); ?>" class="text-white"><?php echo e(lang('PAPD Dashboard')); ?></a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section -->
<section>
    <div class="cover-image sptb">
        <div class="container">
            <div class="row">
                <?php echo $__env->make('includes.user.verticalmenupapd', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                <div class="col-xl-9">

                    <!-- Welcome message -->
                    <div class="alert alert-info-light br-13 border-0 d-flex align-items-center" role="alert">
                        <div class="d-flex">
                            <i class="fe fe-clipboard me-3 fs-18 text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">Permintaan Perjalanan Dinas</h6>
                            <p class="mb-0 fs-13">Anda memiliki <strong><?php echo e($total ?? 0); ?></strong> permintaan PAPD total.</p>
                        </div>
                    </div>

                    <!-- Statistik Cards -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-3 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="mt-0 text-start">
                                                <span class="fs-16 font-weight-semibold"><?php echo e(lang('Total Permintaan')); ?></span>
                                                <h3 class="mb-0 mt-1 text-primary fs-25"><?php echo e($total ?? 0); ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="icon1 bg-primary-transparent my-auto float-end"> 
                                                <i class="las la-clipboard-list"></i> 
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
                                                <span class="fs-16 font-weight-semibold"><?php echo e(lang('Pending')); ?></span>
                                                <h3 class="mb-0 mt-1 text-warning fs-25"><?php echo e($pending ?? 0); ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="icon1 bg-warning-transparent my-auto float-end"> 
                                                <i class="ri-time-line"></i> 
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
                                                <span class="fs-16 font-weight-semibold"><?php echo e(lang('Approved')); ?></span>
                                                <h3 class="mb-0 mt-1 text-success fs-25"><?php echo e($approved ?? 0); ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="icon1 bg-success-transparent my-auto float-end"> 
                                                <i class="ri-checkbox-circle-line"></i> 
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
                                                <span class="fs-16 font-weight-semibold"><?php echo e(lang('Rejected')); ?></span>
                                                <h3 class="mb-0 mt-1 text-danger fs-25"><?php echo e($rejected ?? 0); ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="icon1 bg-danger-transparent my-auto float-end"> 
                                                <i class="ri-close-circle-line"></i> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Expired -->
                        <div class="col-xl-3 col-lg-3 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="mt-0 text-start">
                                                <span class="fs-16 font-weight-semibold"><?php echo e(lang('Expired')); ?></span>
                                                <h3 class="mb-0 mt-1 text-secondary fs-25"><?php echo e($expired ?? 0); ?></h3>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="icon1 bg-secondary-transparent my-auto float-end"> 
                                                <i class="ri-time-line"></i> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Daftar Permintaan -->
                    <div class="row">
                        <div class="col-xl-12 col-lg-12 col-md-12">
                            <div class="card mb-0">
                                <div class="card-header border-0 d-flex">
                                    <h4 class="card-title"><?php echo e(lang('Daftar Permintaan PAPD')); ?></h4>
                                    <div class="float-end ms-auto">
                                        <a href="<?php echo e(route('papd.create')); ?>" class="btn btn-secondary ms-auto">
                                            <i class="fa fa-plus me-2"></i><?php echo e(lang('Buat PAPD')); ?>

                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if(session('success')): ?>
                                        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
                                    <?php endif; ?>
                                    <?php if(session('error')): ?>
                                        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
                                    <?php endif; ?>
                                    <div class="table-responsive">
                                        <?php if(setting('CUSTOMER_RESTICT_TO_DELETE_TICKET') == 'off'): ?>
                                            <button id="massdelete" class="btn btn-outline-light btn-sm ms-7 mb-4 data-table-btn mx-md-center">
                                                <i class="fe fe-trash me-1"></i> <?php echo e(lang('Delete')); ?>

                                            </button>
                                        <?php endif; ?>
                                        <table class="table table-bordered border-bottom text-nowrap w-100" id="papdTable">
                                            <thead>
                                                <tr>
                                                    <th><input type="checkbox" id="customCheckAll"></th>
                                                    <th>ID</th>
                                                    <th>Tujuan</th>
                                                    <th>Keberangkatan</th>
                                                    <th>Kepulangan</th>
                                                    <th>Status</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__empty_1 = true; $__currentLoopData = $papdRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $papd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                                <tr>
                                                    <td>
                                                        <?php if($papd->status == 'pending'): ?>
                                                            <input type="checkbox" class="checkall" value="<?php echo e($papd->id); ?>">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo e($papd->id); ?></td>
                                                    <td><?php echo e($papd->kota_tujuan); ?></td>
                                                    <td><?php echo e(\Carbon\Carbon::parse($papd->tanggal_keberangkatan)->format('d-m-Y')); ?> <?php echo e($papd->jam_keberangkatan); ?></td>
                                                    <td><?php echo e($papd->tanggal_kepulangan ? \Carbon\Carbon::parse($papd->tanggal_kepulangan)->format('d-m-Y') : '-'); ?>

                                                        <?php echo e($papd->jam_kepulangan ?? ''); ?></td>
                                                    <td>
                                                        <?php if($papd->status == 'pending'): ?>
                                                            <span class="badge badge-warning">Pending</span>
                                                        <?php elseif($papd->status == 'approved'): ?>
                                                            <span class="badge badge-success">Approved</span>
                                                        <?php elseif($papd->status == 'rejected'): ?>
                                                            <span class="badge badge-danger">Rejected</span>
                                                        <?php elseif($papd->status == 'expired'): ?>
                                                            <span class="badge badge-secondary">Expired</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-light"><?php echo e($papd->status); ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <a href="<?php echo e(route('papd.show', $papd->id)); ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                            <i class="fe fe-eye"></i>
                                                        </a>
                                                        <?php if($papd->status == 'pending'): ?>
                                                            <a href="<?php echo e(route('papd.edit', $papd->id)); ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fe fe-edit"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-danger delete-single" data-id="<?php echo e($papd->id); ?>" data-bs-toggle="tooltip" title="Hapus">
                                                                <i class="fe fe-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-center">Belum ada permintaan PAPD.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<!-- INTERNAL Data tables -->
<script src="<?php echo e(asset('assets/plugins/datatable/js/jquery.dataTables.min.js')); ?>?v=<?php echo time(); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')); ?>?v=<?php echo time(); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/datatable/dataTables.responsive.min.js')); ?>?v=<?php echo time(); ?>"></script>
<script src="<?php echo e(asset('assets/plugins/datatable/responsive.bootstrap5.min.js')); ?>?v=<?php echo time(); ?>"></script>

<!-- INTERNAL Index js -->
<script src="<?php echo e(asset('assets/js/support/support-sidemenu.js')); ?>?v=<?php echo time(); ?>"></script>

<script type="text/javascript">
    "use strict";

    (function($) {
        var SITEURL = '<?php echo e(url('/')); ?>';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ============ 1. Variabel bahasa ============
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

        // ============ 2. Hancurkan instance lama ============
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#papdTable')) {
            $('#papdTable').DataTable().destroy();
        }

        // ============ 3. Inisialisasi DataTable ============
        var table = $('#papdTable').DataTable({
        destroy: true,
        retrieve: true,
        deferRender: true,
        autoWidth: false,
        // Tentukan kolom secara eksplisit (7 kolom)
        // columns: [
        //     { data: null, defaultContent: '' },  // checkbox
        //     { data: null, defaultContent: '' },  // ID
        //     { data: null, defaultContent: '' },  // Tujuan
        //     { data: null, defaultContent: '' },  // Keberangkatan
        //     { data: null, defaultContent: '' },  // Kepulangan
        //     { data: null, defaultContent: '' },  // Status
        //     { data: null, defaultContent: '' }   // Aksi
        // ],
        language: {
            searchPlaceholder: search,
            sSearch: '',
            paginate: {
                previous: prev,
                next: next
            },
            emptyTable: nodata,
            infoFiltered: `${maxRecordfilter} _MAX_ ${maxRecords}`,
            info: `${showing} _PAGE_ ${ofval} _PAGES_`,
            infoEmpty: noentries,
            lengthMenu: `${show} _MENU_ ${entries} `,
        },
        order: [[1, 'desc']],
        columnDefs: [
            { orderable: false, targets: [0, 6] },
            { searchable: false, targets: [0, 6] }
        ],
        initComplete: function() {
            initTooltips();
        }
    });

        function initTooltips() {
            if (typeof $ !== 'undefined' && $.fn.tooltip) {
                $('[data-bs-toggle="tooltip"]').tooltip();
                return;
            }
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                    try {
                        new bootstrap.Tooltip(el);
                    } catch (e) {}
                });
                return;
            }
            console.warn('Tooltip plugin tidak ditemukan.');
        }

        initTooltips();

        // ============ 4. Event handler ============
        $(document).on('click', '#customCheckAll', function() {
            $('.checkall').prop('checked', this.checked);
        });

        $(document).on('click', '.checkall', function() {
            if ($('.checkall:checked').length === $('.checkall').length) {
                $('#customCheckAll').prop('checked', true);
            } else {
                $('#customCheckAll').prop('checked', false);
            }
        });

        // Delete Single
        $(document).on('click', '.delete-single', function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var $btn = $(this);

            swal({
                title: "<?php echo e(lang('Are you sure?')); ?>",
                text: "<?php echo e(lang('This action cannot be undone.')); ?>",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                    $.ajax({
                        type: "DELETE",
                        url: SITEURL + "/customer/papd/" + id,
                        success: function(data) {
                            toastr.success(data.success || 'Permintaan berhasil dihapus.');
                            location.reload();
                        },
                        error: function(xhr) {
                            console.error('Delete error:', xhr);
                            $btn.prop('disabled', false).html('<i class="fe fe-trash"></i>');
                            if (xhr.responseJSON && xhr.responseJSON.error) {
                                toastr.error(xhr.responseJSON.error);
                            } else {
                                toastr.error('Gagal menghapus permintaan. Silakan coba lagi.');
                            }
                        }
                    });
                }
            });
        });

        // Mass Delete
        $(document).on('click', '#massdelete', function() {
            var id = [];
            $('.checkall:checked').each(function() {
                id.push($(this).val());
            });

            if (id.length > 0) {
                swal({
                    title: "<?php echo e(lang('Are you sure?')); ?>",
                    text: "<?php echo e(lang('This will delete selected pending requests.')); ?>",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $('#massdelete').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> <?php echo e(lang("Loading...")); ?>');
                        $.ajax({
                            url: "<?php echo e(route('papd.massdestroy')); ?>",
                            method: "POST",
                            data: { id: id },
                            success: function(data) {
                                toastr.success(data.success);
                                location.reload();
                            },
                            error: function(xhr) {
                                console.error('Mass delete error:', xhr);
                                $('#massdelete').prop('disabled', false).html('<i class="fe fe-trash me-1"></i> <?php echo e(lang("Delete")); ?>');
                                if (xhr.responseJSON && xhr.responseJSON.error) {
                                    toastr.error(xhr.responseJSON.error);
                                } else {
                                    toastr.error('Gagal menghapus data. Silakan coba lagi.');
                                }
                            }
                        });
                    }
                });
            } else {
                toastr.error('<?php echo e(lang("Please select at least one request.")); ?>');
            }
        });

        console.log('DataTable initialized successfully.');
    })(jQuery);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.papdmaster', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/user/papd/index.blade.php ENDPATH**/ ?>