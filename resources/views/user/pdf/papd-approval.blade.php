<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PAPD - {{ $papdRequest->id }}</title>
    <style>
        @page {
            size: A4;
            margin: 0.35cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9px;
            line-height: 1.12;
            color: #333;
        }
        /* ===== HEADER ===== */
        .header-container {
            width: 100%;
            border-bottom: 1px solid #1b86c7;
            margin-bottom: 2px;
            padding-bottom: 1px;
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
            max-height: 42px;
            width: auto;
        }
        .title-main {
            font-size: 12px;
            font-weight: bold;
            color: #1b86c7;
        }
        .title-sub {
            font-size: 9px;
            font-weight: bold;
            color: #1b86c7;
        }
        .title-sppd {
            font-size: 7px;
            color: #888;
            margin-top: 0;
        }
        /* ===== BADGE ===== */
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 8px;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #28a745; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-danger { background: #dc3545; }
        /* ===== SECTION ===== */
        .section {
            margin-bottom: 2px;
        }
        .section-title {
            background: #f0f5f9;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 9px;
            color: #1b86c7;
            border-left: 2px solid #1b86c7;
            margin-bottom: 1px;
        }
        /* TABEL UNTUK BARIS LABEL-VALUE */
        .detail-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .detail-table td {
            padding: 1px 2px;
            border-bottom: 1px dotted #f0f0f0;
            vertical-align: top;
            overflow-wrap: break-word;
            word-wrap: break-word;
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
            margin-top: 2px;
            font-size: 9px;
            color: #aaa;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 1px;
        }
        .page-break,
        .detail-table tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>

<!-- ===== HEADER ===== -->
<table class="header-table">
    <tr>
        <td style="width:15%;"></td>
        <td class="header-center" style="width:70%;">
            <div class="title-main">Persetujuan Pengajuan Akomodasi</div>
            <div class="title-sub">Perjalanan Dinas (PAPD)</div>
            @if($papdRequest->no_sppd)
                <div class="title-sppd">No. SPPD: {{ $papdRequest->no_sppd }}</div>
            @endif
        </td>
        <td class="header-right" style="width:15%;">
            @if($logo)
                <img src="{{ $logo }}" alt="Logo" class="logo">
            @else
                <div style="font-size:8px; font-weight:bold; color:#1b86c7;">LOGO</div>
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
        <tr><td class="label-col">Jenis Pemohon</td><td class="value-col">{{ ucfirst($papdRequest->jenis_pemohon ?? 'internal') }}</td></tr>
        @if($papdRequest->nama_lengkap)<tr><td class="label-col">Nama Lengkap</td><td class="value-col">{{ $papdRequest->nama_lengkap }}</td></tr>@endif
        @if(($papdRequest->jenis_pemohon ?? 'internal') === 'internal')
            @if($papdRequest->id_karyawan)<tr><td class="label-col">ID Karyawan</td><td class="value-col">{{ $papdRequest->id_karyawan }}</td></tr>@endif
        @endif
        @if($papdRequest->nik_ktp)<tr><td class="label-col">NIK KTP</td><td class="value-col">{{ $papdRequest->nik_ktp }}</td></tr>@endif
        @if($papdRequest->jabatan)<tr><td class="label-col">Jabatan</td><td class="value-col">{{ $papdRequest->jabatan }}</td></tr>@endif
        @if($papdRequest->departemen)<tr><td class="label-col">Departemen</td><td class="value-col">{{ $papdRequest->departemen }}</td></tr>@endif
        @if($papdRequest->atasan_nama)<tr><td class="label-col">Atasan</td><td class="value-col">{{ $papdRequest->atasan_nama }}</td></tr>@endif
        @if($papdRequest->no_hp)<tr><td class="label-col">No HP</td><td class="value-col">{{ $papdRequest->no_hp }}</td></tr>@endif
        @if($papdRequest->email)<tr><td class="label-col">Email</td><td class="value-col">{{ $papdRequest->email }}</td></tr>@endif
        @if($papdRequest->ttl)<tr><td class="label-col">Tanggal Lahir</td><td class="value-col">{{ \Carbon\Carbon::parse($papdRequest->ttl)->format('d-m-Y') }}</td></tr>@endif
    </table>
</div>

<!-- ===== B ===== -->
<div class="section page-break">
    <div class="section-title">B. Perjalanan Dinas</div>
    <table class="detail-table">
        <tr><td class="label-col">Jenis</td><td class="value-col">{{ ucfirst($papdRequest->jenis_perjalanan) }}</td></tr>
        @if($papdRequest->jenis_perjalanan === 'domestik')
            @if($papdRequest->nama_sesuai_ktp)<tr><td class="label-col">Nama Sesuai KTP</td><td class="value-col">{{ $papdRequest->nama_sesuai_ktp }}</td></tr>@endif
        @endif
        @if($papdRequest->jenis_perjalanan === 'internasional')
            @if($papdRequest->no_paspor)<tr><td class="label-col">No Paspor</td><td class="value-col">{{ $papdRequest->no_paspor }}</td></tr>@endif
            @if($papdRequest->exp_date_paspor)<tr><td class="label-col">Exp Paspor</td><td class="value-col">{{ \Carbon\Carbon::parse($papdRequest->exp_date_paspor)->format('d-m-Y') }}</td></tr>@endif
            @if($papdRequest->nama_paspor)<tr><td class="label-col">Nama sesuai Paspor</td><td class="value-col">{{ $papdRequest->nama_paspor }}</td></tr>@endif
        @endif
        <tr><td class="label-col">{{ $papdRequest->jenis_perjalanan === 'domestik' ? 'Kota Tujuan' : 'Negara Tujuan' }}</td><td class="value-col">{{ $papdRequest->kota_tujuan }}</td></tr>
        @if($papdRequest->agenda)<tr><td class="label-col">Agenda</td><td class="value-col">{{ $papdRequest->agenda }}</td></tr>@endif
        <tr><td class="label-col">Role Perjalanan</td><td class="value-col">{{ ucfirst($papdRequest->role_perjalanan ?? 'sendiri') }}</td></tr>
        @if($papdRequest->role_perjalanan === 'team')
            @if($papdRequest->jumlah_anggota_team)<tr><td class="label-col">Jumlah Anggota</td><td class="value-col">{{ $papdRequest->jumlah_anggota_team }} orang</td></tr>@endif
            @if($papdRequest->nama_anggota_team)<tr><td class="label-col">Nama Anggota Team</td><td class="value-col">{!! nl2br(e($papdRequest->nama_anggota_team)) !!}</td></tr>@endif
        @endif
        @if($papdRequest->no_sppd)<tr><td class="label-col">No SPPD</td><td class="value-col">{{ $papdRequest->no_sppd }}</td></tr>@endif
        <tr><td class="label-col">Berangkat</td><td class="value-col">{{ \Carbon\Carbon::parse($papdRequest->tanggal_keberangkatan)->format('d-m-Y') }} {{ $papdRequest->jam_keberangkatan }}</td></tr>
        @if($papdRequest->tanggal_kepulangan)
        <tr><td class="label-col">Kembali</td><td class="value-col">
            {{ \Carbon\Carbon::parse($papdRequest->tanggal_kepulangan)->format('d-m-Y') }}
            {{ $papdRequest->jam_kepulangan ?? '' }}
        </td></tr>
        @endif
        @if($papdRequest->durasi_hari)<tr><td class="label-col">Lama Perjalanan</td><td class="value-col">{{ $papdRequest->durasi_hari }} hari</td></tr>@endif
        @if($papdRequest->pembebanan_biaya)<tr><td class="label-col">Pembebanan Biaya</td><td class="value-col">{{ $papdRequest->pembebanan_biaya }}</td></tr>@endif
    </table>
</div>

<!-- ===== C1 ===== -->
<div class="section page-break">
    <div class="section-title">C1. Transportasi</div>
    @php
        $transportOptions = collect($papdRequest->opsi_transportasi ?? []);
        $optionLabels = [
            'insurance' => 'Insurance',
            'refundable' => 'Refundable',
            'special_baggage' => 'Special Baggage',
            'special_charge' => 'Special Charge',
        ];
        $baggageOptions = $transportOptions->filter(function ($option) {
            return in_array($option, ['special_baggage', 'special_charge']);
        });
        $otherTransportOptions = $transportOptions->reject(function ($option) {
            return in_array($option, ['special_baggage', 'special_charge']);
        });
    @endphp
    <table class="detail-table">
        <tr><td class="label-col">Jenis Tiket</td><td class="value-col">{{ ($papdRequest->jenis_tiket ?? 'sekali_jalan') === 'pulang_pergi' ? 'Pulang Pergi' : 'Sekali Jalan' }}</td></tr>
        <tr><td class="label-col">Moda Berangkat</td><td class="value-col">{{ ucfirst($papdRequest->moda_transportasi) }}</td></tr>
        @if($papdRequest->moda_transportasi_pulang)
        <tr><td class="label-col">Moda Pulang</td><td class="value-col">{{ ucfirst($papdRequest->moda_transportasi_pulang) }}</td></tr>
        @endif
        @if($papdRequest->kelas)<tr><td class="label-col">Kelas</td><td class="value-col">{{ $papdRequest->kelas }}</td></tr>@endif
        @if($papdRequest->rute)<tr><td class="label-col">Rute</td><td class="value-col">{{ $papdRequest->rute }}</td></tr>@endif
        @if($papdRequest->detail_maskapai)<tr><td class="label-col">Nama Operator</td><td class="value-col">{{ $papdRequest->detail_maskapai }}</td></tr>@endif
        @if($papdRequest->no_penerbangan)<tr><td class="label-col">Nomor Perjalanan</td><td class="value-col">{{ $papdRequest->no_penerbangan }}</td></tr>@endif
        <tr><td class="label-col">Bagasi Tambahan</td><td class="value-col">{{ $papdRequest->bagasi_tambahan ? 'Ya' : 'Tidak' }}</td></tr>
        @if($papdRequest->bagasi_tambahan)
            @if($baggageOptions->isNotEmpty())
            <tr><td class="label-col">Opsi Bagasi</td><td class="value-col">{{ $baggageOptions->map(function ($option) use ($optionLabels) { return $optionLabels[$option] ?? ucfirst($option); })->implode(', ') }}</td></tr>
            @endif
        @endif
        <tr><td class="label-col">Transportasi Lokal</td><td class="value-col">{{ $papdRequest->transportasi_lokal ? 'Ya' : 'Tidak' }}</td></tr>
        @if($otherTransportOptions->isNotEmpty())
        <tr><td class="label-col">Opsi Tambahan</td><td class="value-col">{{ $otherTransportOptions->map(function ($option) use ($optionLabels) { return $optionLabels[$option] ?? ucfirst($option); })->implode(', ') }}</td></tr>
        @endif
        @if($papdRequest->moda_transportasi_pulang)
        @if($papdRequest->kelas_pulang)<tr><td class="label-col">Kelas Pulang</td><td class="value-col">{{ $papdRequest->kelas_pulang }}</td></tr>@endif
        @if($papdRequest->rute_pulang)<tr><td class="label-col">Rute Pulang</td><td class="value-col">{{ $papdRequest->rute_pulang }}</td></tr>@endif
        @if($papdRequest->detail_maskapai_pulang)<tr><td class="label-col">Nama Operator Pulang</td><td class="value-col">{{ $papdRequest->detail_maskapai_pulang }}</td></tr>@endif
        @if($papdRequest->no_penerbangan_pulang)<tr><td class="label-col">Nomor Perjalanan Pulang</td><td class="value-col">{{ $papdRequest->no_penerbangan_pulang }}</td></tr>@endif
        <tr><td class="label-col">Bagasi Tambahan Pulang</td><td class="value-col">{{ $papdRequest->bagasi_tambahan_pulang ? 'Ya' : 'Tidak' }}</td></tr>
        <tr><td class="label-col">Transportasi Lokal Pulang</td><td class="value-col">{{ $papdRequest->transportasi_lokal_pulang ? 'Ya' : 'Tidak' }}</td></tr>
        @if(!empty($papdRequest->opsi_transportasi_pulang))
        <tr><td class="label-col">Opsi Tambahan Pulang</td><td class="value-col">{{ collect($papdRequest->opsi_transportasi_pulang)->map(function ($option) use ($optionLabels) { return $optionLabels[$option] ?? ucfirst($option); })->implode(', ') }}</td></tr>
        @endif
        @endif
    </table>
</div>

<!-- ===== C2 ===== -->
<div class="section page-break">
    <div class="section-title">C2. Akomodasi Hotel</div>
    <table class="detail-table">
        <tr><td class="label-col">Reservasi</td><td class="value-col">{{ $papdRequest->hotel_reservasi ? 'Ya' : 'Tidak' }}</td></tr>
        @if($papdRequest->hotel_reservasi)
        @if($papdRequest->nama_hotel)<tr><td class="label-col">Nama Hotel</td><td class="value-col">{{ $papdRequest->nama_hotel }}</td></tr>@endif
        @if($papdRequest->lokasi_hotel)<tr><td class="label-col">Lokasi</td><td class="value-col">{{ $papdRequest->lokasi_hotel }}</td></tr>@endif
        @if($papdRequest->alamat_hotel)<tr><td class="label-col">Alamat</td><td class="value-col">{{ $papdRequest->alamat_hotel }}</td></tr>@endif
        @if($papdRequest->check_in)<tr><td class="label-col">Check-in</td><td class="value-col">{{ \Carbon\Carbon::parse($papdRequest->check_in)->format('d-m-Y') }}</td></tr>@endif
        @if($papdRequest->check_out)<tr><td class="label-col">Check-out</td><td class="value-col">{{ \Carbon\Carbon::parse($papdRequest->check_out)->format('d-m-Y') }}</td></tr>@endif
        @if($papdRequest->jumlah_kamar)<tr><td class="label-col">Jumlah Kamar</td><td class="value-col">{{ $papdRequest->jumlah_kamar }}</td></tr>@endif
        @if($papdRequest->permintaan_khusus)<tr><td class="label-col">Permintaan Khusus</td><td class="value-col">{{ $papdRequest->permintaan_khusus }}</td></tr>@endif
        @endif
    </table>
</div>

<!-- ===== NOTES ===== -->
@if($papdRequest->notes)
@php
    $pdfNotes = preg_replace_callback('/<img\b[^>]*>/i', function ($match) {
        if (preg_match('/\bsrc\s*=\s*(["\'])(.*?)\1/i', $match[0], $source)
            && (stripos($source[2], 'data:image/webp') === 0
                || preg_match('/\.webp(?:[?#]|$)/i', $source[2]))) {
            return '<span>[Gambar WebP tidak dapat ditampilkan di PDF]</span>';
        }

        return $match[0];
    }, $papdRequest->notes);
@endphp
<div class="section page-break">
    <div class="section-title">Catatan</div>
    <table class="detail-table">
        <tr><td class="label-col">Notes</td><td class="value-col">{!! $pdfNotes !!}</td></tr>
    </table>
</div>
@endif

<!-- ===== FOOTER ===== -->
<div class="footer">
    Dicetak pada {{ now()->format('d-m-Y H:i') }} &bull; Dokumen ini adalah bukti persetujuan perjalanan dinas yang dibuat otomatis oleh sistem IT Helpdesk.
</div>

</body>
</html>
