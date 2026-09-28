@component('mail::message')
# Persetujuan GA Request

Halo **{{ $request->l1_approver_name }}**,

Terdapat pengajuan **GA Request** yang menunggu persetujuan Anda (Level 1).

**Detail Permintaan:**
- **No. Request:** {{ $request->request_no }}
- **Pemohon:** {{ $request->nama_lengkap }}
- **Departemen:** {{ $request->departemen ?? '-' }}
- **Total:** Rp {{ number_format($request->total_amount, 0, ',', '.') }}
@if($request->needs_layer2)
- **Catatan:** Membutuhkan persetujuan Level 2 karena terdapat item barang dengan harga di atas threshold.
@endif

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