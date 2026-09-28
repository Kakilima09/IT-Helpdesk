<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>GA Request - {{ $request->request_no }}</title>
    <style>
        @page {
            size: A4;
            margin: 0.6cm 0.6cm 0.6cm 0.6cm;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.25;
            color: #333;
        }
        .header-container {
            width: 100%;
            border-bottom: 2px solid #1b86c7;
            margin-bottom: 6px;
            padding-bottom: 4px;
        }
        .header-table { width: 100%; }
        .header-table td { vertical-align: middle; }
        .header-center { text-align: center; }
        .header-right { text-align: right; }
        .logo { max-height: 80px; width: auto; }
        .title-main { font-size: 16px; font-weight: bold; color: #1b86c7; }
        .title-sub { font-size: 10px; color: #666; }

        .section-title {
            background: #f0f5f9;
            padding: 3px 6px;
            font-weight: bold;
            font-size: 10px;
            color: #1b86c7;
            border-left: 3px solid #1b86c7;
            margin: 10px 0 4px 0;
        }
        table.info-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.info-table td { padding: 2px 4px; font-size: 10px; }
        table.info-table td.label { width: 30%; font-weight: bold; }

        table.items { width: 100%; border-collapse: collapse; margin-top: 4px; }
        table.items th {
            background: #1b86c7;
            color: #fff;
            font-size: 9px;
            padding: 4px;
            text-align: left;
        }
        table.items td {
            border: 1px solid #ccc;
            padding: 4px;
            font-size: 9px;
        }
        .text-right { text-align: right; }

        .total-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        .total-table td { padding: 3px 6px; font-size: 10px; }
        .total-table td.label { width: 70%; text-align: right; font-weight: bold; }
        .grand-total { background: #f0f5f9; font-weight: bold; font-size: 11px; }

        .signature-table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        .signature-table td {
            width: 33%;
            text-align: center;
            font-size: 10px;
            padding: 6px;
            vertical-align: top;
        }
        .signature-table .name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }
        .badge-status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 8px;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            background: #1b86c7;
        }
        .footer {
            margin-top: 20px;
            border-top: 1px solid #ccc;
            padding-top: 4px;
            font-size: 8px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="header-container">
        <table class="header-table">
            <tr>
                <td class="header-left" style="width:30%;">
                    @if($logo)
                        <img src="{{ $logo }}" class="logo" alt="Logo">
                    @endif
                </td>
                <td class="header-center" style="width:40%;">
                    <div class="title-main">FORM GA REQUEST</div>
                    <div class="title-sub">General Affairs Request Form</div>
                    <div style="margin-top:3px;">
                        <span class="badge-status">{{ $request->statusLabel() }}</span>
                    </div>
                </td>
                <td class="header-right" style="width:30%;">
                    <div style="font-size:10px;">No : <b>{{ $request->request_no }}</b></div>
                    <div style="font-size:9px;color:#666;">{{ optional($request->created_at)->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section-title">Data Pemohon</div>
    <table class="info-table">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td>: {{ $request->nama_lengkap }}</td>
        </tr>
        <tr>
            <td class="label">Jabatan</td>
            <td>: {{ $request->jabatan }}</td>
        </tr>
        <tr>
            <td class="label">Departemen</td>
            <td>: {{ $request->departemen }}</td>
        </tr>
        <tr>
            <td class="label">Entitas</td>
            <td>: {{ $request->entitas }}</td>
        </tr>
        <tr>
            <td class="label">Email</td>
            <td>: {{ $request->email }}</td>
        </tr>
        <tr>
            <td class="label">No. HP</td>
            <td>: {{ $request->no_hp }}</td>
        </tr>
    </table>

    <div class="section-title">Daftar Permintaan</div>
    <table class="items">
        <thead>
            <tr>
                <th style="width:4%;">No</th>
                <th style="width:10%;">Jenis</th>
                <th style="width:15%;">Kategori</th>
                <th>Nama Item</th>
                <th style="width:6%;">Satuan</th>
                <th style="width:13%;" class="text-right">Harga Satuan</th>
                <th style="width:13%;" class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($request->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->item_type == 'goods' ? 'Barang' : 'Jasa' }}</td>
                <td>{{ $item->category ? $item->category->name : '-' }}</td>
                <td>{{ $item->name }}</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="total-table">
        <tr><td class="label">Total Barang</td><td style="text-align:right;">Rp {{ number_format($request->goods_total, 2, ',', '.') }}</td></tr>
        <tr><td class="label">Total Jasa</td><td style="text-align:right;">Rp {{ number_format($request->services_total, 2, ',', '.') }}</td></tr>
        <tr class="grand-total"><td class="label">Grand Total</td><td style="text-align:right;">Rp {{ number_format($request->total_amount, 2, ',', '.') }}</td></tr>
    </table>

    @if($request->notes)
    <div class="section-title">Catatan</div>
    <p style="font-size:10px;">{{ $request->notes }}</p>
    @endif

    <div class="signature-table">
        <table class="signature-table">
            <tr>
                <td>
                    Pemohon<br>
                    <div class="name">{{ $request->nama_lengkap }}</div>
                </td>
                <td>
                    Disetujui (L1)<br>{{ $request->atasan_nama }}<br>
                    <div class="name">{{ $request->l1_approver_name }}</div>
                </td>
                @if($request->needs_layer2)
                <td>
                    Disetujui (L2)<br>GA Manager<br>
                    <div class="name">{{ $request->l2_approver_name }}</div>
                </td>
                @endif
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini dibuat otomatis oleh sistem IT Helpdesk.
    </div>

</body>
</html>