@component('mail::message')
# Data PAPD untuk Pemesanan Tiket/Hotel

Dear,
Corporate

Berikut adalah data lengkap permintaan perjalanan dinas yang **telah disetujui** oleh atasan. Mohon segera lakukan pemesanan tiket dan akomodasi sesuai data di bawah ini:

---

## A. Informasi Pemohon
- **Nama:** {{ $request->nama_lengkap }}
- **ID Karyawan:** {{ $request->id_karyawan }}
- **Jabatan:** {{ $request->jabatan }}
- **Departemen:** {{ $request->departemen }}
- **Entitas:** {{ $request->entitas ?? '-' }}
- **Atasan:** {{ $request->atasan_nama }}
- **No HP:** {{ $request->no_hp }}
- **Email:** {{ $request->email }}
- **Tanggal Lahir:** {{ $request->ttl->format('d-m-Y') }}
- **No Paspor:** {{ $request->no_paspor ?? '-' }}
- **Exp Paspor:** {{ $request->exp_date_paspor ? $request->exp_date_paspor->format('d-m-Y') : '-' }}

---

## B. Perjalanan Dinas
- **Jenis:** {{ ucfirst($request->jenis_perjalanan) }}
- **Tujuan:** {{ $request->kota_tujuan }}
- **Agenda:** {{ $request->agenda }}
- **No SPPD:** {{ $request->no_sppd ?? '-' }}
- **Berangkat:** {{ $request->tanggal_keberangkatan->format('d-m-Y') }} {{ $request->jam_keberangkatan }}
- **Kembali:** {{ $request->tanggal_kepulangan ? \Carbon\Carbon::parse($request->tanggal_kepulangan)->format('d-m-Y') . ' ' . ($request->jam_kepulangan ?? '') : '-' }}
- **Durasi:** {{ $request->durasi_hari ? $request->durasi_hari . ' hari' : '-' }}
- **Pembebanan Biaya:** {{ $request->pembebanan_biaya ?? '-' }}

---

## C1. Transportasi
- **Moda:** {{ ucfirst($request->moda_transportasi) }}
- **Kelas:** {{ $request->kelas ?? '-' }}
- **Rute:** {{ $request->rute ?? '-' }}
- **Maskapai:** {{ $request->detail_maskapai ?? '-' }}
- **No Penerbangan:** {{ $request->no_penerbangan ?? '-' }}
- **Bagasi Tambahan:** {{ $request->bagasi_tambahan ? 'Ya' : 'Tidak' }}
- **Transportasi Lokal:** {{ $request->transportasi_lokal ? 'Ya' : 'Tidak' }}
- **Opsi:** {{ $request->opsi_transportasi ? implode(', ', $request->opsi_transportasi) : '-' }}

---

## C2. Akomodasi Hotel
- **Reservasi:** {{ $request->hotel_reservasi ? 'Ya' : 'Tidak' }}
@if($request->hotel_reservasi)
- **Nama Hotel:** {{ $request->nama_hotel ?? '-' }}
- **Lokasi:** {{ $request->lokasi_hotel ?? '-' }}
- **Alamat:** {{ $request->alamat_hotel ?? '-' }}
- **Check-in:** {{ $request->check_in ? $request->check_in->format('d-m-Y') : '-' }}
- **Check-out:** {{ $request->check_out ? $request->check_out->format('d-m-Y') : '-' }}
- **Jumlah Kamar:** {{ $request->jumlah_kamar ?? '-' }}
- **Permintaan Khusus:** {{ $request->permintaan_khusus ?? '-' }}
@endif

---

## Catatan
{!! $request->notes ?? '-' !!}

---

@component('mail::button', ['url' => URL::temporarySignedRoute('papd.downloadPdfSigned', now()->addDays(7), ['id' => $request->id])])
Download PDF Lengkap
@endcomponent

Terima kasih.

@endcomponent