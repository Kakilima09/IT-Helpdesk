@component('mail::message')
# Persetujuan Corporate Diperlukan

Dear Corporate,

Permintaan perjalanan dinas berikut **telah disetujui oleh atasan** dan menunggu **persetujuan corporate**. Mohon buka panel admin untuk menyetujui, kemudian sistem akan menampilkan rekomendasi tiket termurah.

---

## Ringkasan Perjalanan
- **Nama:** {{ $request->nama_lengkap }}
- **Departemen:** {{ $request->departemen }}
- **Tujuan:** {{ $request->kota_tujuan }}
- **Rute:** {{ $request->rute ?? '-' }}
- **Berangkat:** {{ $request->tanggal_keberangkatan ? \Carbon\Carbon::parse($request->tanggal_keberangkatan)->format('d-m-Y') : '-' }} {{ $request->jam_keberangkatan }}
- **Kembali:** {{ $request->tanggal_kepulangan ? \Carbon\Carbon::parse($request->tanggal_kepulangan)->format('d-m-Y') : '-' }} {{ $request->jam_kepulangan ?? '' }}
- **Moda:** {{ ucfirst($request->moda_transportasi) }}@if($request->moda_transportasi_pulang) / {{ ucfirst($request->moda_transportasi_pulang) }}@endif
- **Hotel:** {{ $request->hotel_reservasi ? ($request->nama_hotel ?? 'Ya') : 'Tidak' }}

---

@component('mail::button', ['url' => route('admin.papd.show', $request->id), 'color' => 'primary'])
Buka & Approve di Panel
@endcomponent

Terima kasih.

@endcomponent
