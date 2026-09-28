@extends('layouts.adminmasterga')

@section('styles')
@endsection

@section('content')
<div class="page-header d-xl-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title"><span class="font-weight-normal text-muted ms-2">{{ lang('Detail GA Request') }}</span></h4>
    </div>
    <div class="page-rightheader ms-auto">
        <a href="{{ route('admin.ga.index') }}" class="btn btn-light">
            <i class="fe fe-arrow-left"></i> {{ lang('Kembali') }}
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="row">
    <div class="col-xl-8 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">GA Request {{ $gaRequest->request_no }}</h4>
                    @if($gaRequest->status == 'pending_l1')
                        <span class="badge badge-warning">Pending L1</span>
                    @elseif($gaRequest->status == 'pending_l2')
                        <span class="badge badge-orange">Pending L2</span>
                    @elseif($gaRequest->status == 'approved')
                        <span class="badge badge-success">Approved</span>
                    @elseif($gaRequest->status == 'rejected')
                        <span class="badge badge-danger">Rejected</span>
                    @elseif($gaRequest->status == 'cancelled')
                        <span class="badge badge-secondary">Cancelled</span>
                    @elseif($gaRequest->status == 'expired')
                        <span class="badge badge-dark">Expired</span>
                    @endif
                </div>
                <a href="{{ route('admin.ga.downloadPdf', $gaRequest->id) }}" class="btn btn-sm btn-success">
                    <i class="fe fe-download"></i> Download PDF
                </a>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-primary">Data Pemohon</h6>
                        <table class="table table-sm table-borderless">
                            <tr><th class="wd-30">Nama</th><td>: {{ $gaRequest->nama_lengkap }}</td></tr>
                            <tr><th>Jabatan</th><td>: {{ $gaRequest->jabatan }}</td></tr>
                            <tr><th>Departemen</th><td>: {{ $gaRequest->departemen }}</td></tr>
                            <tr><th>Entitas</th><td>: {{ $gaRequest->entitas }}</td></tr>
                            <tr><th>Email</th><td>: {{ $gaRequest->email }}</td></tr>
                            <tr><th>No. HP</th><td>: {{ $gaRequest->no_hp }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary">Alur Persetujuan</h6>
                        <table class="table table-sm table-borderless">
                            <tr><th class="wd-30">Atasan</th><td>: {{ $gaRequest->atasan_nama }}</td></tr>
                            <tr><th>Approver L1</th><td>: {{ $gaRequest->l1_approver_name }}</td></tr>
                            @if($gaRequest->needs_layer2)
                            <tr><th>Approver L2</th><td>: {{ $gaRequest->l2_approver_name }}</td></tr>
                            <tr><th>Nilai Tertinggi Barang</th><td>: Rp {{ number_format($gaRequest->max_goods_price, 0, ',', '.') }}</td></tr>
                            @endif
                            @if($gaRequest->l1_approved_at)
                            <tr><th>Disetujui L1</th><td>: {{ $gaRequest->l1_approved_at->format('d/m/Y H:i') }}</td></tr>
                            @endif
                            @if($gaRequest->l2_approved_at)
                            <tr><th>Disetujui L2</th><td>: {{ $gaRequest->l2_approved_at->format('d/m/Y H:i') }}</td></tr>
                            @endif
                            @if($gaRequest->rejected_at)
                            <tr><th>Ditolak</th><td>: {{ $gaRequest->rejected_at->format('d/m/Y H:i') }}</td></tr>
                            <tr><th>Oleh</th><td>: {{ optional($gaRequest->rejectedBy)->name ?? optional($gaRequest->rejectedBy)->firstname }}</td></tr>
                            @endif
                            @if($gaRequest->reject_reason)
                            <tr><th>Alasan</th><td>: {{ $gaRequest->reject_reason }}</td></tr>
                            @endif
                        </table>
                    </div>
                </div>

                <h6 class="text-primary">Daftar Item</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Nama Item</th>
                                <th>Satuan</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gaRequest->items as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $item->item_type == 'goods' ? 'Barang' : 'Jasa' }}</td>
                                <td>{{ $item->category ? $item->category->name : '-' }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->qty }}</td>
                                <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><th colspan="5" class="text-end">Total Barang</th><th colspan="2" class="text-end">Rp {{ number_format($gaRequest->goods_total, 0, ',', '.') }}</th></tr>
                            <tr><th colspan="5" class="text-end">Total Jasa</th><th colspan="2" class="text-end">Rp {{ number_format($gaRequest->services_total, 0, ',', '.') }}</th></tr>
                            <tr><th colspan="5" class="text-end">Grand Total</th><th colspan="2" class="text-end">Rp {{ number_format($gaRequest->total_amount, 0, ',', '.') }}</th></tr>
                        </tfoot>
                    </table>
                </div>

                @if($gaRequest->notes)
                <div class="mb-3">
                    <h6 class="text-primary">Catatan</h6>
                    <p class="mb-0">{{ $gaRequest->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-12">
        @if($canAct)
            @if($gaRequest->status == 'pending_l1')
            <div class="card">
                <div class="card-header"><h4 class="card-title">{{lang('Aksi L1')}}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ga.approve', $gaRequest->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="fe fe-check"></i> Setujui (L1)
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fe fe-x"></i> Tolak
                    </button>
                </div>
            </div>
            @endif
            @if($gaRequest->status == 'pending_l2')
            <div class="card">
                <div class="card-header"><h4 class="card-title">{{lang('Aksi L2 (GA Manager)')}}</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ga.approve', $gaRequest->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-3">
                            <i class="fe fe-check"></i> Setujui (L2)
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fe fe-x"></i> Tolak
                    </button>
                </div>
            </div>
            @endif
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.ga.reject', $gaRequest->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak GA Request {{ $gaRequest->request_no }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required maxlength="500" placeholder="Masukkan alasan penolakan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection