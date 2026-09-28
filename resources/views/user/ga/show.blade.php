@extends('layouts.usermasterga')

@section('styles')
@endsection

@section('content')
<section>
    <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="mb-0">{{lang('Detail GA Request')}}</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('ga.index')}}" class="text-white-50">{{lang('Home')}}</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="#" class="text-white">{{lang('Detail GA Request')}}</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="card-title mb-1">{{lang('Detail GA Request')}}</h3>
                                <span class="badge bg-primary">{{ $gaRequest->request_no }}</span>
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
                            </div>
                            <div>
                                <a href="{{ route('ga.downloadPdf', $gaRequest->id) }}" class="btn btn-sm btn-success"><i class="fe fe-download"></i> PDF</a>
                                @if(in_array($gaRequest->status, ['pending_l1', 'pending_l2']))
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal"><i class="fe fe-x-circle"></i> Batalkan</button>
                                @endif
                            </div>
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
                                        <tr><th>Approver L2 (GA Manager)</th><td>: {{ $gaRequest->l2_approver_name }}</td></tr>
                                        @endif
                                        <tr><th>Status</th><td>: {{ $gaRequest->statusLabel() }}</td></tr>
                                        @if($gaRequest->l1_approved_at)
                                        <tr><th>Disetujui L1</th><td>: {{ $gaRequest->l1_approved_at->format('d/m/Y H:i') }}</td></tr>
                                        @endif
                                        @if($gaRequest->l2_approved_at)
                                        <tr><th>Disetujui L2</th><td>: {{ $gaRequest->l2_approved_at->format('d/m/Y H:i') }}</td></tr>
                                        @endif
                                        @if($gaRequest->rejected_at)
                                        <tr><th>Ditolak</th><td>: {{ $gaRequest->rejected_at->format('d/m/Y H:i') }}</td></tr>
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
            </div>
        </div>
    </div>
</section>

@if(in_array($gaRequest->status, ['pending_l1', 'pending_l2']))
<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batalkan GA Request?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin membatalkan request <strong>{{ $gaRequest->request_no }}</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('ga.cancel', $gaRequest->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
@endsection