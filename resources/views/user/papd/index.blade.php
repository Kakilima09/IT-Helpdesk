@extends('layouts.papdmaster')

@section('styles')
<!-- INTERNAL Data table css -->
<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />
<link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}?v=<?php echo time(); ?>" rel="stylesheet" />
@endsection

@section('content')
<!-- Section Banner -->
<section>
    <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="mb-0">{{lang('Dashboard PAPD')}}</h1>
                        @auth('customer')
                        <p class="mb-0 fs-14 opacity-75">Welcome back, {{ Auth::guard('customer')->user()->firstname ?? '' }} {{ Auth::guard('customer')->user()->lastname ?? '' }}!</p>
                        @endauth
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('papd.index')}}" class="text-white-50">{{lang('Home', 'menu')}}</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="{{route('papd.index')}}" class="text-white">{{lang('PAPD Dashboard')}}</a>
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
                @include('includes.user.verticalmenupapd')

                <div class="col-xl-9">

                    <!-- Welcome message -->
                    <div class="alert alert-info-light br-13 border-0 d-flex align-items-center" role="alert">
                        <div class="d-flex">
                            <i class="fe fe-clipboard me-3 fs-18 text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">Permintaan Perjalanan Dinas</h6>
                            <p class="mb-0 fs-13">Anda memiliki <strong>{{ $total ?? 0 }}</strong> permintaan PAPD total.</p>
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
                                                <span class="fs-16 font-weight-semibold">{{lang('Total Permintaan')}}</span>
                                                <h3 class="mb-0 mt-1 text-primary fs-25">{{ $total ?? 0 }}</h3>
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
                                                <span class="fs-16 font-weight-semibold">{{lang('Pending')}}</span>
                                                <h3 class="mb-0 mt-1 text-warning fs-25">{{ $pending ?? 0 }}</h3>
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
                                                <span class="fs-16 font-weight-semibold">{{lang('Approved')}}</span>
                                                <h3 class="mb-0 mt-1 text-success fs-25">{{ $approved ?? 0 }}</h3>
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
                                                <span class="fs-16 font-weight-semibold">{{lang('Rejected')}}</span>
                                                <h3 class="mb-0 mt-1 text-danger fs-25">{{ $rejected ?? 0 }}</h3>
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
                                                <span class="fs-16 font-weight-semibold">{{lang('Expired')}}</span>
                                                <h3 class="mb-0 mt-1 text-secondary fs-25">{{ $expired ?? 0 }}</h3>
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
                                    <h4 class="card-title">{{lang('Daftar Permintaan PAPD')}}</h4>
                                    <div class="float-end ms-auto">
                                        <a href="{{route('papd.create')}}" class="btn btn-secondary ms-auto">
                                            <i class="fa fa-plus me-2"></i>{{lang('Buat PAPD')}}
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
                                    <div class="table-responsive">
                                        @if(setting('CUSTOMER_RESTICT_TO_DELETE_TICKET') == 'off')
                                            <button id="massdelete" class="btn btn-outline-light btn-sm ms-7 mb-4 data-table-btn mx-md-center">
                                                <i class="fe fe-trash me-1"></i> {{lang('Delete')}}
                                            </button>
                                        @endif
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
                                                @forelse($papdRequests as $papd)
                                                <tr>
                                                    <td>
                                                        @if($papd->status == 'pending')
                                                            <input type="checkbox" class="checkall" value="{{ $papd->id }}">
                                                        @endif
                                                    </td>
                                                    <td>{{ $papd->id }}</td>
                                                    <td>{{ $papd->kota_tujuan }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($papd->tanggal_keberangkatan)->format('d-m-Y') }} {{ $papd->jam_keberangkatan }}</td>
                                                    <td>{{ $papd->tanggal_kepulangan ? \Carbon\Carbon::parse($papd->tanggal_kepulangan)->format('d-m-Y') : '-' }}
                                                        {{ $papd->jam_kepulangan ?? '' }}</td>
                                                    <td>
                                                        @if($papd->status == 'pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @elseif($papd->status == 'approved')
                                                            <span class="badge badge-success">Approved</span>
                                                        @elseif($papd->status == 'rejected')
                                                            <span class="badge badge-danger">Rejected</span>
                                                        @elseif($papd->status == 'expired')
                                                            <span class="badge badge-secondary">Expired</span>
                                                        @else
                                                            <span class="badge badge-light">{{ $papd->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('papd.show', $papd->id) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detail">
                                                            <i class="fe fe-eye"></i>
                                                        </a>
                                                        @if($papd->status == 'pending')
                                                            <a href="{{ route('papd.edit', $papd->id) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Edit">
                                                                <i class="fe fe-edit"></i>
                                                            </a>
                                                            <button class="btn btn-sm btn-danger delete-single" data-id="{{ $papd->id }}" data-bs-toggle="tooltip" title="Hapus">
                                                                <i class="fe fe-trash"></i>
                                                            </button>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td class="text-center">Belum ada permintaan PAPD.</td>
                                                    </tr>
                                                @endforelse
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
@endsection

@section('scripts')
<!-- INTERNAL Data tables -->
<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}?v=<?php echo time(); ?>"></script>
<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}?v=<?php echo time(); ?>"></script>

<!-- INTERNAL Index js -->
<script src="{{asset('assets/js/support/support-sidemenu.js')}}?v=<?php echo time(); ?>"></script>

<script type="text/javascript">
    "use strict";

    (function($) {
        var SITEURL = '{{ url('/') }}';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // ============ 1. Variabel bahasa ============
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
                title: "{{ lang('Are you sure?') }}",
                text: "{{ lang('This action cannot be undone.') }}",
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
                    title: "{{ lang('Are you sure?') }}",
                    text: "{{ lang('This will delete selected pending requests.') }}",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $('#massdelete').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> {{ lang("Loading...") }}');
                        $.ajax({
                            url: "{{ route('papd.massdestroy') }}",
                            method: "POST",
                            data: { id: id },
                            success: function(data) {
                                toastr.success(data.success);
                                location.reload();
                            },
                            error: function(xhr) {
                                console.error('Mass delete error:', xhr);
                                $('#massdelete').prop('disabled', false).html('<i class="fe fe-trash me-1"></i> {{ lang("Delete") }}');
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
                toastr.error('{{ lang("Please select at least one request.") }}');
            }
        });

        console.log('DataTable initialized successfully.');
    })(jQuery);
</script>
@endsection