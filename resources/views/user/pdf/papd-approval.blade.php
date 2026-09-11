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
            font-size: 11px;
            line-height: 1.2;
            color: #333;
        }
        /* ===== HEADER ===== */
        .header-container {
            width: 100%;
            border-bottom: 2px solid #1b86c7;
            margin-bottom: 4px;
            padding-bottom: 3px;
        }
        .header-table {
            width: 100%;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-center {
            text-align: center;
        }
        .header-right {
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
        /* TABEL UNTUK BARIS LABEL-VALUE */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-table td {
            padding: 1px 2px;
            border-bottom: 1px dotted #f0f0f0;
            vertical-align: top;
        }
        .label-col {
            width: 22%;
            font-weight: 600;
            color: #555;
        }
        .value-col {
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
<table class="header-table">
    <tr>
        <td style="width:15%;"></td>
        <td class="header-center" style="width:70%;">
            <div class="title-main">Persetujuan Permintaan Akomodasi</div>
            <div class="title-sub">Perjalanan Dinas (PAPD)</div>
            <div class="title-sppd">No. SPPD: {{ $papdRequest->no_sppd ?? '-' }}</div>
        </td>
        <td class="header-right" style="width:15%;">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo" class="logo">
            @else
                <div style="font-size:10px; font-weight:bold; color:#1b86c7;">LOGO</div>
            @endif
        </td>
    </tr>
</table>

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
    <table class="detail-table">
        <tr><td class="label-col">Nama Lengkap</td><td class="value-col">{{ $papdRequest->nama_lengkap }}</td></tr>
        <tr><td class="label-col">ID Karyawan</td><td class="value-col">{{ $papdRequest->id_karyawan }}</td></tr>
        <tr><td class="label-col">NIK KTP</td><td class="value-col">{{ $papdRequest->nik_ktp }}</td></tr>
        <tr><td class="label-col">Jabatan</td><td class="value-col">{{ $papdRequest->jabatan }}</td></tr>
        <tr><td class="label-col">Departemen</td><td class="value-col">{{ $papdRequest->departemen }}</td></tr>
        <tr><td class="label-col">Entitas</td><td class="value-col">{{ $papdRequest->entitas ?? '-' }}</td></tr>
        <tr><td class="label-col">Atasan</td><td class="value-col">{{ $papdRequest->atasan_nama }}</td></tr>
        <tr><td class="label-col">No HP</td><td class="value-col">{{ $papdRequest->no_hp }}</td></tr>
        <tr><td class="label-col">Email</td><td class="value-col">{{ $papdRequest->email }}</td></tr>
        <tr><td class="label-col">Tanggal Lahir</td><td class="value-col">{{ \Carbon\Carbon::parse($papdRequest->ttl)->format('d-m-Y') }}</td></tr>
        <tr><td class="label-col">No Paspor</td><td class="value-col">{{ $papdRequest->no_paspor ?? '-' }}</td></tr>
        <tr><td class="label-col">Exp Paspor</td><td class="value-col">{{ $papdRequest->exp_date_paspor ? \Carbon\Carbon::parse($papdRequest->exp_date_paspor)->format('d-m-Y') : '-' }}</td></tr>
    </table>
</div>

<!-- ===== B ===== -->
<div class="section page-break">
    <div class="section-title">B. Perjalanan Dinas</div>
    <table class="detail-table">
        <tr><td class="label-col">Jenis</td><td class="value-col">{{ ucfirst($papdRequest->jenis_perjalanan) }}</td></tr>
        <tr><td class="label-col">Nama Paspor</td><td class="value-col">{{ $papdRequest->nama_paspor ?? '-' }}</td></tr>
        <tr><td class="label-col">Tujuan</td><td class="value-col">{{ $papdRequest->kota_tujuan }}</td></tr>
        <tr><td class="label-col">Agenda</td><td class="value-col">{{ $papdRequest->agenda }}</td></tr>
        <tr><td class="label-col">No SPPD</td><td class="value-col">{{ $papdRequest->no_sppd ?? '-' }}</td></tr>
        <tr><td class="label-col">Berangkat</td><td class="value-col">{{ \Carbon\Carbon::parse($papdRequest->tanggal_keberangkatan)->format('d-m-Y') }} {{ $papdRequest->jam_keberangkatan }}</td></tr>
        <tr><td class="label-col">Kembali</td><td class="value-col">
            {{ $papdRequest->tanggal_kepulangan ? \Carbon\Carbon::parse($papdRequest->tanggal_kepulangan)->format('d-m-Y') : '-' }}
            {{ $papdRequest->jam_kepulangan ?? '' }}
        </td></tr>
        <tr><td class="label-col">Durasi</td><td class="value-col">{{ $papdRequest->durasi_hari ? $papdRequest->durasi_hari . ' hari' : '-' }}</td></tr>
        <tr><td class="label-col">Pembebanan Biaya</td><td class="value-col">{{ $papdRequest->pembebanan_biaya ?? '-' }}</td></tr>
    </table>
</div>

<!-- ===== C1 ===== -->
<div class="section page-break">
    <div class="section-title">C1. Transportasi</div>
    <table class="detail-table">
        <tr><td class="label-col">Moda</td><td class="value-col">{{ ucfirst($papdRequest->moda_transportasi) }}</td></tr>
        <tr><td class="label-col">Kelas</td><td class="value-col">{{ $papdRequest->kelas ?? '-' }}</td></tr>
        <tr><td class="label-col">Rute</td><td class="value-col">{{ $papdRequest->rute ?? '-' }}</td></tr>
        <tr><td class="label-col">Maskapai</td><td class="value-col">{{ $papdRequest->detail_maskapai ?? '-' }}</td></tr>
        <tr><td class="label-col">No Penerbangan</td><td class="value-col">{{ $papdRequest->no_penerbangan ?? '-' }}</td></tr>
        <tr><td class="label-col">Bagasi Tambahan</td><td class="value-col">{{ $papdRequest->bagasi_tambahan ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><td class="label-col">Transportasi Lokal</td><td class="value-col">{{ $papdRequest->transportasi_lokal ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><td class="label-col">Opsi</td><td class="value-col">{{ $papdRequest->opsi_transportasi ? implode(', ', $papdRequest->opsi_transportasi) : '-' }}</td></tr>
    </table>
</div>

<!-- ===== C2 ===== -->
<div class="section page-break">
    <div class="section-title">C2. Akomodasi Hotel</div>
    <table class="detail-table">
        <tr><td class="label-col">Reservasi</td><td class="value-col">{{ $papdRequest->hotel_reservasi ? 'Ya' : 'Tidak' }}</td></tr>
        @if($papdRequest->hotel_reservasi)
        <tr><td class="label-col">Nama Hotel</td><td class="value-col">{{ $papdRequest->nama_hotel ?? '-' }}</td></tr>
        <tr><td class="label-col">Lokasi</td><td class="value-col">{{ $papdRequest->lokasi_hotel ?? '-' }}</td></tr>
        <tr><td class="label-col">Alamat</td><td class="value-col">{{ $papdRequest->alamat_hotel ?? '-' }}</td></tr>
        <tr><td class="label-col">Check-in</td><td class="value-col">{{ $papdRequest->check_in ? \Carbon\Carbon::parse($papdRequest->check_in)->format('d-m-Y') : '-' }}</td></tr>
        <tr><td class="label-col">Check-out</td><td class="value-col">{{ $papdRequest->check_out ? \Carbon\Carbon::parse($papdRequest->check_out)->format('d-m-Y') : '-' }}</td></tr>
        <tr><td class="label-col">Jumlah Kamar</td><td class="value-col">{{ $papdRequest->jumlah_kamar ?? '-' }}</td></tr>
        <tr><td class="label-col">Permintaan Khusus</td><td class="value-col">{{ $papdRequest->permintaan_khusus ?? '-' }}</td></tr>
        @endif
    </table>
</div>

<!-- ===== NOTES ===== -->
@if($papdRequest->notes)
<div class="section page-break">
    <div class="section-title">Catatan</div>
    <table class="detail-table">
        <tr><td class="label-col">Notes</td><td class="value-col">{!! $papdRequest->notes !!}</td></tr>
    </table>
</div>
@endif

<!-- ===== FOOTER ===== -->
<div class="footer">
    Dicetak pada {{ now()->format('d-m-Y H:i') }} &bull; Dokumen ini adalah bukti persetujuan perjalanan dinas yang dibuat otomatis oleh sistem IT Helpdesk.
</div>

</body>
</html>