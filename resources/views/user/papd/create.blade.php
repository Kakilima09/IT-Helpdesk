@extends('layouts.papdmaster')

@section('styles')
<!-- INTERNAL Summernote css (opsional, jika notes ingin menggunakan editor) -->
<link rel="stylesheet" href="{{asset('assets/plugins/summernote/summernote.css')}}?v=<?php echo time(); ?>">
<!-- Select2 -->
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}">
<style>
    .section-title {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-weight: 600;
        border-left: 4px solid #1b86c7;
    }
    .form-group .row {
        margin-bottom: 10px;
    }
    .form-label {
        font-weight: 500;
    }
    .text-red {
        color: red;
    }
    .card-body {
        padding: 25px;
    }
    .toggle-hotel {
        transition: all 0.3s ease;
    }
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
                        <h1 class="mb-0">{{lang('Permintaan Akomodasi Perjalanan Dinas (PAPD)')}}</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('papd.index')}}" class="text-white-50">{{lang('Home', 'menu')}}</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="#" class="text-white">{{lang('Buat PAPD')}}</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Form -->
<section>
    <div class="cover-image sptb">
        <div class="container">
            <div class="row">
                @include('includes.user.verticalmenupapd')

                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header border-0">
                            <h4 class="card-title">{{lang('Form Permintaan Perjalanan Dinas')}}</h4>
                        </div>
                        <form method="POST" action="{{ route('papd.store') }}" id="papdForm" enctype="multipart/form-data">
                            @csrf
                            @honeypot

                            {{-- @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif --}}
                            <div class="card-body">

                                <!-- ==================== BAGIAN A ==================== -->
                                <div class="section-title">A. Informasi Pemohon</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama Lengkap <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control select2-ajax @error('nama_lengkap') is-invalid @enderror" 
                                                    name="nama_lengkap" id="nama_lengkap" required>
                                                <option value="">{{ lang('Cari nama karyawan...') }}</option>
                                            </select>
                                            @error('nama_lengkap')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                            <small class="text-muted">Ketik minimal 2 huruf untuk mencari karyawan.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">ID Karyawan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('id_karyawan') is-invalid @enderror" 
                                                   name="id_karyawan" id="id_karyawan" value="{{ old('id_karyawan') }}" readonly>
                                            @error('id_karyawan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
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
                                                   name="nik_ktp" value="{{ old('nik_ktp') }}" maxlength="20" placeholder="Masukkan NIK KTP">
                                            @error('nik_ktp')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Jabatan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('jabatan') is-invalid @enderror" 
                                                   name="jabatan" value="{{ old('jabatan') }}" required>
                                            @error('jabatan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Departemen <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('departemen') is-invalid @enderror" 
                                                   name="departemen" id="departemen" value="{{ old('departemen') }}" readonly>
                                            @error('departemen')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Entitas</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('entitas') is-invalid @enderror" 
                                                   name="entitas" value="{{ old('entitas') }}">
                                            @error('entitas')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Atasan Langsung <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('atasan_nama') is-invalid @enderror" 
                                                   name="atasan_nama" value="{{ old('atasan_nama') }}" required>
                                            @error('atasan_nama')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Email Atasan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control @error('atasan_email') is-invalid @enderror" 
                                                   name="atasan_email" value="{{ old('atasan_email') }}" required>
                                            <small class="text-muted">Notifikasi persetujuan akan dikirim ke email ini.</small>
                                            @error('atasan_email')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">No HP <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror" 
                                                   name="no_hp" value="{{ old('no_hp') }}" required>
                                            @error('no_hp')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Email <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   name="email" id="email" value="{{ old('email', auth()->user()->email ?? '') }}" readonly>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Tanggal Lahir <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" class="form-control @error('ttl') is-invalid @enderror" 
                                                   name="ttl" value="{{ old('ttl') }}" required>
                                            @error('ttl')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">No Paspor</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('no_paspor') is-invalid @enderror" 
                                                   name="no_paspor" value="{{ old('no_paspor') }}">
                                            @error('no_paspor')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Exp Date Paspor</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" class="form-control @error('exp_date_paspor') is-invalid @enderror" 
                                                   name="exp_date_paspor" value="{{ old('exp_date_paspor') }}">
                                            @error('exp_date_paspor')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ==================== BAGIAN B ==================== -->
                                <div class="section-title">B. Informasi Perjalanan Dinas</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Jenis Perjalanan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('jenis_perjalanan') is-invalid @enderror" name="jenis_perjalanan" required>
                                                <option value="domestik" {{ old('jenis_perjalanan') == 'domestik' ? 'selected' : '' }}>Domestik</option>
                                                <option value="internasional" {{ old('jenis_perjalanan') == 'internasional' ? 'selected' : '' }}>Internasional</option>
                                            </select>
                                            @error('jenis_perjalanan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="nama_paspor_group" style="{{ old('jenis_perjalanan') == 'internasional' ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama sesuai Paspor <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('nama_paspor') is-invalid @enderror"
                                                   name="nama_paspor" value="{{ old('nama_paspor') }}" maxlength="255" placeholder="Nama sesuai paspor">
                                            @error('nama_paspor')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                            <small class="text-muted">Diwajibkan untuk perjalanan internasional.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Kota / Negara Tujuan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('kota_tujuan') is-invalid @enderror" 
                                                   name="kota_tujuan" value="{{ old('kota_tujuan') }}" required>
                                            @error('kota_tujuan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Agenda <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea class="form-control @error('agenda') is-invalid @enderror" name="agenda" rows="3" required>{{ old('agenda') }}</textarea>
                                            @error('agenda')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">No SPPD</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('no_sppd') is-invalid @enderror" 
                                                   name="no_sppd" value="{{ old('no_sppd') }}">
                                            @error('no_sppd')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
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
                                                   value="{{ old('tanggal_keberangkatan') }}" required>
                                            @error('tanggal_keberangkatan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <input type="time" class="form-control @error('jam_keberangkatan') is-invalid @enderror" 
                                                   name="jam_keberangkatan" value="{{ old('jam_keberangkatan') }}" required>
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
                                                   value="{{ old('tanggal_kepulangan') }}" >
                                            @error('tanggal_kepulangan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <input type="time" class="form-control @error('jam_kepulangan') is-invalid @enderror" 
                                                   name="jam_kepulangan" value="{{ old('jam_kepulangan') }}" >
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
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Pembebanan Biaya <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('pembebanan_biaya') is-invalid @enderror" 
                                                   name="pembebanan_biaya" value="{{ old('pembebanan_biaya') }}">
                                            @error('pembebanan_biaya')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ==================== BAGIAN C1 ==================== -->
                                <div class="section-title">C1. Transportasi</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Moda Transportasi <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('moda_transportasi') is-invalid @enderror" name="moda_transportasi" required>
                                                <option value="pesawat" {{ old('moda_transportasi') == 'pesawat' ? 'selected' : '' }}>Pesawat</option>
                                                <option value="kereta" {{ old('moda_transportasi') == 'kereta' ? 'selected' : '' }}>Kereta</option>
                                                <option value="bus" {{ old('moda_transportasi') == 'bus' ? 'selected' : '' }}>Bus / Travel</option>
                                                <option value="kapal" {{ old('moda_transportasi') == 'kapal' ? 'selected' : '' }}>Kapal</option>
                                                <option value="whoosh" {{ old('moda_transportasi') == 'whoosh' ? 'selected' : '' }}>Whoosh</option>
                                                <option value="lainnya" {{ old('moda_transportasi') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                            @error('moda_transportasi')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Kelas</label>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('kelas') is-invalid @enderror" name="kelas">
                                                <option value="">Pilih Kelas</option>
                                                <option value="Ekonomi" {{ old('kelas') == 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                                                <option value="Bisnis" {{ old('kelas') == 'Bisnis' ? 'selected' : '' }}>Bisnis</option>
                                                <option value="Eksekutif" {{ old('kelas') == 'Eksekutif' ? 'selected' : '' }}>Eksekutif</option>
                                                <option value="VIP" {{ old('kelas') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                            </select>
                                            @error('kelas')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Rute</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('rute') is-invalid @enderror" 
                                                   name="rute" value="{{ old('rute') }}">
                                            @error('rute')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Maskapai & No Penerbangan</label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control @error('detail_maskapai') is-invalid @enderror" 
                                                   name="detail_maskapai" placeholder="Maskapai" value="{{ old('detail_maskapai') }}">
                                            @error('detail_maskapai')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control @error('no_penerbangan') is-invalid @enderror" 
                                                   name="no_penerbangan" placeholder="No Penerbangan" value="{{ old('no_penerbangan') }}">
                                            @error('no_penerbangan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Bagasi Tambahan</label>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input" name="bagasi_tambahan" value="1" {{ old('bagasi_tambahan') == '1' ? 'checked' : '' }}>
                                                <span class="custom-control-label">Ya</span>
                                            </label>
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input" name="bagasi_tambahan" value="0" {{ old('bagasi_tambahan') == '0' ? 'checked' : '' }}>
                                                <span class="custom-control-label">Tidak</span>
                                            </label>
                                            @error('bagasi_tambahan')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Transportasi Lokal</label>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input" name="transportasi_lokal" value="1" {{ old('transportasi_lokal') == '1' ? 'checked' : '' }}>
                                                <span class="custom-control-label">Ya</span>
                                            </label>
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input" name="transportasi_lokal" value="0" {{ old('transportasi_lokal') == '0' ? 'checked' : '' }}>
                                                <span class="custom-control-label">Tidak</span>
                                            </label>
                                            @error('transportasi_lokal')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Opsi Tambahan</label>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="custom-control custom-checkbox d-inline-block me-3">
                                                <input type="checkbox" class="custom-control-input" name="opsi_transportasi[]" value="insurance" {{ in_array('insurance', old('opsi_transportasi', [])) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Insurance</span>
                                            </label>
                                            <label class="custom-control custom-checkbox d-inline-block me-3">
                                                <input type="checkbox" class="custom-control-input" name="opsi_transportasi[]" value="refundable" {{ in_array('refundable', old('opsi_transportasi', [])) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Refundable</span>
                                            </label>
                                            <label class="custom-control custom-checkbox d-inline-block me-3">
                                                <input type="checkbox" class="custom-control-input" name="opsi_transportasi[]" value="lainnya" {{ in_array('lainnya', old('opsi_transportasi', [])) ? 'checked' : '' }}>
                                                <span class="custom-control-label">Lainnya</span>
                                            </label>
                                            @error('opsi_transportasi')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ==================== BAGIAN C2 ==================== -->
                                <div class="section-title">C2. Akomodasi Hotel</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Reservasi Hotel</label>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input hotel-toggle" name="hotel_reservasi" value="1" {{ old('hotel_reservasi') == '1' ? 'checked' : '' }}>
                                                <span class="custom-control-label">Ya</span>
                                            </label>
                                            <label class="custom-control custom-radio d-inline-block me-3">
                                                <input type="radio" class="custom-control-input hotel-toggle" name="hotel_reservasi" value="0" {{ old('hotel_reservasi') == '0' ? 'checked' : '' }}>
                                                <span class="custom-control-label">Tidak</span>
                                            </label>
                                            @error('hotel_reservasi')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="toggle-hotel" id="hotelDetails" style="{{ old('hotel_reservasi') == '1' ? 'display:block;' : 'display:none;' }}">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Nama Hotel</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control @error('nama_hotel') is-invalid @enderror" 
                                                       name="nama_hotel" value="{{ old('nama_hotel') }}">
                                                @error('nama_hotel')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Lokasi / Alamat</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control @error('lokasi_hotel') is-invalid @enderror" 
                                                       name="lokasi_hotel" placeholder="Kota / Wilayah" value="{{ old('lokasi_hotel') }}">
                                                @error('lokasi_hotel')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                                <textarea class="form-control mt-2 @error('alamat_hotel') is-invalid @enderror" 
                                                          name="alamat_hotel" rows="2" placeholder="Alamat lengkap">{{ old('alamat_hotel') }}</textarea>
                                                @error('alamat_hotel')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Tanggal Check-in / Check-out</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="date" class="form-control @error('check_in') is-invalid @enderror" 
                                                       name="check_in" value="{{ old('check_in') }}">
                                                @error('check_in')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <input type="date" class="form-control @error('check_out') is-invalid @enderror" 
                                                       name="check_out" value="{{ old('check_out') }}">
                                                @error('check_out')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Jumlah Kamar</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="number" min="1" class="form-control @error('jumlah_kamar') is-invalid @enderror" 
                                                       name="jumlah_kamar" value="{{ old('jumlah_kamar') }}">
                                                @error('jumlah_kamar')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Permintaan Khusus</label>
                                            </div>
                                            <div class="col-md-9">
                                                <textarea class="form-control @error('permintaan_khusus') is-invalid @enderror" 
                                                          name="permintaan_khusus" rows="2">{{ old('permintaan_khusus') }}</textarea>
                                                @error('permintaan_khusus')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>

                                <!-- ==================== NOTES ==================== -->
                                <div class="section-title">Catatan Tambahan</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Notes</label>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea class="summernote form-control @error('notes') is-invalid @enderror" 
                                                      name="notes" rows="4">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ==================== SUBMIT ==================== -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-9">
                                            <button type="submit" class="btn btn-success btn-lg" id="submitPapd">
                                                {{lang('Ajukan Permintaan')}}
                                            </button>
                                            <a href="{{ route('papd.index') }}" class="btn btn-secondary btn-lg">
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
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}"></script>

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
        // Inisialisasi Summernote untuk notes (jika perlu)
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

        // Toggle hotel details based on radio button
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

        // Validasi sederhana sebelum submit (opsional)
        $('#papdForm').on('submit', function(e) {
            // Cek jika hotel_reservasi = Ya tapi field hotel kosong
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

        // Toggle Nama Paspor berdasarkan jenis perjalanan
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

        // ===== OTOMATIS DURASI HARI =====
        function hitungDurasi() {
            var tglBerangkat = $('#tanggal_keberangkatan').val();
            var tglKembali = $('#tanggal_kepulangan').val();

            if (tglBerangkat && tglKembali) {
                var start = new Date(tglBerangkat);
                var end = new Date(tglKembali);

                // Validasi: jika tanggal kembali lebih kecil dari tanggal berangkat
                if (end < start) {
                    $('#durasi_hari').val('');
                    toastr.warning('Tanggal kepulangan tidak boleh lebih awal dari tanggal keberangkatan.');
                    return;
                }

                // Hitung selisih hari (1 hari = 86400000 ms)
                var diffTime = Math.abs(end - start);
                var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                // Jika berangkat dan pulang hari yang sama, maka 1 hari (sesuai kebutuhan)
                // diffDays = diffDays + 1; // uncomment jika inklusif

                $('#durasi_hari').val(diffDays);
            } else {
                $('#durasi_hari').val('');
            }
        }

        // Event ketika tanggal berangkat atau tanggal pulang berubah (change & input untuk keamanan)
        $('#tanggal_keberangkatan, #tanggal_kepulangan').on('change input', hitungDurasi);

        // Panggil saat halaman dimuat (untuk nilai old)
        hitungDurasi();
    });
</script>

@endsection