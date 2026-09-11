@extends('layouts.adminmasterpapd')

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
        width: 200px;
        display: inline-block;
    }
    .detail-row {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('content')
<!-- Page header -->
<div class="page-header d-xl-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title">
            <span class="font-weight-normal text-muted ms-2">Detail PAPD #{{ $papdRequest->id }}</span>
        </h4>
    </div>
    <div class="page-rightheader ms-md-auto">
        <div class="d-flex align-items-end flex-wrap my-auto end-content breadcrumb-end">
            <a href="{{ route('admin.papd.index') }}" class="btn btn-secondary">
                <i class="fe fe-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('admin.papd.downloadPdf', $papdRequest->id) }}" class="btn btn-success">
                <i class="fe fe-download"></i> Download PDF
            </a>
        </div>
    </div>
</div>

<!-- Detail -->
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Detail Permintaan PAPD</h4>
        <span class="ms-auto badge badge-lg 
            @if($papdRequest->status == 'approved') badge-success
            @elseif($papdRequest->status == 'pending') badge-warning
            @elseif($papdRequest->status == 'rejected') badge-danger
            @elseif($papdRequest->status == 'expired') badge-secondary
            @endif
        ">
            {{ strtoupper($papdRequest->status) }}
        </span>
    </div>
    <div class="card-body">
        <!-- A. Informasi Pemohon -->
        <div class="detail-section">
            <h5 class="mb-3">A. Informasi Pemohon</h5>
            <div class="detail-row"><span class="detail-label">Nama Lengkap</span> {{ $papdRequest->nama_lengkap }}</div>
            <div class="detail-row"><span class="detail-label">ID Karyawan</span> {{ $papdRequest->id_karyawan }}</div>
            <div class="detail-row"><span class="detail-label">NIK KTP</span> {{ $papdRequest->nik_ktp ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Jabatan</span> {{ $papdRequest->jabatan }}</div>
            <div class="detail-row"><span class="detail-label">Departemen</span> {{ $papdRequest->departemen }}</div>
            <div class="detail-row"><span class="detail-label">Entitas</span> {{ $papdRequest->entitas ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Atasan</span> {{ $papdRequest->atasan_nama }}</div>
            <div class="detail-row"><span class="detail-label">Email Atasan</span> {{ $papdRequest->atasan_email }}</div>
            <div class="detail-row"><span class="detail-label">No HP</span> {{ $papdRequest->no_hp }}</div>
            <div class="detail-row"><span class="detail-label">Email</span> {{ $papdRequest->email }}</div>
            <div class="detail-row"><span class="detail-label">Tanggal Lahir</span> {{ \Carbon\Carbon::parse($papdRequest->ttl)->format('d-m-Y') }}</div>
            <div class="detail-row"><span class="detail-label">No Paspor</span> {{ $papdRequest->no_paspor ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Nama Paspor</span> {{ $papdRequest->nama_paspor ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Exp Paspor</span> {{ $papdRequest->exp_date_paspor ? \Carbon\Carbon::parse($papdRequest->exp_date_paspor)->format('d-m-Y') : '-' }}</div>
        </div>

        <!-- B. Perjalanan Dinas -->
        <div class="detail-section">
            <h5 class="mb-3">B. Perjalanan Dinas</h5>
            <div class="detail-row"><span class="detail-label">Jenis</span> {{ ucfirst($papdRequest->jenis_perjalanan) }}</div>
            <div class="detail-row"><span class="detail-label">Tujuan</span> {{ $papdRequest->kota_tujuan }}</div>
            <div class="detail-row"><span class="detail-label">Agenda</span> {{ $papdRequest->agenda }}</div>
            <div class="detail-row"><span class="detail-label">No SPPD</span> {{ $papdRequest->no_sppd ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Keberangkatan</span> {{ \Carbon\Carbon::parse($papdRequest->tanggal_keberangkatan)->format('d-m-Y') }} {{ $papdRequest->jam_keberangkatan }}</div>
            <div class="detail-row">
                <span class="detail-label">Kepulangan</span>
                @if($papdRequest->tanggal_kepulangan)
                    {{ \Carbon\Carbon::parse($papdRequest->tanggal_kepulangan)->format('d-m-Y') }} {{ $papdRequest->jam_kepulangan ?? '' }}
                @else
                    -
                @endif
            </div>
            <div class="detail-row">
                <span class="detail-label">Durasi</span>
                {{ $papdRequest->durasi_hari ? $papdRequest->durasi_hari . ' hari' : '-' }}
            </div>
            <div class="detail-row"><span class="detail-label">Pembebanan Biaya</span> {{ $papdRequest->pembebanan_biaya ?? '-' }}</div>
        </div>

        <!-- C1. Transportasi -->
        <div class="detail-section">
            <h5 class="mb-3">C1. Transportasi</h5>
            <div class="detail-row"><span class="detail-label">Moda</span> {{ ucfirst($papdRequest->moda_transportasi) }}</div>
            <div class="detail-row"><span class="detail-label">Kelas</span> {{ $papdRequest->kelas ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Rute</span> {{ $papdRequest->rute ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Maskapai</span> {{ $papdRequest->detail_maskapai ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">No Penerbangan</span> {{ $papdRequest->no_penerbangan ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Bagasi Tambahan</span> {{ $papdRequest->bagasi_tambahan ? 'Ya' : 'Tidak' }}</div>
            <div class="detail-row"><span class="detail-label">Transportasi Lokal</span> {{ $papdRequest->transportasi_lokal ? 'Ya' : 'Tidak' }}</div>
            <div class="detail-row"><span class="detail-label">Opsi</span> {{ $papdRequest->opsi_transportasi ? implode(', ', $papdRequest->opsi_transportasi) : '-' }}</div>
        </div>

        <!-- C2. Akomodasi Hotel -->
        <div class="detail-section">
            <h5 class="mb-3">C2. Akomodasi Hotel</h5>
            <div class="detail-row"><span class="detail-label">Reservasi</span> {{ $papdRequest->hotel_reservasi ? 'Ya' : 'Tidak' }}</div>
            @if($papdRequest->hotel_reservasi)
            <div class="detail-row"><span class="detail-label">Nama Hotel</span> {{ $papdRequest->nama_hotel ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Lokasi</span> {{ $papdRequest->lokasi_hotel ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Alamat</span> {{ $papdRequest->alamat_hotel ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Check-in</span> {{ $papdRequest->check_in ? \Carbon\Carbon::parse($papdRequest->check_in)->format('d-m-Y') : '-' }}</div>
            <div class="detail-row"><span class="detail-label">Check-out</span> {{ $papdRequest->check_out ? \Carbon\Carbon::parse($papdRequest->check_out)->format('d-m-Y') : '-' }}</div>
            <div class="detail-row"><span class="detail-label">Jumlah Kamar</span> {{ $papdRequest->jumlah_kamar ?? '-' }}</div>
            <div class="detail-row"><span class="detail-label">Permintaan Khusus</span> {{ $papdRequest->permintaan_khusus ?? '-' }}</div>
            @endif
        </div>

        <!-- Notes -->
        @if($papdRequest->notes)
        <div class="detail-section">
            <h5 class="mb-3">Catatan</h5>
            <p>{!! $papdRequest->notes !!}</p>
        </div>
        @endif

        <!-- Info Workflow -->
        <div class="detail-section">
            <h5 class="mb-3">Info Workflow</h5>
            <div class="detail-row"><span class="detail-label">Tanggal Dibuat</span> {{ $papdRequest->created_at->format('d-m-Y H:i') }}</div>
            @if($papdRequest->approved_at)
            <div class="detail-row"><span class="detail-label">Tanggal Approved</span> {{ $papdRequest->approved_at->format('d-m-Y H:i') }}</div>
            @endif
            @if($papdRequest->rejected_at)
            <div class="detail-row"><span class="detail-label">Tanggal Rejected</span> {{ $papdRequest->rejected_at->format('d-m-Y H:i') }}</div>
            @endif
            <div class="detail-row"><span class="detail-label">Closing Status</span> 
                @if($papdRequest->closing_status == 'pending')
                    <span class="badge badge-warning">Pending</span>
                @elseif($papdRequest->closing_status == 'done')
                    <span class="badge badge-success">Done</span>
                @elseif($papdRequest->closing_status == 'cancel')
                    <span class="badge badge-danger">Cancel</span>
                @endif
            </div>
            @if($papdRequest->closing_note)
            <div class="detail-row"><span class="detail-label">Catatan Closing</span> {{ $papdRequest->closing_note }}</div>
            @endif
        </div>
    </div>
</div>
@endsection