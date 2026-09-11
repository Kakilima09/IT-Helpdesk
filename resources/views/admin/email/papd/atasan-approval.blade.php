@component('mail::message')
# Persetujuan Permintaan Akomodasi Perjalanan Dinas

Yth. Bapak/Ibu **{{ $request->atasan_nama }}**,

Berikut adalah ringkasan permintaan perjalanan dinas dari:

- **Nama Pemohon:** {{ $request->nama_lengkap }}
- **Departemen:** {{ $request->departemen }}
- **Tujuan:** {{ $request->kota_tujuan }}
- **Keberangkatan:** {{ \Carbon\Carbon::parse($request->tanggal_keberangkatan)->format('d-m-Y') }} {{ $request->jam_keberangkatan ?? '' }}
- **Kepulangan:** {{ $request->tanggal_kepulangan ? \Carbon\Carbon::parse($request->tanggal_kepulangan)->format('d-m-Y') : '-' }} {{ $request->jam_kepulangan ?? '' }}
- **Agenda:** {{ Str::limit($request->agenda, 100) }}

---

### 🚆 Transportasi
@if($request->moda_transportasi)
- **Moda:** {{ ucfirst($request->moda_transportasi) }}
@if($request->kelas)
- **Kelas:** {{ $request->kelas }}
@endif
@if($request->rute)
- **Rute:** {{ $request->rute }}
@endif
@if($request->detail_maskapai)
- **Maskapai:** {{ $request->detail_maskapai }}
@endif
@if($request->no_penerbangan)
- **No. Penerbangan:** {{ $request->no_penerbangan }}
@endif
@if($request->bagasi_tambahan !== null)
- **Bagasi Tambahan:** {{ $request->bagasi_tambahan ? 'Ya' : 'Tidak' }}
@endif
@if($request->transportasi_lokal !== null)
- **Transportasi Lokal:** {{ $request->transportasi_lokal ? 'Ya' : 'Tidak' }}
@endif
@else
- **Moda:** Tidak diisi
@endif

---

### 🏨 Akomodasi Hotel
@if($request->hotel_reservasi)
- **Reservasi:** Ya
@if($request->nama_hotel)
- **Nama Hotel:** {{ $request->nama_hotel }}
@endif
@if($request->lokasi_hotel)
- **Lokasi:** {{ $request->lokasi_hotel }}
@endif
@if($request->alamat_hotel)
- **Alamat:** {{ $request->alamat_hotel }}
@endif
@if($request->check_in)
- **Check-in:** {{ \Carbon\Carbon::parse($request->check_in)->format('d-m-Y') }}
@endif
@if($request->check_out)
- **Check-out:** {{ \Carbon\Carbon::parse($request->check_out)->format('d-m-Y') }}
@endif
@if($request->jumlah_kamar)
- **Jumlah Kamar:** {{ $request->jumlah_kamar }}
@endif
@if($request->permintaan_khusus)
- **Permintaan Khusus:** {{ $request->permintaan_khusus }}
@endif
@else
- **Reservasi:** Tidak
@endif

---

Silakan klik salah satu tombol di bawah ini untuk memberikan persetujuan:

@component('mail::button', ['url' => $approveUrl, 'color' => 'success'])
✅ Setuju
@endcomponent

@component('mail::button', ['url' => $rejectUrl, 'color' => 'error'])
❌ Tolak
@endcomponent

Terima kasih.

@endcomponent