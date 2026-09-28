@component('mail::message')
# GA Request Ditolak

Halo **{{ $request->nama_lengkap }}**,

Mohon maaf, **GA Request** Anda **ditolak**.

**Detail Permintaan:**
- **No. Request:** {{ $request->request_no }}
- **Total:** Rp {{ number_format($request->total_amount, 0, ',', '.') }}
- **Status:** Rejected

**Alasan Penolakan:**
{{ $request->reject_reason ?? '-' }}

Jika Anda memiliki pertanyaan, silakan hubungi tim terkait.

Terima kasih.
@endcomponent