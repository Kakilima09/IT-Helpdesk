@component('mail::message')
# Persetujuan GA Request (Level 2)

Halo **{{ $request->l2_approver_name }}**,

Pengajuan **GA Request** berikut telah disetujui di Level 1 dan membutuhkan persetujuan Anda sebagai **GA Manager** (Level 2).

**Detail Permintaan:**
- **No. Request:** {{ $request->request_no }}
- **Pemohon:** {{ $request->nama_lengkap }}
- **Departemen:** {{ $request->departemen ?? '-' }}
- **Total:** Rp {{ number_format($request->total_amount, 0, ',', '.') }}
- **Nilai Tertinggi Barang:** Rp {{ number_format($request->max_goods_price, 0, ',', '.') }}

Dokumen PDF terlampir pada email ini.

Silakan gunakan tombol berikut untuk mengambil keputusan:

@component('mail::button', ['url' => $approveUrl, 'color' => 'success'])
Setujui
@endcomponent

@component('mail::button', ['url' => $rejectUrl, 'color' => 'error'])
Tolak
@endcomponent

Terima kasih.
@endcomponent