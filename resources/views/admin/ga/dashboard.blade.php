@extends('layouts.adminmasterga')

@section('styles')
<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />
@endsection

@section('content')
<!-- Page header -->
<div class="page-header d-xl-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title">{{ lang('Dashboard GA') }}</h4>
        <p class="mb-0 text-muted fs-13">Ikhtisar pengajuan General Affairs (GA).</p>
    </div>
    <div class="page-rightheader ms-auto">
        <a href="{{ route('admin.ga.categories.index') }}" class="btn btn-info me-2">
            <i class="fe fe-layers"></i> {{ lang('Kategori Barang') }}
        </a>
        <a href="{{ route('admin.ga.export') }}" class="btn btn-success">
            <i class="fe fe-download"></i> Export Excel
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- KPI Cards -->
<div class="row">
    <div class="col-xl-3 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="avatar avatar-lg bg-primary-transparent text-primary rounded-circle"><i class="fe fe-clipboard"></i></span>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Total Request')}}</p>
                        <h5 class="mb-0">{{ $total }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="avatar avatar-lg bg-success-transparent text-success rounded-circle"><i class="fe fe-database"></i></span>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Total Nilai')}}</p>
                        <h5 class="mb-0 fs-17">Rp {{ number_format($totalAmount, 0, ',', '.') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="avatar avatar-lg bg-warning-transparent text-warning rounded-circle"><i class="fe fe-loader"></i></span>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Pending Persetujuan')}}</p>
                        <h5 class="mb-0">{{ $totalPendingL1 + $totalPendingL2 }}</h5>
                        <small class="text-muted">L1: {{ $totalPendingL1 }} / L2: {{ $totalPendingL2 }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <span class="avatar avatar-lg bg-danger-transparent text-danger rounded-circle"><i class="fe fe-alert-circle"></i></span>
                    </div>
                    <div>
                        <p class="fs-14 font-weight-semibold mb-1">{{lang('Ditolak / Kadaluwarsa')}}</p>
                        <h5 class="mb-0">{{ $totalRejected + $totalExpired }}</h5>
                        <small class="text-muted">Rejected: {{ $totalRejected }} / Expired: {{ $totalExpired }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row">
    <div class="col-xl-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{lang('Distribusi Status Request')}}</h4>
            </div>
            <div class="card-body">
                <div id="chartByStatus"></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{lang('Request per Departemen')}}</h4>
            </div>
            <div class="card-body">
                <div id="chartByDepartment"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent + Quick Actions -->
<div class="row">
    <div class="col-xl-8 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{lang('Request Terbaru')}}</h4>
                <a href="{{ route('admin.ga.index') }}" class="btn btn-sm btn-primary ms-auto">{{lang('Semua Request')}}</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-nowrap w-100" id="gaRecentTable">
                        <thead>
                            <tr>
                                <th>No. Request</th>
                                <th>Pemohon</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $ga)
                            <tr>
                                <td><a href="{{ route('admin.ga.show', $ga->id) }}"><span class="badge bg-primary">{{ $ga->request_no }}</span></a></td>
                                <td>{{ $ga->nama_lengkap }}<br><small class="text-muted">{{ $ga->departemen ?? '-' }}</small></td>
                                <td><span class="font-weight-bold">Rp {{ number_format($ga->total_amount, 0, ',', '.') }}</span></td>
                                <td>
                                    @if($ga->status == 'pending_l1')
                                        <span class="badge badge-warning">Pending L1</span>
                                    @elseif($ga->status == 'pending_l2')
                                        <span class="badge badge-orange">Pending L2</span>
                                    @elseif($ga->status == 'approved')
                                        <span class="badge badge-success">Approved</span>
                                    @elseif($ga->status == 'rejected')
                                        <span class="badge badge-danger">Rejected</span>
                                    @elseif($ga->status == 'cancelled')
                                        <span class="badge badge-secondary">Cancelled</span>
                                    @elseif($ga->status == 'expired')
                                        <span class="badge badge-dark">Expired</span>
                                    @endif
                                </td>
                                <td>{{ optional($ga->created_at)->format('d-m-Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.ga.show', $ga->id) }}" class="btn btn-info btn-sm" title="Detail">
                                        <i class="fe fe-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.ga.downloadPdf', $ga->id) }}" class="btn btn-success btn-sm" title="Download PDF">
                                        <i class="fe fe-download"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data GA Request.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">{{lang('Aksi Cepat')}}</h4>
            </div>
            <div class="card-body">
                @if($isSuperadmin || $isCorporate)
                    <div class="mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1 fs-13">{{lang('Per Departemen')}}</h6>
                                <small class="text-muted">Saring daftar request berdasarkan departemen.</small>
                            </div>
                        </div>
                        <div class="btn-group d-flex mt-2" role="group">
                            @forelse($departments->take(4) as $dept)
                                <a href="{{ route('admin.ga.index', ['department' => $dept]) }}" class="btn btn-outline-primary btn-sm">{{ $dept }}</a>
                            @empty
                                <span class="text-muted fs-13">Belum ada departemen.</span>
                            @endforelse
                        </div>
                    </div>
                    <hr>
                @endif
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1 fs-13">{{lang('Menunggu Persetujuan L2')}}</h6>
                            <small class="text-muted">{{ $totalPendingL2 }} request menunggu level 2.</small>
                        </div>
                    </div>
                    <a href="{{ route('admin.ga.index', ['status' => 'pending_l2']) }}" class="btn btn-sm btn-warning mt-2 w-100">
                        <i class="fe fe-loader"></i> {{lang('Lihat Pending L2')}}
                    </a>
                </div>
                <hr>
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1 fs-13">{{lang('Disetujui')}}</h6>
                            <small class="text-muted">{{ $totalApproved }} request telah diapprove.</small>
                        </div>
                    </div>
                    <a href="{{ route('admin.ga.index', ['status' => 'approved']) }}" class="btn btn-sm btn-success mt-2 w-100">
                        <i class="fe fe-check-circle"></i> {{lang('Lihat Approved')}}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>
<script src="{{asset('assets/plugins/apexchart/apexcharts.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#gaRecentTable').DataTable({
            responsive: true,
            ordering: true,
            paging: false,
            info: false,
            order: [[4, 'desc']],
            language: {
                search: "Cari:",
                emptyTable: "Tidak ada data",
                zeroRecords: "Tidak ditemukan data yang sesuai",
            }
        });

        const statusData = @json($chartByStatus);
        const labelMap = {
            pending_l1: 'Pending L1',
            pending_l2: 'Pending L2',
            approved: 'Approved',
            rejected: 'Rejected',
            cancelled: 'Cancelled',
            expired: 'Expired'
        };
        const statusLabels = Object.keys(statusData).map(k => labelMap[k] || k);
        const statusValues = Object.values(statusData);

        if (document.getElementById('chartByStatus')) {
            new ApexCharts(document.getElementById('chartByStatus'), {
                chart: { type: 'donut', height: 320 },
                labels: statusLabels,
                series: statusValues,
                colors: ['#ffc107', '#fd7e14', '#28a745', '#dc3545', '#6c757d', '#343a40'],
                legend: { position: 'bottom' },
                tooltip: { y: { formatter: v => v + ' request' } }
            }).render();
        }

        const deptData = @json($chartByDepartment);
        const deptLabels = Object.keys(deptData);
        const deptValues = Object.values(deptData);

        if (document.getElementById('chartByDepartment')) {
            new ApexCharts(document.getElementById('chartByDepartment'), {
                chart: { type: 'bar', height: 320 },
                series: [{ name: 'Request', data: deptValues }],
                xaxis: { categories: deptLabels.length ? deptLabels : ['-'] },
                plotOptions: { bar: { borderRadius: 4 } },
                colors: ['#1b86c7'],
                dataLabels: { enabled: false }
            }).render();
        }
    });
</script>
@endsection