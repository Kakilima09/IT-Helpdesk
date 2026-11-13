@component('mail::message')
# Permintaan Perjalanan Dinas Disetujui

Yth. **{{ $request->nama_lengkap }}**,

PePengajuan perjalanan dinas Anda dengan rincian berikut telah **disetujui** oleh atasan:

- **{{ $request->jenis_perjalanan === 'domestik' ? 'Kota Tujuan' : 'Negara Tujuan' }}:** {{ $request->kota_tujuan }}
- **Tanggal Berangkat:** {{ \Carbon\Carbon::parse($request->tanggal_keberangkatan)->format('d-m-Y H:i') }}
- **Tanggal Kembali:** {{ $request->tanggal_kepulangan ? \Carbon\Carbon::parse($request->tanggal_kepulangan . ' ' . ($request->jam_kepulangan ?? '00:00'))->format('d-m-Y H:i') : '-' }}
- **Agenda:** {{ $request->agenda }}



Terima kasih.
@endcomponent
