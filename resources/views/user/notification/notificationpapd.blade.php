@extends('layouts.usermaster')

@section('styles')
<style>
    .notification-detail {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 5px;
        margin: 15px 0;
    }
    .papd-info {
        margin-top: 10px;
    }
    .papd-info table {
        width: 100%;
    }
    .papd-info table td {
        padding: 4px 8px;
        border-bottom: 1px solid #eee;
    }
    .papd-info .label {
        font-weight: 600;
        color: #555;
        width: 30%;
    }
    .action-buttons .btn {
        margin-right: 8px;
    }
</style>
@endsection

@section('content')
<!-- Section -->
<section>
    <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h3 class="mb-0">{{ $notifications->data['mailsubject'] ?? 'Notifikasi' }}</h3>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('papd.index')}}" class="text-white-50">{{lang('Home', 'menu')}}</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="#" class="text-white">{{lang('Notifikasi', 'menu')}}</a>
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
                    <div class="card">
                        <div class="card-header d-block border-0">
                            <h4 class="card-title">
                                {{ $notifications->data['mailsubject'] ?? 'Notifikasi' }}
                                @if(isset($notifications->data['mailsendtag']))
                                    <span class="badge badge-success badge-notify br-13 ms-2 mt-0" style="background-color: {{$notifications->data['mailsendtagcolor'] ?? '#28a745'}}">
                                        {{$notifications->data['mailsendtag']}}
                                    </span>
                                @endif
                            </h4>
                            <div class="mt-2">
                                <span class="badge badge-light">
                                    {{ \Carbon\Carbon::parse($notifications->created_at)->timezone(Auth::guard('customer')->user()->timezone ?? 'Asia/Jakarta')->format(setting('date_format') ?? 'd-m-Y') }}
                                </span>
                                <span class="badge badge-light">
                                    {{ \Carbon\Carbon::parse($notifications->created_at)->timezone(Auth::guard('customer')->user()->timezone ?? 'Asia/Jakarta')->format(setting('time_format') ?? 'H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body pt-1">
                            <div class="notification-content">
                                {!! $notifications->data['mailtext'] ?? 'Tidak ada konten notifikasi.' !!}
                            </div>

                            <!-- ===== Cek apakah notifikasi berisi PAPD ===== -->
                            @php
                                $papd_id = $notifications->data['papd_id'] ?? null;
                                $papdStatus = $notifications->data['papd_status'] ?? null;
                                $papdRequest = null;
                                if ($papd_id) {
                                    try {
                                        $papdRequest = \App\Models\Papd\PapdRequest::find($papd_id);
                                    } catch (\Exception $e) {
                                        // Abaikan jika model tidak ditemukan
                                    }
                                }
                            @endphp

                            @if($papdRequest)
                                <div class="papd-info">
                                    <hr>
                                    <h5 class="mt-3">📋 Detail Permintaan Perjalanan Dinas</h5>
                                    <div class="notification-detail">
                                        <table>
                                            <tr>
                                                <td class="label">No SPPD</td>
                                                <td>{{ $papdRequest->no_sppd ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Pemohon</td>
                                                <td>{{ $papdRequest->nama_lengkap ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Departemen</td>
                                                <td>{{ $papdRequest->departemen ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Tujuan</td>
                                                <td>{{ $papdRequest->kota_tujuan ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="label">Status</td>
                                                <td>
                                                    @if($papdRequest->status == 'pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif($papdRequest->status == 'approved')
                                                        <span class="badge badge-success">Approved</span>
                                                    @else
                                                        <span class="badge badge-danger">Rejected</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <!-- Tombol Aksi -->
                                    <div class="action-buttons mt-3">
                                        @if($papdRequest->status == 'approved')
                                            <a href="{{ route('papd.downloadPdf', $papdRequest->id) }}" class="btn btn-success btn-sm">
                                                <i class="fe fe-download"></i> Download PDF
                                            </a>
                                        @endif
                                        <a href="{{ route('papd.show', $papdRequest->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fe fe-eye"></i> Lihat Detail
                                        </a>
                                        @if($papdRequest->status == 'pending')
                                            <a href="{{ route('papd.edit', $papdRequest->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fe fe-edit"></i> Edit
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- ===== Tombol balik ===== -->
                            <div class="mt-4">
                                <a href="{{ route('papd.index') }}" class="btn btn-secondary">
                                    <i class="fe fe-arrow-left"></i> Kembali ke Dashboard
                                </a>
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
<!-- Jika diperlukan script tambahan -->
@endsection