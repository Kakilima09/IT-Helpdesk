@component('mail::message')
# GA Request Kadaluwarsa

Halo **{{ $request->nama_lengkap }}**,

**GA Request** Anda berstatus **kadaluwarsa** karena tidak ada persetujuan dalam batas waktu yang ditentukan.

**Detail Permintaan:**
- **No. Request:** {{ $request->request_no }}
- **Total:** Rp {{ number_format($request->total_amount, 0, ',', '.') }}
- **Status:** Expired

Jika Anda masih membutuhkan permintaan tersebut, silakan ajukan ulang melalui aplikasi.

Terima kasih.
@endcomponent