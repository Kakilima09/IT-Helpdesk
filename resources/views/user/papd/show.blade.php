@extends('layouts.papdmaster')

@section('styles')
<style>
    .detail-section {
        background: #f9f9f9;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .detail-label {
        font-weight: 600;
        color: #555;
    }
    .status-badge {
        font-size: 1.1rem;
        padding: 8px 20px;
    }
</style>
@endsection

@section('content')
<section>
    <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="mb-0">Detail Permintaan PAPD</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="{{ route('papd.index') }}" class="text-white-50">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('papd.show', ['id' => $papdRequest->id]) }}" class="text-white">Detail PAPD</a></li>
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
                @include('includes.user.verticalmenupapd')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h4 class="card-title">Permintaan #{{ $papdRequest->id }}</h4>
                            <span class="badge status-badge 
                                @if($papdRequest->status == 'pending') badge-warning
                                @elseif($papdRequest->status == 'approved') badge-success
                                @elseif($papd->status == 'expired') badge-secondary
                                @else badge-danger @endif">
                                {{ ucfirst($papdRequest->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <div class="detail-section">
                                <h5 class="mb-3">A. Informasi Pemohon</h5>
                                <div class="row">
                                    <div class="col-md-6"><span class="detail-label">Nama:</span> {{ $papdRequest->nama_lengkap }}</div>
                                    <div class="col-md-6"><span class="detail-label">ID Karyawan:</span> {{ $papdRequest->id_karyawan }}</div>
                                    <div class="col-md-6"><span class="detail-label">Jabatan:</span> {{ $papdRequest->jabatan }}</div>
                                    <div class="col-md-6"><span class="detail-label">Departemen:</span> {{ $papdRequest->departemen }}</div>
                                    <div class="col-md-6"><span class="detail-label">Entitas:</span> {{ $papdRequest->entitas ?? '-' }}</div>
                                    <div class="col-md-6"><span class="detail-label">Atasan:</span> {{ $papdRequest->atasan_nama }}</div>
                                    <div class="col-md-6"><span class="detail-label">Email Atasan:</span> {{ $papdRequest->atasan_email }}</div>
                                    <div class="col-md-6"><span class="detail-label">No HP:</span> {{ $papdRequest->no_hp }}</div>
                                    <div class="col-md-6"><span class="detail-label">Email:</span> {{ $papdRequest->email }}</div>
                                    <div class="col-md-6"><span class="detail-label">Tanggal Lahir:</span> {{ $papdRequest->ttl->format('d-m-Y') }}</div>
                                    <div class="col-md-6"><span class="detail-label">No Paspor:</span> {{ $papdRequest->no_paspor ?? '-' }}</div>
                                    <div class="col-md-6"><span class="detail-label">Exp Paspor:</span> {{ $papdRequest->exp_date_paspor ? $papdRequest->exp_date_paspor->format('d-m-Y') : '-' }}</div>
                                </div>
                            </div>

                            <div class="detail-section">
                                <h5 class="mb-3">B. Informasi Perjalanan Dinas</h5>
                                <div class="row">
                                    <div class="col-md-6"><span class="detail-label">Jenis:</span> {{ ucfirst($papdRequest->jenis_perjalanan) }}</div>
                                    <div class="col-md-6"><span class="detail-label">Tujuan:</span> {{ $papdRequest->kota_tujuan }}</div>
                                    <div class="col-md-12"><span class="detail-label">Agenda:</span> {{ $papdRequest->agenda }}</div>
                                    <div class="col-md-6"><span class="detail-label">No SPPD:</span> {{ $papdRequest->no_sppd ?? '-' }}</div>
                                    <div class="col-md-6"><span class="detail-label">Durasi:</span> {{ $papdRequest->durasi_hari ? $papdRequest->durasi_hari . ' hari' : '-' }}</div>
                                    <div class="col-md-6"><span class="detail-label">Berangkat:</span> {{ $papdRequest->tanggal_keberangkatan->format('d-m-Y') }} {{ $papdRequest->jam_keberangkatan }}</div>
                                    <div class="col-md-6"><span class="detail-label">Kembali:</span> 
                                        {{ $papdRequest->tanggal_kepulangan ? \Carbon\Carbon::parse($papdRequest->tanggal_kepulangan)->format('d-m-Y') : '-' }}
                                        {{ $papdRequest->jam_kepulangan ?? '' }}
                                    </div>
                                    <div class="col-md-6"><span class="detail-label">Pembebanan Biaya:</span> {{ $papdRequest->pembebanan_biaya ?? '-' }}</div>
                                </div>
                            </div>

                            <div class="detail-section">
                                <h5 class="mb-3">C1. Transportasi</h5>
                                <div class="row">
                                    <div class="col-md-6"><span class="detail-label">Moda:</span> {{ ucfirst($papdRequest->moda_transportasi) }}</div>
                                    <div class="col-md-6"><span class="detail-label">Kelas:</span> {{ $papdRequest->kelas ?? '-' }}</div>
                                    <div class="col-md-6"><span class="detail-label">Rute:</span> {{ $papdRequest->rute ?? '-' }}</div>
                                    <div class="col-md-6"><span class="detail-label">Maskapai:</span> {{ $papdRequest->detail_maskapai ?? '-' }}</div>
                                    <div class="col-md-6"><span class="detail-label">No Penerbangan:</span> {{ $papdRequest->no_penerbangan ?? '-' }}</div>
                                    <div class="col-md-6"><span class="detail-label">Bagasi Tambahan:</span> {{ $papdRequest->bagasi_tambahan ? 'Ya' : 'Tidak' }}</div>
                                    <div class="col-md-6"><span class="detail-label">Transportasi Lokal:</span> {{ $papdRequest->transportasi_lokal ? 'Ya' : 'Tidak' }}</div>
                                    <div class="col-md-12"><span class="detail-label">Opsi:</span> {{ $papdRequest->opsi_transportasi ? implode(', ', $papdRequest->opsi_transportasi) : '-' }}</div>
                                </div>
                            </div>

                            <div class="detail-section">
                                <h5 class="mb-3">C2. Akomodasi Hotel</h5>
                                <div class="row">
                                    <div class="col-md-6"><span class="detail-label">Reservasi:</span> {{ $papdRequest->hotel_reservasi ? 'Ya' : 'Tidak' }}</div>
                                    @if($papdRequest->hotel_reservasi)
                                        <div class="col-md-6"><span class="detail-label">Nama Hotel:</span> {{ $papdRequest->nama_hotel ?? '-' }}</div>
                                        <div class="col-md-6"><span class="detail-label">Lokasi:</span> {{ $papdRequest->lokasi_hotel ?? '-' }}</div>
                                        <div class="col-md-12"><span class="detail-label">Alamat:</span> {{ $papdRequest->alamat_hotel ?? '-' }}</div>
                                        <div class="col-md-6"><span class="detail-label">Check-in:</span> {{ $papdRequest->check_in ? $papdRequest->check_in->format('d-m-Y') : '-' }}</div>
                                        <div class="col-md-6"><span class="detail-label">Check-out:</span> {{ $papdRequest->check_out ? $papdRequest->check_out->format('d-m-Y') : '-' }}</div>
                                        <div class="col-md-6"><span class="detail-label">Jumlah Kamar:</span> {{ $papdRequest->jumlah_kamar ?? '-' }}</div>
                                        <div class="col-md-12"><span class="detail-label">Permintaan Khusus:</span> {{ $papdRequest->permintaan_khusus ?? '-' }}</div>
                                    @endif
                                </div>
                            </div>

                            @if($papdRequest->notes)
                            <div class="detail-section">
                                <h5 class="mb-3">Catatan</h5>
                                <p>{!! $papdRequest->notes !!}</p>
                            </div>
                            @endif

                            <div class="mt-4">
                                @if($papdRequest->status == 'pending')
                                    <a href="{{ route('papd.edit', $papdRequest->id) }}" class="btn btn-primary">Edit</a>
                                @endif
                                <a href="{{ route('papd.index') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection