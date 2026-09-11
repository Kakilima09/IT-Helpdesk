@component('mail::message')
# Permintaan Perjalanan Dinas Ditolak

Yth. **{{ $request->nama_lengkap }}**,

Dengan ini kami informasikan bahwa permintaan perjalanan dinas Anda dengan rincian berikut **tidak disetujui** oleh atasan:

- **Tujuan:** {{ $request->kota_tujuan }}
- **Tanggal Berangkat:** {{ \Carbon\Carbon::parse($request->tanggal_keberangkatan)->format('d-m-Y') }}
- **Tanggal Kembali:** {{ $request->tanggal_kepulangan ? \Carbon\Carbon::parse($request->tanggal_kepulangan . ' ' . ($request->jam_kepulangan ?? '00:00'))->format('d-m-Y H:i') : '-' }}

Silakan hubungi atasan Anda untuk informasi lebih lanjut mengenai penolakan ini.

Anda dapat melihat detail permintaan melalui link di bawah ini (harap login terlebih dahulu):

@component('mail::button', ['url' => url('/customer/papd/' . $request->id)])
Lihat Detail Permintaan
@endcomponent

Terima kasih.

@endcomponent