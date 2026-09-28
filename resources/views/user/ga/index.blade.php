@extends('layouts.usermasterga')

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
                        <h1 class="mb-0">{{lang('GA Request')}}</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('ga.index')}}" class="text-white-50">{{lang('Home')}}</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="#" class="text-white">{{lang('GA Request')}}</a>
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
                @include('includes.user.verticalmenuga')

                <div class="col-xl-9">

                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="alert alert-info-light br-13 border-0 d-flex align-items-center" role="alert">
                        <div class="d-flex">
                            <i class="fe fe-shopping-cart me-3 fs-18 text-info"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-1">Pengajuan Permintaan GA</h6>
                            <p class="mb-0 fs-13">Anda memiliki <strong>{{ $total ?? 0 }}</strong> permintaan GA total.</p>
                        </div>
                        <div>
                            <a href="{{ route('ga.create') }}" class="btn btn-sm btn-primary">
                                <i class="fe fe-plus"></i> {{lang('Buat Request')}}
                            </a>
                        </div>
                    </div>

                    <!-- Preferensi Notifikasi GA -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{lang('Preferensi Notifikasi GA')}}</h3>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('ga.updatePref') }}" class="row align-items-center g-3">
                                @csrf
                                <div class="col-md-6">
                                    <select name="notification_pref" class="form-control">
                                        <option value="email" {{ ($notificationPref ?? 'email') === 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="whatsapp" {{ ($notificationPref ?? 'email') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="both" {{ ($notificationPref ?? 'email') === 'both' ? 'selected' : '' }}>Email &amp; WhatsApp</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Notifikasi status GA Request dikirim sesuai preferensi ini.</small>
                                </div>
                                <div class="col-md-2 text-end">
                                    <button type="submit" class="btn btn-sm btn-primary w-100">
                                        <i class="fe fe-save"></i> {{lang('Simpan')}}
                                    </button>
                                </div>
                            </form>
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

                        <div class="col-xl-4 col-lg-4 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="mt-0 text-start">
                                                <span class="fs-16 font-weight-semibold">{{lang('Pending L1')}}</span>
                                                <h3 class="mb-0 mt-1 text-warning fs-25">{{ $pendingL1 ?? 0 }}</h3>
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

                        <div class="col-xl-2 col-lg-2 col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="mt-0 text-start">
                                                <span class="fs-16 font-weight-semibold">{{lang('Pending L2')}}</span>
                                                <h3 class="mb-0 mt-1 text-warning fs-25">{{ $pendingL2 ?? 0 }}</h3>
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
                    </div>

                    <!-- Tabel Request -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{lang('Daftar GA Request')}}</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered text-nowrap border-bottom dt-responsive" id="ga-table">
                                    <thead>
                                        <tr>
                                            <th class="wd-15p border-bottom-0">{{lang('No. Request')}}</th>
                                            <th class="wd-15p border-bottom-0">{{lang('Tanggal')}}</th>
                                            <th class="wd-20p border-bottom-0">{{lang('Keperluan / Items')}}</th>
                                            <th class="wd-15p border-bottom-0">{{lang('Total')}}</th>
                                            <th class="wd-15p border-bottom-0">{{lang('Status')}}</th>
                                            <th class="wd-15p border-bottom-0">{{lang('Aksi')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($gaRequests as $gaRequest)
                                        <tr>
                                            <td>
                                                <a href="{{ route('ga.show', $gaRequest->id) }}"><span class="badge bg-primary">{{ $gaRequest->request_no }}</span></a>
                                                <br>
                                                <span class="fs-12 text-muted">{{ $gaRequest->nama_lengkap }}</span>
                                            </td>
                                            <td>{{ optional($gaRequest->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <span class="mb-0">
                                                    {{ $gaRequest->goods_total > 0 ? number_format($gaRequest->goods_total, 0, ',', '.') . ' (Barang)' : '' }}
                                                    @if($gaRequest->goods_total > 0 && $gaRequest->services_total > 0) / @endif
                                                    {{ $gaRequest->services_total > 0 ? number_format($gaRequest->services_total, 0, ',', '.') . ' (Jasa)' : '' }}
                                                </span>
                                            </td>
                                            <td><span class="font-weight-bold">Rp {{ number_format($gaRequest->total_amount, 0, ',', '.') }}</span></td>
                                            <td>
                                                @if($gaRequest->status == 'pending_l1')
                                                    <span class="badge bg-warning text-dark">Pending L1</span>
                                                @elseif($gaRequest->status == 'pending_l2')
                                                    <span class="badge bg-orange text-dark">Pending L2</span>
                                                @elseif($gaRequest->status == 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($gaRequest->status == 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @elseif($gaRequest->status == 'cancelled')
                                                    <span class="badge bg-secondary">Cancelled</span>
                                                @elseif($gaRequest->status == 'expired')
                                                    <span class="badge bg-dark">Expired</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('ga.show', $gaRequest->id) }}" class="btn btn-sm btn-info"><i class="fe fe-eye"></i></a>
                                                @if($gaRequest->status == 'approved')
                                                <a href="{{ route('ga.downloadPdf', $gaRequest->id) }}" class="btn btn-sm btn-success"><i class="fe fe-download"></i></a>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#ga-table').DataTable({
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Tidak ada data",
                infoFiltered: "(difilter dari _MAX_ total data)",
                zeroRecords: "Tidak ada data yang cocok",
                paginate: { previous: "Sebelumnya", next: "Berikutnya" }
            }
        });
    });
</script>
@endsection