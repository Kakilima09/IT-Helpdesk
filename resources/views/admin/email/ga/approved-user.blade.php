@component('mail::message')
# GA Request Disetujui

Halo **{{ $request->nama_lengkap }}**,

Selamat, **GA Request** Anda telah **disetujui**.

**Detail Permintaan:**
- **No. Request:** {{ $request->request_no }}
- **Total:** Rp {{ number_format($request->total_amount, 0, ',', '.') }}
- **Status:** Approved

Dokumen PDF terlampir pada email ini. Anda juga dapat mengunduh kembali dokumen melalui tombol di bawah:

@component('mail::button', ['url' => $downloadUrl, 'color' => 'primary'])
Download PDF
@endcomponent

Terima kasih.
@endcomponent