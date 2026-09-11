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
                        <h1 class="mb-0">Edit Permintaan PAPD</h1>
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
                                        <div class="col-md-3"><label class="form-label">ID Karyawan <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('id_karyawan') is-invalid @enderror" 
                                                   name="id_karyawan" value="{{ old('id_karyawan', $papdRequest->id_karyawan) }}" required>
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
                                        <div class="col-md-3"><label class="form-label">Departemen <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('departemen') is-invalid @enderror" 
                                                   name="departemen" value="{{ old('departemen', $papdRequest->departemen) }}" required>
                                            @error('departemen')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Entitas</label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('entitas') is-invalid @enderror" 
                                                   name="entitas" value="{{ old('entitas', $papdRequest->entitas) }}">
                                            @error('entitas')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
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

                                <hr>

                                <!-- ==================== BAGIAN B ==================== -->
                                <div class="section-title">B. Informasi Perjalanan Dinas</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Jenis Perjalanan <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('jenis_perjalanan') is-invalid @enderror" name="jenis_perjalanan" required>
                                                <option value="domestik" {{ old('jenis_perjalanan', $papdRequest->jenis_perjalanan) == 'domestik' ? 'selected' : '' }}>Domestik</option>
                                                <option value="internasional" {{ old('jenis_perjalanan', $papdRequest->jenis_perjalanan) == 'internasional' ? 'selected' : '' }}>Internasional</option>
                                            </select>
                                            @error('jenis_perjalanan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
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
                                        <div class="col-md-3"><label class="form-label">Kota / Negara Tujuan <span class="text-red">*</span></label></div>
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
                                
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Tanggal Kepulangan </label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="date" class="form-control @error('tanggal_kepulangan') is-invalid @enderror" 
                                                   name="tanggal_kepulangan" id="tanggal_kepulangan" 
                                                   value="{{ old('tanggal_kepulangan', $papdRequest->tanggal_kepulangan->format('Y-m-d')) }}" >
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

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Durasi (hari) </label>
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
                                        <div class="col-md-3"><label class="form-label">Moda Transportasi <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('moda_transportasi') is-invalid @enderror" name="moda_transportasi" required>
                                                <option value="pesawat" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'pesawat' ? 'selected' : '' }}>Pesawat</option>
                                                <option value="kereta" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'kereta' ? 'selected' : '' }}>Kereta</option>
                                                <option value="bus" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'bus' ? 'selected' : '' }}>Bus / Travel</option>
                                                <option value="kapal" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'kapal' ? 'selected' : '' }}>Kapal</option>
                                                <option value="whoosh" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'whoosh' ? 'selected' : '' }}>Whoosh</option>
                                                <option value="lainnya" {{ old('moda_transportasi', $papdRequest->moda_transportasi) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
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
                                        <div class="col-md-3"><label class="form-label">Rute</label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('rute') is-invalid @enderror" 
                                                   name="rute" value="{{ old('rute', $papdRequest->rute) }}">
                                            @error('rute')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Maskapai & No Penerbangan</label></div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control @error('detail_maskapai') is-invalid @enderror" 
                                                   name="detail_maskapai" placeholder="Maskapai" value="{{ old('detail_maskapai', $papdRequest->detail_maskapai) }}">
                                            @error('detail_maskapai')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control @error('no_penerbangan') is-invalid @enderror" 
                                                   name="no_penerbangan" placeholder="No Penerbangan" value="{{ old('no_penerbangan', $papdRequest->no_penerbangan) }}">
                                            @error('no_penerbangan')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Bagasi Tambahan</label></div>
                                        <div class="col-md-9">
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input" name="bagasi_tambahan" value="1" {{ old('bagasi_tambahan', $papdRequest->bagasi_tambahan) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Ya</span>
                                            </label>
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input" name="bagasi_tambahan" value="0" {{ !old('bagasi_tambahan', $papdRequest->bagasi_tambahan) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Tidak</span>
                                            </label>
                                            @error('bagasi_tambahan')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label">Transportasi Lokal</label></div>
                                        <div class="col-md-9">
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input" name="transportasi_lokal" value="1" {{ old('transportasi_lokal', $papdRequest->transportasi_lokal) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Ya</span>
                                            </label>
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input" name="transportasi_lokal" value="0" {{ !old('transportasi_lokal', $papdRequest->transportasi_lokal) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Tidak</span>
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
                                            <label class="custom-control custom-checkbox d-inline-block me-3">
                                                <input type="checkbox" class="custom-control-input" name="opsi_transportasi[]" value="insurance" {{ in_array('insurance', $selectedOps) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Insurance</span>
                                            </label>
                                            <label class="custom-control custom-checkbox d-inline-block me-3">
                                                <input type="checkbox" class="custom-control-input" name="opsi_transportasi[]" value="refundable" {{ in_array('refundable', $selectedOps) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Refundable</span>
                                            </label>
                                            <label class="custom-control custom-checkbox d-inline-block me-3">
                                                <input type="checkbox" class="custom-control-input" name="opsi_transportasi[]" value="lainnya" {{ in_array('lainnya', $selectedOps) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Lainnya</span>
                                            </label>
                                            @error('opsi_transportasi')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror
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
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input hotel-toggle" name="hotel_reservasi" value="1" {{ old('hotel_reservasi', $papdRequest->hotel_reservasi) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Ya</span>
                                            </label>
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input hotel-toggle" name="hotel_reservasi" value="0" {{ !old('hotel_reservasi', $papdRequest->hotel_reservasi) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Tidak</span>
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

        $('select[name="jenis_perjalanan"]').on('change', function() {
            if ($(this).val() == 'internasional') {
                $('#nama_paspor_group').slideDown();
                $('input[name="nama_paspor"]').prop('required', true);
            } else {
                $('#nama_paspor_group').slideUp();
                $('input[name="nama_paspor"]').prop('required', false);
                $('input[name="nama_paspor"]').val(''); // optional: kosongkan field
            }
        });

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
    });
    
</script>
@endsection