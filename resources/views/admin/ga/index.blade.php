@extends('layouts.adminmasterga')

@section('styles')
<!-- Data table css -->
<link href="{{asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/datatable/responsive.bootstrap5.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/datatable/buttonbootstrap.min.css')}}" rel="stylesheet" />
@endsection

@section('content')
<!-- Page header -->
<div class="page-header d-xl-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="font-weight-normal text-muted ms-2">{{ lang('GA Requests') }}</span></h4>
    </div>
    <div class="page-rightheader ms-auto">
        <a href="{{ route('admin.ga.dashboard') }}" class="btn btn-light">
            <i class="fe fe-arrow-left"></i> {{ lang('Dashboard') }}
        </a>
        <a href="{{ route('admin.ga.categories.index') }}" class="btn btn-info">
            <i class="fe fe-layers"></i> {{ lang('Kategori Barang') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<!-- Filter dan Export -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{lang('Filter Data')}}</h4>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.ga.index') }}" class="row g-3 align-items-end">
            @if($isSuperadmin || $isCorporate)
                <div class="col-md-2">
                    <label class="form-label">Departemen</label>
                    <select name="department" class="form-select">
                        <option value="">Semua</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="pending_l1" {{ request('status') == 'pending_l1' ? 'selected' : '' }}>Pending L1</option>
                    <option value="pending_l2" {{ request('status') == 'pending_l2' ? 'selected' : '' }}>Pending L2</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.ga.export', request()->query()) }}" class="btn btn-success w-100">
                    <i class="fe fe-download"></i> Export Excel
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabel Data -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">{{lang('Daftar GA Request')}}</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered w-100" id="gaAdminTable">
                <thead>
                    <tr>
                        <th>No. Request</th>
                        <th>Pemohon</th>
                        <th>Departemen</th>
                        <th>Total</th>
                        <th>Tgl Pengajuan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gaRequests as $ga)
                    <tr>
                        <td><a href="{{ route('admin.ga.show', $ga->id) }}"><span class="badge bg-primary">{{ $ga->request_no }}</span></a></td>
                        <td>{{ $ga->nama_lengkap }}<br><small class="text-muted">{{ $ga->email }}</small></td>
                        <td>{{ $ga->departemen ?? '-' }}</td>
                        <td><span class="font-weight-bold">Rp {{ number_format($ga->total_amount, 0, ',', '.') }}</span></td>
                        <td>{{ optional($ga->created_at)->format('d-m-Y H:i') }}</td>
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
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('admin.ga.show', $ga->id) }}" class="btn btn-info btn-sm" title="Detail">
                                    <i class="fe fe-eye"></i>
                                </a>
                                <a href="{{ route('admin.ga.downloadPdf', $ga->id) }}" class="btn btn-success btn-sm" title="Download PDF">
                                    <i class="fe fe-download"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Belum ada data GA Request.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="text-end mt-3">
            {{ $gaRequests->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/js/dataTables.bootstrap5.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('assets/plugins/datatable/responsive.bootstrap5.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#gaAdminTable').DataTable({
            responsive: true,
            ordering: true,
            paging: false,
            info: false,
            order: [[4, 'desc']],
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ data",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                emptyTable: "Tidak ada data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                zeroRecords: "Tidak ditemukan data yang sesuai",
            }
        });
    });
</script>
@endsection