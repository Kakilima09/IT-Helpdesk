@component('mail::message')
# Data PAPD untuk Pemesanan Tiket/Hotel

Dear,
Corporate

Berikut adalah data lengkap Pengajuan Akomodasi Perjalanan Dinas yang **telah disetujui** oleh atasan. Mohon segera lakukan pemesanan tiket dan akomodasi sesuai data di bawah ini:

---

## A. Informasi Pemohon
- **Nama:** {{ $request->nama_lengkap }}
- **ID Karyawan:** {{ $request->id_karyawan }}
- **Jabatan:** {{ $request->jabatan }}
- **Departemen:** {{ $request->departemen }}
- **Atasan:** {{ $request->atasan_nama }}
- **No HP:** {{ $request->no_hp }}
- **Email:** {{ $request->email }}
- **Tanggal Lahir:** {{ $request->ttl->format('d-m-Y') }}
@if($request->jenis_perjalanan === 'internasional')
- **No Paspor:** {{ $request->no_paspor ?? '-' }}
- **Exp Paspor:** {{ $request->exp_date_paspor ? $request->exp_date_paspor->format('d-m-Y') : '-' }}
@endif

---

## B. Perjalanan Dinas
- **Jenis:** {{ ucfirst($request->jenis_perjalanan) }}
- **{{ $request->jenis_perjalanan === 'domestik' ? 'Kota Tujuan' : 'Negara Tujuan' }}:** {{ $request->kota_tujuan }}
- **Agenda:** {{ $request->agenda }}
- **Role Perjalanan:** {{ ucfirst($request->role_perjalanan ?? 'sendiri') }}
@if($request->role_perjalanan === 'team')
- **Jumlah Anggota:** {{ $request->jumlah_anggota_team ?? 0 }}
- **Nama Anggota Team:** {{ str_replace("\n", ', ', $request->nama_anggota_team ?? '') }}
@endif
- **No SPPD:** {{ $request->no_sppd ?? '-' }}
- **Berangkat:** {{ $request->tanggal_keberangkatan->format('d-m-Y') }} {{ $request->jam_keberangkatan }}
- **Kembali:** {{ $request->tanggal_kepulangan ? \Carbon\Carbon::parse($request->tanggal_kepulangan)->format('d-m-Y') . ' ' . ($request->jam_kepulangan ?? '') : '-' }}
- **Lama Perjalanan:** {{ $request->durasi_hari ? $request->durasi_hari . ' hari' : '-' }}
- **Pembebanan Biaya:** {{ $request->pembebanan_biaya ?? '-' }}

---

## C1. Transportasi
- **Jenis Tiket:** {{ ($request->jenis_tiket ?? 'sekali_jalan') === 'pulang_pergi' ? 'Pulang Pergi' : 'Sekali Jalan' }}
- **Moda Berangkat:** {{ ucfirst($request->moda_transportasi) }}
@if($request->moda_transportasi_pulang)
- **Moda Pulang:** {{ ucfirst($request->moda_transportasi_pulang) }}
@endif
- **Kelas:** {{ $request->kelas ?? '-' }}
- **Rute:** {{ $request->rute ?? '-' }}
- **Nama Operator:** {{ $request->detail_maskapai ?? '-' }}
- **Nomor Perjalanan:** {{ $request->no_penerbangan ?? '-' }}
- **Bagasi Tambahan:** {{ $request->bagasi_tambahan ? 'Ya' : 'Tidak' }}
- **Transportasi Lokal:** {{ $request->transportasi_lokal ? 'Ya' : 'Tidak' }}
- **Opsi:** {{ collect($request->opsi_transportasi ?? [])->map(function ($option) { return ['insurance' => 'Insurance', 'refundable' => 'Refundable', 'special_baggage' => 'Special Baggage', 'special_charge' => 'Special Charge'][$option] ?? ucfirst($option); })->implode(', ') ?: '-' }}

---

## C2. Akomodasi Hotel
- **Reservasi:** {{ $request->hotel_reservasi ? 'Ya' : 'Tidak' }}
@if($request->hotel_reservasi)
- **Nama Hotel:** {{ $request->nama_hotel ?? '-' }}
- **Lokasi / Alamat:** {{ $request->hotel_location_address ?: '-' }}
- **Check-in:** {{ $request->check_in ? $request->check_in->format('d-m-Y') : '-' }}
- **Check-out:** {{ $request->check_out ? $request->check_out->format('d-m-Y') : '-' }}
- **Jumlah Kamar:** {{ $request->jumlah_kamar ?? '-' }}
- **Permintaan Khusus:** {{ $request->permintaan_khusus ?? '-' }}
@endif

---

## Catatan
{!! $request->notes ?? '-' !!}

---

Terima kasih.

@endcomponent
