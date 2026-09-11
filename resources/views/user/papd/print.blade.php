<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PAPD - {{ $papdRequest->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 0.6cm 0.6cm 0.6cm 0.6cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            line-height: 1.2;
            color: #333;
        }
        /* ===== HEADER ===== */
        .header-container {
            display: table;
            width: 100%;
            border-bottom: 2px solid #1b86c7;
            margin-bottom: 4px;
            padding-bottom: 3px;
        }
        .header-left, .header-center, .header-right {
            display: table-cell;
            vertical-align: middle;
        }
        .header-left {
            width: 15%;
        }
        .header-center {
            width: 70%;
            text-align: center;
        }
        .header-right {
            width: 15%;
            text-align: right;
        }
        .logo {
            max-height: 80px;
            width: auto;
        }
        .title-main {
            font-size: 16px;
            font-weight: bold;
            color: #1b86c7;
        }
        .title-sub {
            font-size: 11px;
            font-weight: bold;
            color: #1b86c7;
        }
        .title-sppd {
            font-size: 8px;
            color: #888;
            margin-top: 1px;
        }
        /* ===== BADGE ===== */
        .badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 8px;
            color: #fff;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #28a745; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #dc3545; }
        /* ===== SECTION ===== */
        .section {
            margin-bottom: 3px;
        }
        .section-title {
            background: #f0f5f9;
            padding: 1px 5px;
            font-weight: bold;
            font-size: 8px;
            color: #1b86c7;
            border-left: 3px solid #1b86c7;
            margin-bottom: 2px;
        }
        .row {
            display: flex;
            padding: 1px 0;
            border-bottom: 1px dotted #f0f0f0;
        }
        .label {
            width: 22%;
            font-weight: 600;
            color: #555;
        }
        .value {
            width: 78%;
        }
        .footer {
            margin-top: 6px;
            font-size: 6.5px;
            color: #aaa;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 3px;
        }
        .page-break { page-break-after: avoid; }
    </style>
</head>
<body>

<!-- ===== HEADER ===== -->
<div class="header-container">
    <div class="header-left">
        <!-- Bisa dikosongkan, atau tambahkan info kecil jika diperlukan -->
    </div>
    <div class="header-center">
        <div class="title-main">Permintaan Akomodasi</div>
        <div class="title-sub">Perjalanan Dinas (PAPD)</div>
        <div class="title-sppd">No. SPPD: {{ $papdRequest->no_sppd ?? '-' }}</div>
    </div>
    <div class="header-right">
        @if($logo)
            <img src="{{ $logo }}" alt="Logo" class="logo">
        @else
            <div style="font-size:10px; font-weight:bold; color:#1b86c7;">LOGO</div>
        @endif
    </div>
</div>

<!-- ===== STATUS ===== -->
<div style="text-align:center; margin-bottom:3px;">
    <span class="badge 
        @if($papdRequest->status == 'approved') badge-success
        @elseif($papdRequest->status == 'rejected') badge-danger
        @else badge-warning @endif">
        {{ strtoupper($papdRequest->status) }}
    </span>
</div>

<!-- ===== A ===== -->
<div class="section page-break">
    <div class="section-title">A. Informasi Pemohon</div>
    <div class="row"><div class="label">Nama Lengkap</div><div class="value">{{ $papdRequest->nama_lengkap }}</div></div>
    <div class="row"><div class="label">ID Karyawan</div><div class="value">{{ $papdRequest->id_karyawan }}</div></div>
    <div class="row"><div class="label">Jabatan</div><div class="value">{{ $papdRequest->jabatan }}</div></div>
    <div class="row"><div class="label">Departemen</div><div class="value">{{ $papdRequest->departemen }}</div></div>
    <div class="row"><div class="label">Entitas</div><div class="value">{{ $papdRequest->entitas ?? '-' }}</div></div>
    <div class="row"><div class="label">Atasan</div><div class="value">{{ $papdRequest->atasan_nama }}</div></div>
    <div class="row"><div class="label">No HP</div><div class="value">{{ $papdRequest->no_hp }}</div></div>
    <div class="row"><div class="label">Email</div><div class="value">{{ $papdRequest->email }}</div></div>
    <div class="row"><div class="label">Tanggal Lahir</div><div class="value">{{ \Carbon\Carbon::parse($papdRequest->ttl)->format('d-m-Y') }}</div></div>
    <div class="row"><div class="label">No Paspor</div><div class="value">{{ $papdRequest->no_paspor ?? '-' }}</div></div>
    <div class="row"><div class="label">Exp Paspor</div><div class="value">{{ $papdRequest->exp_date_paspor ? \Carbon\Carbon::parse($papdRequest->exp_date_paspor)->format('d-m-Y') : '-' }}</div></div>
</div>

<!-- ===== B ===== -->
<div class="section page-break">
    <div class="section-title">B. Perjalanan Dinas</div>
    <div class="row"><div class="label">Jenis</div><div class="value">{{ ucfirst($papdRequest->jenis_perjalanan) }}</div></div>
    <div class="row"><div class="label">Tujuan</div><div class="value">{{ $papdRequest->kota_tujuan }}</div></div>
    <div class="row"><div class="label">Agenda</div><div class="value">{{ $papdRequest->agenda }}</div></div>
    <div class="row"><div class="label">No SPPD</div><div class="value">{{ $papdRequest->no_sppd ?? '-' }}</div></div>
    <div class="row"><div class="label">Berangkat</div><div class="value">{{ \Carbon\Carbon::parse($papdRequest->tanggal_keberangkatan)->format('d-m-Y') }} {{ $papdRequest->jam_keberangkatan }}</div></div>
    <div class="row"><div class="label">Kembali</div><div class="value">{{ \Carbon\Carbon::parse($papdRequest->tanggal_kepulangan)->format('d-m-Y') }} {{ $papdRequest->jam_kepulangan }}</div></div>
    <div class="row"><div class="label">Durasi</div><div class="value">{{ $papdRequest->durasi_hari }} hari</div></div>
    <div class="row"><div class="label">Pembebanan Biaya</div><div class="value">{{ $papdRequest->pembebanan_biaya ?? '-' }}</div></div>
</div>

<!-- ===== C1 ===== -->
<div class="section page-break">
    <div class="section-title">C1. Transportasi</div>
    <div class="row"><div class="label">Moda</div><div class="value">{{ ucfirst($papdRequest->moda_transportasi) }}</div></div>
    <div class="row"><div class="label">Kelas</div><div class="value">{{ $papdRequest->kelas ?? '-' }}</div></div>
    <div class="row"><div class="label">Rute</div><div class="value">{{ $papdRequest->rute ?? '-' }}</div></div>
    <div class="row"><div class="label">Maskapai</div><div class="value">{{ $papdRequest->detail_maskapai ?? '-' }}</div></div>
    <div class="row"><div class="label">No Penerbangan</div><div class="value">{{ $papdRequest->no_penerbangan ?? '-' }}</div></div>
    <div class="row"><div class="label">Bagasi Tambahan</div><div class="value">{{ $papdRequest->bagasi_tambahan ? 'Ya' : 'Tidak' }}</div></div>
    <div class="row"><div class="label">Transportasi Lokal</div><div class="value">{{ $papdRequest->transportasi_lokal ? 'Ya' : 'Tidak' }}</div></div>
    <div class="row"><div class="label">Opsi</div><div class="value">{{ $papdRequest->opsi_transportasi ? implode(', ', $papdRequest->opsi_transportasi) : '-' }}</div></div>
</div>

<!-- ===== C2 ===== -->
<div class="section page-break">
    <div class="section-title">C2. Akomodasi Hotel</div>
    <div class="row"><div class="label">Reservasi</div><div class="value">{{ $papdRequest->hotel_reservasi ? 'Ya' : 'Tidak' }}</div></div>
    @if($papdRequest->hotel_reservasi)
    <div class="row"><div class="label">Nama Hotel</div><div class="value">{{ $papdRequest->nama_hotel ?? '-' }}</div></div>
    <div class="row"><div class="label">Lokasi</div><div class="value">{{ $papdRequest->lokasi_hotel ?? '-' }}</div></div>
    <div class="row"><div class="label">Alamat</div><div class="value">{{ $papdRequest->alamat_hotel ?? '-' }}</div></div>
    <div class="row"><div class="label">Check-in</div><div class="value">{{ $papdRequest->check_in ? \Carbon\Carbon::parse($papdRequest->check_in)->format('d-m-Y') : '-' }}</div></div>
    <div class="row"><div class="label">Check-out</div><div class="value">{{ $papdRequest->check_out ? \Carbon\Carbon::parse($papdRequest->check_out)->format('d-m-Y') : '-' }}</div></div>
    <div class="row"><div class="label">Jumlah Kamar</div><div class="value">{{ $papdRequest->jumlah_kamar ?? '-' }}</div></div>
    <div class="row"><div class="label">Permintaan Khusus</div><div class="value">{{ $papdRequest->permintaan_khusus ?? '-' }}</div></div>
    @endif
</div>

<!-- ===== NOTES ===== -->
@if($papdRequest->notes)
<div class="section page-break">
    <div class="section-title">Catatan</div>
    <div class="row"><div class="label">Notes</div><div class="value">{!! $papdRequest->notes !!}</div></div>
</div>
@endif

<!-- ===== FOOTER ===== -->
<div class="footer">
    Dicetak pada {{ now()->format('d-m-Y H:i') }} &bull; Dokumen ini adalah bukti persetujuan perjalanan dinas yang dibuat otomatis oleh sistem IT Helpdesk.
</div>

</body>
</html>