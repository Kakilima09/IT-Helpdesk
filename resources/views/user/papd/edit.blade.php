@extends('layouts.papdmaster')

@section('styles')
<!-- INTERNAL Summernote css -->
<link rel="stylesheet" href="{{asset('assets/plugins/summernote/summernote.css')}}?v=<?php echo time(); ?>">
<!-- Select2 -->
<link rel="stylesheet" href="{{asset('assets/plugins/select2/css/select2.min.css')}}?v=<?php echo time(); ?>">
<style>
    .section-title {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-weight: 600;
        border-left: 4px solid #1b86c7;
    }
    .form-group .row { margin-bottom: 10px; }
    .form-label { font-weight: 500; }
    .text-red { color: red; }
    .card-body { padding: 25px; }
    .toggle-hotel { transition: all 0.3s ease; }
</style>
@endsection

@section('content')

<!-- Section Banner -->
<section>
    <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="mb-0">Edit Pengajuan Akomodasi Perjalanan Dinas</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="{{route('papd.index')}}" class="text-white-50">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ route('papd.edit', ['id' => $papdRequest->id]) }}" class="text-white">Edit PAPD</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="cover-image sptb">
        <div class="container">
            <div class="row">
                @include('includes.user.verticalmenupapd')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header border-0">
                            <h4 class="card-title">Edit Form PAPD (Status: <span class="badge badge-warning">Pending</span>)</h4>
                        </div>
                        <form method="POST" action="{{ route('papd.update', $papdRequest->id) }}" id="papdForm">
                            @csrf
                            @method('PUT')
                            @honeypot

                            <div class="card-body">
                                <!-- ==================== BAGIAN A ==================== -->
                                <div class="section-title">A. Informasi Pemohon</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Jenis Pemohon</label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control" value="{{ ucfirst($papdRequest->jenis_pemohon ?? 'internal') }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Nama Lengkap <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                                   name="nama_lengkap" value="{{ old('nama_lengkap', $papdRequest->nama_lengkap) }}" required>
                                            @error('nama_lengkap')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">ID Karyawan @if($papdRequest->jenis_pemohon !== 'eksternal')<span class="text-red">*</span>@endif</label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('id_karyawan') is-invalid @enderror"
                                                   name="id_karyawan" value="{{ old('id_karyawan', $papdRequest->id_karyawan) }}" {{ $papdRequest->jenis_pemohon === 'eksternal' ? '' : 'required' }}>
                                            @error('id_karyawan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">NIK KTP <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('nik_ktp') is-invalid @enderror"
                                                   name="nik_ktp" value="{{ old('nik_ktp', $papdRequest->nik_ktp ?? '') }}" maxlength="20">
                                            @error('nik_ktp')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Jabatan <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror"
                                                   name="jabatan" value="{{ old('jabatan', $papdRequest->jabatan) }}" required>
                                            @error('jabatan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Nama Perusahaan <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('departemen') is-invalid @enderror"
                                                   name="departemen" value="{{ old('departemen', $papdRequest->departemen) }}" required>
                                            @error('departemen')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Atasan Langsung <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('atasan_nama') is-invalid @enderror"
                                                   name="atasan_nama" value="{{ old('atasan_nama', $papdRequest->atasan_nama) }}" required>
                                            @error('atasan_nama')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Email Atasan <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control @error('atasan_email') is-invalid @enderror"
                                                   name="atasan_email" value="{{ old('atasan_email', $papdRequest->atasan_email) }}" required>
                                            <small class="text-muted">Notifikasi persetujuan akan dikirim ke email ini.</small>
                                            @error('atasan_email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">No HP <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror"
                                                   name="no_hp" value="{{ old('no_hp', $papdRequest->no_hp) }}" required>
                                            @error('no_hp')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Email <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   name="email" value="{{ old('email', $papdRequest->email) }}" required>
                                            @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Tanggal Lahir <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="date" class="form-control @error('ttl') is-invalid @enderror"
                                                   name="ttl" value="{{ old('ttl', $papdRequest->ttl->format('Y-m-d')) }}" required>
                                            @error('ttl')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ==================== BAGIAN B ==================== -->
                                <div class="section-title">B. Informasi Perjalanan Dinas</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Jenis Perjalanan <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('jenis_perjalanan') is-invalid @enderror" name="jenis_perjalanan" id="jenis_perjalanan" required>
                                                <option value="domestik" {{ old('jenis_perjalanan', $papdRequest->jenis_perjalanan) == 'domestik' ? 'selected' : '' }}>Domestik</option>
                                                <option value="internasional" {{ old('jenis_perjalanan', $papdRequest->jenis_perjalanan) == 'internasional' ? 'selected' : '' }}>Internasional</option>
                                            </select>
                                            @error('jenis_perjalanan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="passport-details-group" style="{{ old('jenis_perjalanan', $papdRequest->jenis_perjalanan) === 'internasional' ? 'display:block;' : 'display:none;' }}">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">No Paspor</label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('no_paspor') is-invalid @enderror"
                                                   name="no_paspor" value="{{ old('no_paspor', $papdRequest->no_paspor) }}">
                                            @error('no_paspor')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Exp Date Paspor</label></div>
                                        <div class="col-md-9">
                                            <input type="date" class="form-control @error('exp_date_paspor') is-invalid @enderror"
                                                   name="exp_date_paspor" value="{{ old('exp_date_paspor', optional($papdRequest->exp_date_paspor)->format('Y-m-d')) }}">
                                            @error('exp_date_paspor')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>
                                </div>

                                <div class="form-group" id="nama_sesuai_ktp_group" style="{{ old('jenis_perjalanan', $papdRequest->jenis_perjalanan) == 'domestik' ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama Sesuai KTP <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('nama_sesuai_ktp') is-invalid @enderror"
                                                   name="nama_sesuai_ktp" value="{{ old('nama_sesuai_ktp', $papdRequest->nama_sesuai_ktp) }}" maxlength="255">
                                            @error('nama_sesuai_ktp')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="nama_paspor_group" style="{{ old('jenis_perjalanan', $papdRequest->jenis_perjalanan) == 'internasional' ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama sesuai Paspor <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('nama_paspor') is-invalid @enderror"
                                                   name="nama_paspor" value="{{ old('nama_paspor', $papdRequest->nama_paspor ?? '') }}" maxlength="255">
                                            @error('nama_paspor')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                            <small class="text-muted">Diwajibkan untuk perjalanan internasional.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label" id="tujuan-label">{{ old('jenis_perjalanan', $papdRequest->jenis_perjalanan) == 'internasional' ? 'Negara Tujuan' : 'Kota Tujuan' }} <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('kota_tujuan') is-invalid @enderror"
                                                   name="kota_tujuan" value="{{ old('kota_tujuan', $papdRequest->kota_tujuan) }}" required>
                                            @error('kota_tujuan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Agenda <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <textarea class="form-control @error('agenda') is-invalid @enderror" name="agenda" rows="3" required>{{ old('agenda', $papdRequest->agenda) }}</textarea>
                                            @error('agenda')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Role Perjalanan <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="role_perjalanan" id="role_perjalanan_team" value="team" {{ old('role_perjalanan', $papdRequest->role_perjalanan ?? 'sendiri') === 'team' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="role_perjalanan_team">Team</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="role_perjalanan" id="role_perjalanan_sendiri" value="sendiri" {{ old('role_perjalanan', $papdRequest->role_perjalanan ?? 'sendiri') === 'sendiri' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_perjalanan_sendiri">Sendiri</label>
                                            </div>
                                            @error('role_perjalanan')<span class="text-danger d-block"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="nama_anggota_team_group" style="{{ old('role_perjalanan', $papdRequest->role_perjalanan ?? 'sendiri') === 'team' ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Nama Anggota Team <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <textarea class="form-control @error('nama_anggota_team') is-invalid @enderror"
                                                      name="nama_anggota_team" id="nama_anggota_team" rows="3"
                                                      placeholder="Masukkan satu nama per baris">{{ old('nama_anggota_team', $papdRequest->nama_anggota_team) }}</textarea>
                                            @error('nama_anggota_team')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Satu nama anggota per baris.</small>
                                            <div class="mt-2">Jumlah anggota: <strong id="jumlah_anggota_team_display">{{ $papdRequest->jumlah_anggota_team ?? 0 }}</strong> orang</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">No SPPD</label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('no_sppd') is-invalid @enderror"
                                                   name="no_sppd" value="{{ old('no_sppd', $papdRequest->no_sppd) }}">
                                            @error('no_sppd')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Tanggal Keberangkatan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="date" class="form-control @error('tanggal_keberangkatan') is-invalid @enderror"
                                                   name="tanggal_keberangkatan" id="tanggal_keberangkatan"
                                                   value="{{ old('tanggal_keberangkatan', $papdRequest->tanggal_keberangkatan->format('Y-m-d')) }}" required>
                                            @error('tanggal_keberangkatan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <input type="time" class="form-control @error('jam_keberangkatan') is-invalid @enderror"
                                                   name="jam_keberangkatan" value="{{ old('jam_keberangkatan', $papdRequest->jam_keberangkatan) }}" required>
                                            @error('jam_keberangkatan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="return-trip-fields">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Tanggal Kepulangan <span id="return-date-required-marker" class="text-red" style="{{ old('jenis_tiket', $papdRequest->jenis_tiket ?? ($papdRequest->tanggal_kepulangan ? 'pulang_pergi' : 'sekali_jalan')) === 'pulang_pergi' ? '' : 'display:none;' }}">*</span></label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="date" class="form-control @error('tanggal_kepulangan') is-invalid @enderror"
                                                   name="tanggal_kepulangan" id="tanggal_kepulangan"
                                                   value="{{ old('tanggal_kepulangan', optional($papdRequest->tanggal_kepulangan)->format('Y-m-d')) }}" >
                                            @error('tanggal_kepulangan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <input type="time" class="form-control @error('jam_kepulangan') is-invalid @enderror"
                                                   name="jam_kepulangan" value="{{ old('jam_kepulangan', $papdRequest->jam_kepulangan) }}" >
                                            @error('jam_kepulangan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Lama Perjalanan (hari)</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="number" min="1" class="form-control @error('durasi_hari') is-invalid @enderror"
                                                   name="durasi_hari" id="durasi_hari"
                                                   value="{{ old('durasi_hari', $papdRequest->durasi_hari ?? '') }}"
                                                   readonly >
                                            @error('durasi_hari')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Pembebanan Biaya <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('pembebanan_biaya') is-invalid @enderror"
                                                   name="pembebanan_biaya" value="{{ old('pembebanan_biaya', $papdRequest->pembebanan_biaya) }}">
                                            @error('pembebanan_biaya')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ==================== BAGIAN C1 ==================== -->
                                <div class="section-title">C1. Transportasi</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Jenis Tiket <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="jenis_tiket" value="sekali_jalan" {{ old('jenis_tiket', $papdRequest->jenis_tiket ?? ($papdRequest->tanggal_kepulangan ? 'pulang_pergi' : 'sekali_jalan')) === 'sekali_jalan' ? 'checked' : '' }} required>
                                                <span class="form-check-label">Sekali Jalan</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="jenis_tiket" value="pulang_pergi" {{ old('jenis_tiket', $papdRequest->jenis_tiket ?? ($papdRequest->tanggal_kepulangan ? 'pulang_pergi' : 'sekali_jalan')) === 'pulang_pergi' ? 'checked' : '' }} required>
                                                <span class="form-check-label">Pulang Pergi</span>
                                            </label>
                                            @error('jenis_tiket')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">Moda Transportasi <span class="text-red">*</span></label>
                                            <small class="text-muted d-block">Untuk perjalanan berangkat.</small>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('moda_transportasi') is-invalid @enderror" name="moda_transportasi" id="moda_transportasi" required>
                                                <option value="pesawat" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'pesawat' ? 'selected' : '' }}>Pesawat</option>
                                                <option value="kereta" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'kereta' ? 'selected' : '' }}>Kereta</option>
                                                <option value="bus" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'bus' ? 'selected' : '' }}>Bus / Travel</option>
                                                <option value="kapal" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'kapal' ? 'selected' : '' }}>Kapal</option>
                                                <option value="whoosh" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'whoosh' ? 'selected' : '' }}>Whoosh</option>
                                            </select>
                                            @error('moda_transportasi')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Kelas</label></div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('kelas') is-invalid @enderror" name="kelas">
                                                <option value="">Pilih Kelas</option>
                                                <option value="Ekonomi" {{ old('kelas', $papdRequest->kelas) == 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                                                <option value="Bisnis" {{ old('kelas', $papdRequest->kelas) == 'Bisnis' ? 'selected' : '' }}>Bisnis</option>
                                                <option value="Eksekutif" {{ old('kelas', $papdRequest->kelas) == 'Eksekutif' ? 'selected' : '' }}>Eksekutif</option>
                                                <option value="VIP" {{ old('kelas', $papdRequest->kelas) == 'VIP' ? 'selected' : '' }}>VIP</option>
                                            </select>
                                            @error('kelas')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label" id="label_asal">Lokasi Asal</label></div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('kode_asal') is-invalid @enderror" id="kode_asal_select" name="kode_asal"></select>
                                            <input type="hidden" name="kota_asal" id="kota_asal" value="{{ old('kota_asal', $papdRequest->kota_asal) }}">
                                            @error('kode_asal')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Cari berdasarkan nama kota atau kode lokasi (contoh: Jakarta / CGK).</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label" id="label_tujuan">Lokasi Tujuan</label></div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('kode_tujuan') is-invalid @enderror" id="kode_tujuan_select" name="kode_tujuan"></select>
                                            @error('kode_tujuan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Rute</label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('rute') is-invalid @enderror"
                                                   name="rute" id="rute" value="{{ old('rute', $papdRequest->rute) }}" readonly>
                                            @error('rute')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            <small class="text-muted">Terisi otomatis dari kode asal & tujuan.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Nama Operator & Nomor Perjalanan</label></div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control @error('detail_maskapai') is-invalid @enderror"
                                                   name="detail_maskapai" placeholder="Nama Operator" value="{{ old('detail_maskapai', $papdRequest->detail_maskapai) }}">
                                            @error('detail_maskapai')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control @error('no_penerbangan') is-invalid @enderror"
                                                   name="no_penerbangan" placeholder="Nomor Perjalanan" value="{{ old('no_penerbangan', $papdRequest->no_penerbangan) }}">
                                            @error('no_penerbangan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Bagasi Tambahan</label></div>
                                        <div class="col-md-9">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="bagasi_tambahan" value="1" {{ old('bagasi_tambahan', $papdRequest->bagasi_tambahan) ? 'checked' : '' }}>
                                                <span class="form-check-label">Ya</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="bagasi_tambahan" value="0" {{ !old('bagasi_tambahan', $papdRequest->bagasi_tambahan) ? 'checked' : '' }}>
                                                <span class="form-check-label">Tidak</span>
                                            </label>
                                            @error('bagasi_tambahan')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                            <div id="special-baggage-options" class="mt-2" style="{{ old('bagasi_tambahan', $papdRequest->bagasi_tambahan) ? '' : 'display:none;' }}">
                                                <span class="d-block mb-1">Pilih opsi bagasi:</span>
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" name="opsi_transportasi[]" value="special_baggage" {{ in_array('special_baggage', old('opsi_transportasi', $papdRequest->opsi_transportasi ?? [])) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Special Baggage</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" name="opsi_transportasi[]" value="special_charge" {{ in_array('special_charge', old('opsi_transportasi', $papdRequest->opsi_transportasi ?? [])) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Special Charge</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Transportasi Lokal</label></div>
                                        <div class="col-md-9">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="transportasi_lokal" value="1" {{ old('transportasi_lokal', $papdRequest->transportasi_lokal) ? 'checked' : '' }}>
                                                <span class="form-check-label">Ya</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="transportasi_lokal" value="0" {{ !old('transportasi_lokal', $papdRequest->transportasi_lokal) ? 'checked' : '' }}>
                                                <span class="form-check-label">Tidak</span>
                                            </label>
                                            @error('transportasi_lokal')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Opsi Tambahan</label></div>
                                        <div class="col-md-9">
                                            @php
                                                $selectedOps = old('opsi_transportasi', $papdRequest->opsi_transportasi ?? []);
                                            @endphp
                                            <label class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="opsi_transportasi[]" value="insurance" {{ in_array('insurance', $selectedOps) ? 'checked' : '' }}>
                                                <span class="form-check-label">Insurance</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="opsi_transportasi[]" value="refundable" {{ in_array('refundable', $selectedOps) ? 'checked' : '' }}>
                                                <span class="form-check-label">Refundable</span>
                                            </label>
                                            @error('opsi_transportasi')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="return-transport-group" style="{{ old('jenis_tiket', $papdRequest->jenis_tiket ?? ($papdRequest->tanggal_kepulangan ? 'pulang_pergi' : 'sekali_jalan')) === 'pulang_pergi' ? '' : 'display:none;' }}">
                                    <hr>
                                    <div class="section-title">Transportasi Pulang</div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label">Moda Transportasi Pulang <span class="text-red">*</span></label>
                                            </div>
                                            <div class="col-md-9">
                                                <select class="form-control @error('moda_transportasi_pulang') is-invalid @enderror"
                                                        name="moda_transportasi_pulang" id="moda_transportasi_pulang">
                                                    <option value="">Pilih Moda Transportasi</option>
                                                    <option value="pesawat" {{ old('moda_transportasi_pulang', $papdRequest->moda_transportasi_pulang ?? $papdRequest->moda_transportasi) == 'pesawat' ? 'selected' : '' }}>Pesawat</option>
                                                    <option value="kereta" {{ old('moda_transportasi_pulang', $papdRequest->moda_transportasi_pulang ?? $papdRequest->moda_transportasi) == 'kereta' ? 'selected' : '' }}>Kereta</option>
                                                    <option value="bus" {{ old('moda_transportasi_pulang', $papdRequest->moda_transportasi_pulang ?? $papdRequest->moda_transportasi) == 'bus' ? 'selected' : '' }}>Bus / Travel</option>
                                                    <option value="kapal" {{ old('moda_transportasi_pulang', $papdRequest->moda_transportasi_pulang ?? $papdRequest->moda_transportasi) == 'kapal' ? 'selected' : '' }}>Kapal</option>
                                                    <option value="whoosh" {{ old('moda_transportasi_pulang', $papdRequest->moda_transportasi_pulang ?? $papdRequest->moda_transportasi) == 'whoosh' ? 'selected' : '' }}>Whoosh</option>
                                                </select>
                                                @error('moda_transportasi_pulang')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Kelas Pulang</label></div>
                                            <div class="col-md-9">
                                                <select class="form-control @error('kelas_pulang') is-invalid @enderror" name="kelas_pulang">
                                                    <option value="">Pilih Kelas</option>
                                                    <option value="Ekonomi" {{ old('kelas_pulang', $papdRequest->kelas_pulang) == 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                                                    <option value="Bisnis" {{ old('kelas_pulang', $papdRequest->kelas_pulang) == 'Bisnis' ? 'selected' : '' }}>Bisnis</option>
                                                    <option value="Eksekutif" {{ old('kelas_pulang', $papdRequest->kelas_pulang) == 'Eksekutif' ? 'selected' : '' }}>Eksekutif</option>
                                                    <option value="VIP" {{ old('kelas_pulang', $papdRequest->kelas_pulang) == 'VIP' ? 'selected' : '' }}>VIP</option>
                                                </select>
                                                @error('kelas_pulang')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label" id="label_asal_pulang">Lokasi Asal Pulang</label></div>
                                            <div class="col-md-9">
                                                <select class="form-control @error('kode_asal_pulang') is-invalid @enderror" id="kode_asal_pulang_select" name="kode_asal_pulang"></select>
                                                <input type="hidden" name="kota_asal_pulang" id="kota_asal_pulang" value="{{ old('kota_asal_pulang', $papdRequest->kota_asal_pulang) }}">
                                                @error('kode_asal_pulang')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                                <small class="text-muted">Cari berdasarkan nama kota atau kode lokasi (contoh: Jakarta / CGK).</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label" id="label_tujuan_pulang">Lokasi Tujuan Pulang</label></div>
                                            <div class="col-md-9">
                                                <select class="form-control @error('kode_tujuan_pulang') is-invalid @enderror" id="kode_tujuan_pulang_select" name="kode_tujuan_pulang"></select>
                                                @error('kode_tujuan_pulang')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Rute Pulang</label></div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control @error('rute_pulang') is-invalid @enderror"
                                                       name="rute_pulang" id="rute_pulang" value="{{ old('rute_pulang', $papdRequest->rute_pulang) }}" readonly>
                                                @error('rute_pulang')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                                <small class="text-muted">Terisi otomatis dari kode asal & tujuan pulang.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Nama Operator & Nomor Perjalanan Pulang</label></div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control @error('detail_maskapai_pulang') is-invalid @enderror"
                                                       name="detail_maskapai_pulang" placeholder="Nama Operator" value="{{ old('detail_maskapai_pulang', $papdRequest->detail_maskapai_pulang) }}">
                                                @error('detail_maskapai_pulang')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control @error('no_penerbangan_pulang') is-invalid @enderror"
                                                       name="no_penerbangan_pulang" placeholder="Nomor Perjalanan" value="{{ old('no_penerbangan_pulang', $papdRequest->no_penerbangan_pulang) }}">
                                                @error('no_penerbangan_pulang')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Bagasi Tambahan Pulang</label></div>
                                            <div class="col-md-9">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="bagasi_tambahan_pulang" value="1" {{ old('bagasi_tambahan_pulang', $papdRequest->bagasi_tambahan_pulang) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Ya</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="bagasi_tambahan_pulang" value="0" {{ !old('bagasi_tambahan_pulang', $papdRequest->bagasi_tambahan_pulang) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Tidak</span>
                                                </label>
                                                @error('bagasi_tambahan_pulang')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                                <div id="special-baggage-options-pulang" class="mt-2" style="{{ old('bagasi_tambahan_pulang', $papdRequest->bagasi_tambahan_pulang) ? '' : 'display:none;' }}">
                                                    <span class="d-block mb-1">Pilih opsi bagasi pulang:</span>
                                                    <label class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" name="opsi_transportasi_pulang[]" value="special_baggage" {{ in_array('special_baggage', old('opsi_transportasi_pulang', $papdRequest->opsi_transportasi_pulang ?? [])) ? 'checked' : '' }}>
                                                        <span class="form-check-label">Special Baggage</span>
                                                    </label>
                                                    <label class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" name="opsi_transportasi_pulang[]" value="special_charge" {{ in_array('special_charge', old('opsi_transportasi_pulang', $papdRequest->opsi_transportasi_pulang ?? [])) ? 'checked' : '' }}>
                                                        <span class="form-check-label">Special Charge</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Transportasi Lokal Pulang</label></div>
                                            <div class="col-md-9">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="transportasi_lokal_pulang" value="1" {{ old('transportasi_lokal_pulang', $papdRequest->transportasi_lokal_pulang) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Ya</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="transportasi_lokal_pulang" value="0" {{ !old('transportasi_lokal_pulang', $papdRequest->transportasi_lokal_pulang) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Tidak</span>
                                                </label>
                                                @error('transportasi_lokal_pulang')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Opsi Tambahan Pulang</label></div>
                                            <div class="col-md-9">
                                                @php
                                                    $selectedOpsPulang = old('opsi_transportasi_pulang', $papdRequest->opsi_transportasi_pulang ?? []);
                                                @endphp
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" name="opsi_transportasi_pulang[]" value="insurance" {{ in_array('insurance', $selectedOpsPulang) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Insurance</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" name="opsi_transportasi_pulang[]" value="refundable" {{ in_array('refundable', $selectedOpsPulang) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Refundable</span>
                                                </label>
                                                @error('opsi_transportasi_pulang')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ==================== BAGIAN C2 ==================== -->
                                <div class="section-title">C2. Akomodasi Hotel</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Reservasi Hotel</label></div>
                                        <div class="col-md-9">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input hotel-toggle" name="hotel_reservasi" value="1" {{ old('hotel_reservasi', $papdRequest->hotel_reservasi) ? 'checked' : '' }}>
                                                <span class="form-check-label">Ya</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input hotel-toggle" name="hotel_reservasi" value="0" {{ !old('hotel_reservasi', $papdRequest->hotel_reservasi) ? 'checked' : '' }}>
                                                <span class="form-check-label">Tidak</span>
                                            </label>
                                            @error('hotel_reservasi')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="toggle-hotel" id="hotelDetails" style="{{ old('hotel_reservasi', $papdRequest->hotel_reservasi) ? 'display:block;' : 'display:none;' }}">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Nama Hotel</label></div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control @error('nama_hotel') is-invalid @enderror"
                                                       name="nama_hotel" value="{{ old('nama_hotel', $papdRequest->nama_hotel) }}">
                                                @error('nama_hotel')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Lokasi / Alamat</label></div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control @error('lokasi_hotel') is-invalid @enderror"
                                                       name="lokasi_hotel" placeholder="Kota / Wilayah" value="{{ old('lokasi_hotel', $papdRequest->lokasi_hotel) }}">
                                                @error('lokasi_hotel')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                                <textarea class="form-control mt-2 @error('alamat_hotel') is-invalid @enderror"
                                                          name="alamat_hotel" rows="2" placeholder="Alamat lengkap">{{ old('alamat_hotel', $papdRequest->alamat_hotel) }}</textarea>
                                                @error('alamat_hotel')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Check-in / Check-out</label></div>
                                            <div class="col-md-4">
                                                <input type="date" class="form-control @error('check_in') is-invalid @enderror"
                                                       name="check_in" value="{{ old('check_in', optional($papdRequest->check_in)->format('Y-m-d')) }}">
                                                @error('check_in')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <input type="date" class="form-control @error('check_out') is-invalid @enderror"
                                                       name="check_out" value="{{ old('check_out', optional($papdRequest->check_out)->format('Y-m-d')) }}">
                                                @error('check_out')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Jumlah Kamar</label></div>
                                            <div class="col-md-9">
                                                <input type="number" min="1" class="form-control @error('jumlah_kamar') is-invalid @enderror"
                                                       name="jumlah_kamar" value="{{ old('jumlah_kamar', $papdRequest->jumlah_kamar) }}">
                                                @error('jumlah_kamar')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3"><label class="form-label">Permintaan Khusus</label></div>
                                            <div class="col-md-9">
                                                <textarea class="form-control @error('permintaan_khusus') is-invalid @enderror"
                                                          name="permintaan_khusus" rows="2">{{ old('permintaan_khusus', $papdRequest->permintaan_khusus) }}</textarea>
                                                @error('permintaan_khusus')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ==================== NOTES ==================== -->
                                <div class="section-title">Catatan Tambahan</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Notes</label></div>
                                        <div class="col-md-9">
                                            <textarea class="summernote form-control @error('notes') is-invalid @enderror"
                                                      name="notes" rows="4">{{ old('notes', $papdRequest->notes) }}</textarea>
                                            @error('notes')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ==================== SUBMIT ==================== -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-9">
                                            <button type="submit" class="btn btn-primary btn-lg" id="submitPapd">
                                                {{lang('Perbarui Permintaan')}}
                                            </button>
                                            <a href="{{ route('papd.show', $papdRequest->id) }}" class="btn btn-secondary btn-lg">
                                                {{lang('Batal')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- end card-body -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<!-- INTERNAL Summernote js -->
<script src="{{asset('assets/plugins/summernote/summernote.js')}}?v=<?php echo time(); ?>"></script>
<!-- Select2 -->
<script src="{{asset('assets/plugins/select2/js/select2.full.min.js')}}?v=<?php echo time(); ?>"></script>

<script>
    $(document).ready(function() {
        // Inisialisasi Select2 dengan AJAX
        $('#nama_lengkap').select2({
            minimumInputLength: 2,
            placeholder: 'Cari nama karyawan...',
            allowClear: true,
            ajax: {
                url: '{{ route("papd.getUserData") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    // Pastikan data adalah array, jika tidak beri array kosong
                    return {
                        results: data.length ? data : []
                    };
                },
                cache: true
            },
            // Untuk debugging, tambahkan event saat request dimulai
            // (opsional)
            // ajax: { ... },
            // Jika tidak ada hasil, tampilkan pesan
            language: {
                noResults: function() {
                    return 'Karyawan tidak ditemukan';
                },
                searching: function() {
                    return 'Mencari...';
                }
            }
        });

        // Event saat item dipilih
        $('#nama_lengkap').on('select2:select', function(e) {
            var data = e.params.data;
            if (data) {
                $('#id_karyawan').val(data.employee_id || '');
                $('#departemen').val(data.department || '');
                $('#email').val(data.email || '');
            }
        });

        // Event saat input dikosongkan
        $('#nama_lengkap').on('select2:clear', function() {
            $('#id_karyawan').val('');
            $('#departemen').val('');
            $('#email').val('');
        });

        // ===== Lokasi asal/tujuan =====
        function currentLocationType() {
            var moda = $('#moda_transportasi').val();
            if (moda === 'pesawat') return 'airport';
            if (moda === 'kereta' || moda === 'whoosh') return 'train';
            if (moda === 'kapal') return 'port';
            if (moda === 'bus') return 'bus';
            return '';
        }

        function locationLabels() {
            var moda = $('#moda_transportasi').val();
            if (moda === 'pesawat') return { asal: 'Bandara Asal', tujuan: 'Bandara Tujuan', search: 'Cari bandara...' };
            if (moda === 'kereta' || moda === 'whoosh') return { asal: 'Stasiun Asal', tujuan: 'Stasiun Tujuan', search: 'Cari stasiun...' };
            if (moda === 'kapal') return { asal: 'Pelabuhan Asal', tujuan: 'Pelabuhan Tujuan', search: 'Cari pelabuhan...' };
            if (moda === 'bus') return { asal: 'Terminal Asal', tujuan: 'Terminal Tujuan', search: 'Cari terminal...' };
            return { asal: 'Lokasi Asal', tujuan: 'Lokasi Tujuan', search: 'Cari lokasi...' };
        }

        function updateLocationLabels() {
            var label = locationLabels();
            $('#label_asal').text(label.asal);
            $('#label_tujuan').text(label.tujuan);
            $('#kode_asal_select, #kode_tujuan_select').each(function() {
                var instance = $(this).data('select2');
                if (instance) {
                    instance.$container.find('.select2-search__field').attr('placeholder', label.search);
                    instance.$container.find('.select2-selection__placeholder').text(label.search);
                }
            });
        }

        function initLocationSelect(selector) {
            $(selector).select2({
                minimumInputLength: 1,
                placeholder: 'Cari lokasi...',
                allowClear: true,
                ajax: {
                    url: '{{ route("papd.getLocations") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term, type: currentLocationType() };
                    },
                    processResults: function(data) {
                        return { results: data.length ? data : [] };
                    },
                    cache: true
                },
                language: {
                    noResults: function() { return 'Lokasi tidak ditemukan'; },
                    searching: function() { return 'Mencari...'; }
                }
            });
        }

        function locationCity(text) {
            var m = (text || '').match(/^(.*?)\s*\(/);
            return m ? m[1].trim() : '';
        }

        function syncRoute() {
            var originText = $('#kode_asal_select option:selected').text();
            var destText = $('#kode_tujuan_select option:selected').text();
            var originCode = $('#kode_asal_select').val() || '';
            var destCode = $('#kode_tujuan_select').val() || '';
            var originCity = locationCity(originText);
            var destCity = locationCity(destText);

            if (originCity) {
                $('#kota_asal').val(originCity);
            }

            if (originCode && destCode) {
                $('#rute').val(originCity + ' (' + originCode + ') - ' + destCity + ' (' + destCode + ')');
            }
        }

        initLocationSelect('#kode_asal_select');
        initLocationSelect('#kode_tujuan_select');
        updateLocationLabels();

        $('#kode_asal_select').on('select2:select select2:clear', syncRoute);
        $('#kode_tujuan_select').on('select2:select select2:clear', syncRoute);
        $('#moda_transportasi').on('change', function() {
            $('#kode_asal_select').val(null).trigger('change');
            $('#kode_tujuan_select').val(null).trigger('change');
            $('#kota_asal').val('');
            $('#rute').val('');
            updateLocationLabels();
        });

        @if(old('kode_asal', $papdRequest->kode_asal))
            var editOrigin = new Option('{{ old('kota_asal', $papdRequest->kota_asal) }} ({{ old('kode_asal', $papdRequest->kode_asal) }})', '{{ old('kode_asal', $papdRequest->kode_asal) }}', true, true);
            $('#kode_asal_select').append(editOrigin).trigger('change');
        @endif
        @if(old('kode_tujuan', $papdRequest->kode_tujuan))
            var editDest = new Option('{{ $papdRequest->kota_tujuan }} ({{ old('kode_tujuan', $papdRequest->kode_tujuan) }})', '{{ old('kode_tujuan', $papdRequest->kode_tujuan) }}', true, true);
            $('#kode_tujuan_select').append(editDest).trigger('change');
        @endif

        // ===== Lokasi pulang =====
        function currentReturnLocationType() {
            var moda = $('#moda_transportasi_pulang').val();
            if (moda === 'pesawat') return 'airport';
            if (moda === 'kereta' || moda === 'whoosh') return 'train';
            if (moda === 'kapal') return 'port';
            if (moda === 'bus') return 'bus';
            return '';
        }

        function returnLocationLabels() {
            var moda = $('#moda_transportasi_pulang').val();
            if (moda === 'pesawat') return { asal: 'Bandara Asal Pulang', tujuan: 'Bandara Tujuan Pulang', search: 'Cari bandara...' };
            if (moda === 'kereta' || moda === 'whoosh') return { asal: 'Stasiun Asal Pulang', tujuan: 'Stasiun Tujuan Pulang', search: 'Cari stasiun...' };
            if (moda === 'kapal') return { asal: 'Pelabuhan Asal Pulang', tujuan: 'Pelabuhan Tujuan Pulang', search: 'Cari pelabuhan...' };
            if (moda === 'bus') return { asal: 'Terminal Asal Pulang', tujuan: 'Terminal Tujuan Pulang', search: 'Cari terminal...' };
            return { asal: 'Lokasi Asal Pulang', tujuan: 'Lokasi Tujuan Pulang', search: 'Cari lokasi...' };
        }

        function updateReturnLocationLabels() {
            var label = returnLocationLabels();
            $('#label_asal_pulang').text(label.asal);
            $('#label_tujuan_pulang').text(label.tujuan);
            $('#kode_asal_pulang_select, #kode_tujuan_pulang_select').each(function() {
                var instance = $(this).data('select2');
                if (instance) {
                    instance.$container.find('.select2-search__field').attr('placeholder', label.search);
                    instance.$container.find('.select2-selection__placeholder').text(label.search);
                }
            });
        }

        function initReturnLocationSelect(selector) {
            $(selector).select2({
                minimumInputLength: 1,
                placeholder: 'Cari lokasi...',
                allowClear: true,
                ajax: {
                    url: '{{ route("papd.getLocations") }}',
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return { q: params.term, type: currentReturnLocationType() };
                    },
                    processResults: function(data) {
                        return { results: data.length ? data : [] };
                    },
                    cache: true
                },
                language: {
                    noResults: function() { return 'Lokasi tidak ditemukan'; },
                    searching: function() { return 'Mencari...'; }
                }
            });
        }

        function syncReturnRoute() {
            var originText = $('#kode_asal_pulang_select option:selected').text();
            var destText = $('#kode_tujuan_pulang_select option:selected').text();
            var originCode = $('#kode_asal_pulang_select').val() || '';
            var destCode = $('#kode_tujuan_pulang_select').val() || '';
            var originCity = locationCity(originText);
            var destCity = locationCity(destText);

            if (originCity) {
                $('#kota_asal_pulang').val(originCity);
            }

            if (originCode && destCode) {
                $('#rute_pulang').val(originCity + ' (' + originCode + ') - ' + destCity + ' (' + destCode + ')');
            }
        }

        initReturnLocationSelect('#kode_asal_pulang_select');
        initReturnLocationSelect('#kode_tujuan_pulang_select');
        updateReturnLocationLabels();

        $('#kode_asal_pulang_select').on('select2:select select2:clear', syncReturnRoute);
        $('#kode_tujuan_pulang_select').on('select2:select select2:clear', syncReturnRoute);
        $('#moda_transportasi_pulang').on('change', function() {
            $('#kode_asal_pulang_select').val(null).trigger('change');
            $('#kode_tujuan_pulang_select').val(null).trigger('change');
            $('#kota_asal_pulang').val('');
            $('#rute_pulang').val('');
            updateReturnLocationLabels();
        });

        @if(old('kode_asal_pulang', $papdRequest->kode_asal_pulang))
            var editReturnOrigin = new Option('{{ old('kota_asal_pulang', $papdRequest->kota_asal_pulang) }} ({{ old('kode_asal_pulang', $papdRequest->kode_asal_pulang) }})', '{{ old('kode_asal_pulang', $papdRequest->kode_asal_pulang) }}', true, true);
            $('#kode_asal_pulang_select').append(editReturnOrigin).trigger('change');
        @endif
        @if(old('kode_tujuan_pulang', $papdRequest->kode_tujuan_pulang))
            var editReturnDest = new Option('{{ old('kode_tujuan_pulang', $papdRequest->kode_tujuan_pulang) }}', '{{ old('kode_tujuan_pulang', $papdRequest->kode_tujuan_pulang) }}', true, true);
            $('#kode_tujuan_pulang_select').append(editReturnDest).trigger('change');
        @endif

        // Jika ada nilai old (setelah error validasi), set nilai awal
        @if(old('nama_lengkap'))
            var oldName = "{{ old('nama_lengkap') }}";
            var oldId = "{{ old('id_karyawan') }}";
            var oldDept = "{{ old('departemen') }}";
            var oldEmail = "{{ old('email') }}";
            // Buat opsi baru dan pilih
            var newOption = new Option(oldName, oldName, true, true);
            $('#nama_lengkap').append(newOption).trigger('change');
            $('#id_karyawan').val(oldId);
            $('#departemen').val(oldDept);
            $('#email').val(oldEmail);
        @endif
        // Inisialisasi Summernote untuk notes
        $('.summernote').summernote({
            placeholder: 'Catatan tambahan (opsional)',
            tabsize: 1,
            height: 150,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link']],
                ['view', ['fullscreen']],
                ['help', ['help']]
            ]
        });

        // Toggle hotel details
        $('.hotel-toggle').on('change', function() {
            if ($(this).val() == '1' && $(this).is(':checked')) {
                $('#hotelDetails').slideDown();
            } else {
                $('#hotelDetails').slideUp();
            }
        });

        // Jika tidak ada pilihan, sembunyikan
        if (!$('input[name="hotel_reservasi"]:checked').length) {
            $('#hotelDetails').hide();
        }

        // Validasi (opsional)
        $('#papdForm').on('submit', function(e) {
            var hotelRes = $('input[name="hotel_reservasi"]:checked').val();
            if (hotelRes == '1') {
                var namaHotel = $('input[name="nama_hotel"]').val();
                if (!namaHotel) {
                    e.preventDefault();
                    toastr.warning('Jika reservasi hotel "Ya", mohon isi Nama Hotel.');
                    return false;
                }
            }
        });

        function updateTripTypeFields() {
            var isInternational = $('#jenis_perjalanan').val() === 'internasional';
            var passportDetails = $('#passport-details-group');
            var passportFields = passportDetails.find('input');

            passportDetails.toggle(isInternational);
            passportFields.prop('disabled', !isInternational);
            if (!isInternational) {
                passportFields.val('');
            }

            if (isInternational) {
                $('#nama_paspor_group').slideDown();
                $('input[name="nama_paspor"]').prop('required', true);
                $('#nama_sesuai_ktp_group').hide();
                $('input[name="nama_sesuai_ktp"]').prop('required', false).prop('disabled', true);
                $('#tujuan-label').html('Negara Tujuan <span class="text-red">*</span>');
            } else {
                $('#nama_paspor_group').slideUp();
                $('input[name="nama_paspor"]').prop('required', false);
                $('#nama_sesuai_ktp_group').show();
                $('input[name="nama_sesuai_ktp"]').prop('required', true).prop('disabled', false);
                $('#tujuan-label').html('Kota Tujuan <span class="text-red">*</span>');
            }
        }

        $('#jenis_perjalanan').on('change', updateTripTypeFields);
        updateTripTypeFields();

        function updateTeamCount() {
            var names = $('#nama_anggota_team').val().split(/\r\n|\r|\n/).filter(function(name) {
                return name.trim() !== '';
            });
            $('#jumlah_anggota_team_display').text(names.length);
        }

        function updateTravelRole() {
            var isTeam = $('input[name="role_perjalanan"]:checked').val() === 'team';
            $('#nama_anggota_team_group').toggle(isTeam);
            $('#nama_anggota_team').prop('required', isTeam).prop('disabled', !isTeam);
            if (!isTeam) {
                $('#nama_anggota_team').val('');
            }
            updateTeamCount();
        }

        $('input[name="role_perjalanan"]').on('change', updateTravelRole);
        $('#nama_anggota_team').on('input', updateTeamCount);
        updateTravelRole();

        var initialData = {
            name: "{{ $papdRequest->nama_lengkap }}",
            employee_id: "{{ $papdRequest->id_karyawan }}",
            department: "{{ $papdRequest->departemen }}",
            email: "{{ $papdRequest->email }}"
        };

        // Buat opsi awal untuk Select2
        if (initialData.name) {
            var newOption = new Option(initialData.name, initialData.name, true, true);
            $('#nama_lengkap').append(newOption).trigger('change');
            $('#id_karyawan').val(initialData.employee_id);
            $('#departemen').val(initialData.departemen);
            $('#email').val(initialData.email);
        }

        function updateTicketTypeFields() {
            var isRoundTrip = $('input[name="jenis_tiket"]:checked').val() === 'pulang_pergi';
            $('#tanggal_kepulangan').prop('required', isRoundTrip);
            $('#return-date-required-marker').toggle(isRoundTrip);
            $('#return-transport-group').toggle(isRoundTrip);
            $('#moda_transportasi_pulang').prop('required', isRoundTrip);
            if (!isRoundTrip) {
                $('#moda_transportasi_pulang').val('');
                $('#kode_asal_pulang_select').val(null).trigger('change');
                $('#kode_tujuan_pulang_select').val(null).trigger('change');
                $('#kota_asal_pulang').val('');
                $('#rute_pulang').val('');
                updateReturnLocationLabels();
            }
            hitungDurasi();
        }

        $('input[name="jenis_tiket"]').on('change', updateTicketTypeFields);

        function updateSpecialBaggageOptions() {
            var baggageRequested = $('input[name="bagasi_tambahan"]:checked').val() === '1';
            var options = $('#special-baggage-options');

            options.toggle(baggageRequested);
            options.find('input').prop('disabled', !baggageRequested);
            if (!baggageRequested) {
                options.find('input').prop('checked', false);
            }
        }

        $('input[name="bagasi_tambahan"]').on('change', updateSpecialBaggageOptions);
        updateSpecialBaggageOptions();

        function updateReturnSpecialBaggageOptions() {
            var baggageRequested = $('input[name="bagasi_tambahan_pulang"]:checked').val() === '1';
            var options = $('#special-baggage-options-pulang');

            options.toggle(baggageRequested);
            options.find('input').prop('disabled', !baggageRequested);
            if (!baggageRequested) {
                options.find('input').prop('checked', false);
            }
        }

        $('input[name="bagasi_tambahan_pulang"]').on('change', updateReturnSpecialBaggageOptions);
        updateReturnSpecialBaggageOptions();

        updateTicketTypeFields();

        // ===== OTOMATIS DURASI HARI =====
        function hitungDurasi() {
            var tglBerangkat = $('#tanggal_keberangkatan').val();
            var tglKembali = $('#tanggal_kepulangan').val();

            if (tglBerangkat && tglKembali) {
                var start = new Date(tglBerangkat);
                var end = new Date(tglKembali);

                if (end < start) {
                    $('#durasi_hari').val('');
                    toastr.warning('Tanggal kepulangan tidak boleh lebih awal dari tanggal keberangkatan.');
                    return;
                }

                var diffTime = Math.abs(end - start);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                // diffDays = diffDays + 1; // inklusif

                $('#durasi_hari').val(diffDays);
            } else {
                $('#durasi_hari').val('');
            }
        }

        // Event ketika tanggal berangkat atau pulang berubah
        $('#tanggal_keberangkatan, #tanggal_kepulangan').on('change input', hitungDurasi);

        // Panggil saat halaman dimuat (untuk nilai dari database)
        hitungDurasi();
        updateTicketTypeFields();
    });

</script>
@endsection
