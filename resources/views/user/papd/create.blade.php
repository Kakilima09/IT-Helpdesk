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
                        <h1 class="mb-0">{{lang('Pengajuan Akomodasi Perjalanan Dinas (PAPD)')}}</h1>
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
                            <h4 class="card-title">{{lang('Form Pengajuan Akomodasi Perjalanan Dinas')}}</h4>
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
                                            <label class="form-label mb-0 mt-2">Jenis Pemohon <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="jenis_pemohon" id="jenis_pemohon_internal" value="internal" {{ old('jenis_pemohon', 'internal') === 'internal' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="jenis_pemohon_internal">Internal</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="jenis_pemohon" id="jenis_pemohon_eksternal" value="eksternal" {{ old('jenis_pemohon') === 'eksternal' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="jenis_pemohon_eksternal">Eksternal</label>
                                            </div>
                                            @error('jenis_pemohon')
                                                <span class="text-danger d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

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
                                            <input type="text" class="form-control @error('nama_lengkap') is-invalid @enderror"
                                            name="nama_lengkap" id="nama_lengkap_eksternal" value="{{ old('nama_lengkap') }}"
                                            maxlength="255" placeholder="Masukkan nama lengkap pemohon" required disabled>
                                            @error('nama_lengkap')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                            <small class="text-muted" id="nama-lengkap-help">Ketik minimal 2 huruf untuk mencari karyawan.</small>
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
                                                name="email" id="email" value="{{ old('email', old('jenis_pemohon') === 'eksternal' ? '' : (auth()->user()->email ?? '')) }}" readonly required>
                                            @error('email')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="id-karyawan-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">ID Karyawan <span class="text-red" id="id-karyawan-required">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('id_karyawan') is-invalid @enderror"
                                                   name="id_karyawan" id="id_karyawan" value="{{ old('id_karyawan') }}" readonly required>
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
                                                   name="nik_ktp" id="nik_ktp" value="{{ old('nik_ktp') }}" maxlength="20" placeholder="Masukkan NIK KTP" required>
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
                                            <label class="form-label mb-0 mt-2">Nama Perusahaan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('departemen') is-invalid @enderror"
                                                   name="departemen" id="departemen" value="{{ old('departemen') }}" readonly required>
                                            @error('departemen')
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

                                <hr>

                                <!-- ==================== BAGIAN B ==================== -->
                                <div class="section-title">B. Informasi Perjalanan Dinas</div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Jenis Perjalanan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('jenis_perjalanan') is-invalid @enderror" name="jenis_perjalanan" id="jenis_perjalanan" required>
                                                <option value="domestik" {{ old('jenis_perjalanan') == 'domestik' ? 'selected' : '' }}>Domestik</option>
                                                <option value="internasional" {{ old('jenis_perjalanan') == 'internasional' ? 'selected' : '' }}>Internasional</option>
                                            </select>
                                            @error('jenis_perjalanan')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="passport-details-group" style="{{ old('jenis_perjalanan', 'domestik') === 'internasional' ? 'display:block;' : 'display:none;' }}">
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

                                <div class="form-group" id="nama_sesuai_ktp_group" style="{{ old('jenis_perjalanan', 'domestik') == 'domestik' ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama Sesuai KTP <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('nama_sesuai_ktp') is-invalid @enderror"
                                                   name="nama_sesuai_ktp" value="{{ old('nama_sesuai_ktp') }}" maxlength="255">
                                            @error('nama_sesuai_ktp')
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
                                            <label class="form-label mb-0 mt-2">Role Perjalanan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="role_perjalanan" id="role_perjalanan_team" value="team" {{ old('role_perjalanan', 'sendiri') === 'team' ? 'checked' : '' }} required>
                                                <label class="form-check-label" for="role_perjalanan_team">Team</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="role_perjalanan" id="role_perjalanan_sendiri" value="sendiri" {{ old('role_perjalanan', 'sendiri') === 'sendiri' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="role_perjalanan_sendiri">Sendiri</label>
                                            </div>
                                            @error('role_perjalanan')
                                                <span class="text-danger d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="nama_anggota_team_group" style="{{ old('role_perjalanan', 'sendiri') === 'team' ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama Anggota Team <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            @php
                                                $oldTeamMembers = old('nama_anggota_team', ['']);
                                                if (!is_array($oldTeamMembers)) {
                                                    $oldTeamMembers = preg_split('/\r\n|\r|\n/', $oldTeamMembers);
                                                }
                                                $oldTeamMembers = array_values(array_filter(array_map('trim', $oldTeamMembers), function ($name) {
                                                    return $name !== '';
                                                }));
                                            @endphp
                                            <div class="d-flex mb-2">
                                                <input type="text" class="form-control" id="new-team-member-name"
                                                       maxlength="255" placeholder="Masukkan nama anggota">
                                                <button type="button" class="btn btn-outline-primary" id="add-team-member" style="margin-left: 8px;">Add</button>
                                            </div>
                                            <div id="nama_anggota_team_inputs" class="mb-2">
                                                @foreach($oldTeamMembers as $index => $teamMember)
                                                    <div class="d-flex mb-2 team-member-row">
                                                        <span class="form-control">{{ $teamMember }}</span>
                                                        <input type="hidden" name="nama_anggota_team[]" value="{{ $teamMember }}">
                                                        <button type="button" class="btn btn-outline-danger remove-team-member" aria-label="Hapus anggota" style="margin-left: 8px;">Hapus</button>
                                                    </div>
                                                    @error('nama_anggota_team.' . $index)
                                                        <span class="text-danger d-block mb-2" role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                @endforeach
                                            </div>
                                            @error('nama_anggota_team')
                                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                            <div class="mt-2">Jumlah anggota: <strong id="jumlah_anggota_team_display">0</strong> orang</div>
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

                                <div id="return-trip-fields">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Tanggal Kepulangan <span id="return-date-required-marker" class="text-red" style="{{ old('jenis_tiket') === 'pulang_pergi' ? '' : 'display:none;' }}">*</span></label>
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
                                            <label class="form-label mb-0 mt-2">Jenis Tiket <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="jenis_tiket" value="sekali_jalan" {{ old('jenis_tiket', 'sekali_jalan') === 'sekali_jalan' ? 'checked' : '' }} required>
                                                <span class="form-check-label">Sekali Jalan</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="jenis_tiket" value="pulang_pergi" {{ old('jenis_tiket') === 'pulang_pergi' ? 'checked' : '' }} required>
                                                <span class="form-check-label">Pulang Pergi</span>
                                            </label>
                                            @error('jenis_tiket')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Moda Transportasi <span class="text-red">*</span></label>
                                            <small class="text-muted d-block">Untuk perjalanan berangkat.</small>
                                        </div>
                                        <div class="col-md-9">
                                            <select class="form-control @error('moda_transportasi') is-invalid @enderror" name="moda_transportasi" id="moda_transportasi" required>
                                                <option value="">Pilih Moda Transportasi</option>
                                                <option value="pesawat" {{ old('moda_transportasi') == 'pesawat' ? 'selected' : '' }}>Pesawat</option>
                                                <option value="kereta" {{ old('moda_transportasi') == 'kereta' ? 'selected' : '' }}>Kereta</option>
                                                <option value="bus" {{ old('moda_transportasi') == 'bus' ? 'selected' : '' }}>Bus / Travel</option>
                                                <option value="kapal" {{ old('moda_transportasi') == 'kapal' ? 'selected' : '' }}>Kapal</option>
                                                <option value="whoosh" {{ old('moda_transportasi') == 'whoosh' ? 'selected' : '' }}>Whoosh</option>
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
                                            <label class="form-label mb-0 mt-2" id="label_asal">Lokasi Asal</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('kota_asal') is-invalid @enderror"
                                                   name="kota_asal" id="kota_asal" value="{{ old('kota_asal') }}" maxlength="255"
                                                   placeholder="Masukkan lokasi asal">
                                            @error('kota_asal')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"><label class="form-label mb-0 mt-2" id="tujuan-label">Kota Tujuan <span class="text-red">*</span></label></div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('kota_tujuan') is-invalid @enderror"
                                                   name="kota_tujuan" id="kota_tujuan" value="{{ old('kota_tujuan') }}" maxlength="255"
                                                   placeholder="Masukkan lokasi tujuan" required>
                                            @error('kota_tujuan')
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
                                            name="rute" id="rute" value="{{ old('rute') }}" readonly>
                                            @error('rute')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                            <small class="text-muted">Terisi otomatis dari lokasi asal dan tujuan.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama Operator & Nomor Perjalanan</label>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control @error('detail_maskapai') is-invalid @enderror"
                                            name="detail_maskapai" placeholder="Nama Operator" value="{{ old('detail_maskapai') }}">
                                            @error('detail_maskapai')
                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control @error('no_penerbangan') is-invalid @enderror"
                                            name="no_penerbangan" placeholder="Nomor Perjalanan" value="{{ old('no_penerbangan') }}">
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
                                            <small class="text-muted d-block">(Spesial Case + Extra Charge)</small>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="bagasi_tambahan" value="1" {{ old('bagasi_tambahan') == '1' ? 'checked' : '' }}>
                                                <span class="form-check-label">Ya</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="bagasi_tambahan" value="0" {{ old('bagasi_tambahan') == '0' ? 'checked' : '' }}>
                                                <span class="form-check-label">Tidak</span>
                                            </label>
                                            @error('bagasi_tambahan')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                            {{-- <div id="special-baggage-options" class="mt-2" style="{{ old('bagasi_tambahan') == '1' ? '' : 'display:none;' }}">
                                                <span class="d-block mb-1">Pilih opsi bagasi:</span>
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" name="opsi_transportasi[]" value="special_baggage" {{ in_array('special_baggage', old('opsi_transportasi', [])) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Special Baggage</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" name="opsi_transportasi[]" value="special_charge" {{ in_array('special_charge', old('opsi_transportasi', [])) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Special Charge</span>
                                                </label>
                                            </div> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Transportasi Lokal</label>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="transportasi_lokal" value="1" {{ old('transportasi_lokal') == '1' ? 'checked' : '' }}>
                                                <span class="form-check-label">Ya</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="transportasi_lokal" value="0" {{ old('transportasi_lokal') == '0' ? 'checked' : '' }}>
                                                <span class="form-check-label">Tidak</span>
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
                                            <small class="text-muted d-block">(Spesial Case + Extra Charge)</small>
                                        </div>
                                        <div class="col-md-9">
                                            <label class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="opsi_transportasi[]" value="insurance" {{ in_array('insurance', old('opsi_transportasi', [])) ? 'checked' : '' }}>
                                                <span class="form-check-label">Insurance</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="checkbox" class="form-check-input" name="opsi_transportasi[]" value="refundable" {{ in_array('refundable', old('opsi_transportasi', [])) ? 'checked' : '' }}>
                                                <span class="form-check-label">Refundable</span>
                                            </label>
                                            @error('opsi_transportasi')
                                                <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="return-transport-group" style="{{ old('jenis_tiket') === 'pulang_pergi' ? '' : 'display:none;' }}">
                                    <hr>
                                    <div class="section-title">Transportasi Pulang</div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Moda Transportasi Pulang <span class="text-red">*</span></label>
                                            </div>
                                            <div class="col-md-9">
                                                <select class="form-control @error('moda_transportasi_pulang') is-invalid @enderror"
                                                        name="moda_transportasi_pulang" id="moda_transportasi_pulang">
                                                    <option value="">Pilih Moda Transportasi</option>
                                                    <option value="pesawat" {{ old('moda_transportasi_pulang') == 'pesawat' ? 'selected' : '' }}>Pesawat</option>
                                                    <option value="kereta" {{ old('moda_transportasi_pulang') == 'kereta' ? 'selected' : '' }}>Kereta</option>
                                                    <option value="bus" {{ old('moda_transportasi_pulang') == 'bus' ? 'selected' : '' }}>Bus / Travel</option>
                                                    <option value="kapal" {{ old('moda_transportasi_pulang') == 'kapal' ? 'selected' : '' }}>Kapal</option>
                                                    <option value="whoosh" {{ old('moda_transportasi_pulang') == 'whoosh' ? 'selected' : '' }}>Whoosh</option>
                                                </select>
                                                @error('moda_transportasi_pulang')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Kelas Pulang</label>
                                            </div>
                                            <div class="col-md-9">
                                                <select class="form-control @error('kelas_pulang') is-invalid @enderror" name="kelas_pulang">
                                                    <option value="">Pilih Kelas</option>
                                                    <option value="Ekonomi" {{ old('kelas_pulang') == 'Ekonomi' ? 'selected' : '' }}>Ekonomi</option>
                                                    <option value="Bisnis" {{ old('kelas_pulang') == 'Bisnis' ? 'selected' : '' }}>Bisnis</option>
                                                    <option value="Eksekutif" {{ old('kelas_pulang') == 'Eksekutif' ? 'selected' : '' }}>Eksekutif</option>
                                                    <option value="VIP" {{ old('kelas_pulang') == 'VIP' ? 'selected' : '' }}>VIP</option>
                                                </select>
                                                @error('kelas_pulang')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2" id="label_asal_pulang">Lokasi Asal Pulang</label>
                                            </div>
                                            <div class="col-md-9">
                                                    <input type="text" class="form-control @error('kota_asal_pulang') is-invalid @enderror"
                                                           name="kota_asal_pulang" id="kota_asal_pulang" value="{{ old('kota_asal_pulang') }}" maxlength="255"
                                                           placeholder="Masukkan lokasi asal pulang">
                                                    @error('kota_asal_pulang')
                                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2" id="label_tujuan_pulang">Lokasi Tujuan Pulang</label>
                                            </div>
                                            <div class="col-md-9">
                                                    <input type="text" class="form-control @error('kota_tujuan_pulang') is-invalid @enderror"
                                                           name="kota_tujuan_pulang" id="kota_tujuan_pulang" value="{{ old('kota_tujuan_pulang') }}" maxlength="255"
                                                           placeholder="Masukkan lokasi tujuan pulang">
                                                    @error('kota_tujuan_pulang')
                                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                    @enderror
                                                </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Rute Pulang</label>
                                            </div>
                                            <div class="col-md-9">
                                                <input type="text" class="form-control @error('rute_pulang') is-invalid @enderror"
                                                       name="rute_pulang" id="rute_pulang" value="{{ old('rute_pulang') }}" readonly>
                                                @error('rute_pulang')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                                <small class="text-muted">Terisi otomatis dari lokasi asal dan tujuan pulang.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Nama Operator & Nomor Perjalanan Pulang</label>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" class="form-control @error('detail_maskapai_pulang') is-invalid @enderror"
                                                       name="detail_maskapai_pulang" placeholder="Nama Operator" value="{{ old('detail_maskapai_pulang') }}">
                                                @error('detail_maskapai_pulang')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" class="form-control @error('no_penerbangan_pulang') is-invalid @enderror"
                                                       name="no_penerbangan_pulang" placeholder="Nomor Perjalanan" value="{{ old('no_penerbangan_pulang') }}">
                                                @error('no_penerbangan_pulang')
                                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Bagasi Tambahan Pulang</label>
                                            </div>
                                            <div class="col-md-9">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="bagasi_tambahan_pulang" value="1" {{ old('bagasi_tambahan_pulang') == '1' ? 'checked' : '' }}>
                                                    <span class="form-check-label">Ya</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="bagasi_tambahan_pulang" value="0" {{ old('bagasi_tambahan_pulang') == '0' ? 'checked' : '' }}>
                                                    <span class="form-check-label">Tidak</span>
                                                </label>
                                                @error('bagasi_tambahan_pulang')
                                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                                <div id="special-baggage-options-pulang" class="mt-2" style="{{ old('bagasi_tambahan_pulang') == '1' ? '' : 'display:none;' }}">
                                                    <span class="d-block mb-1">Pilih opsi bagasi pulang:</span>
                                                    <label class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" name="opsi_transportasi_pulang[]" value="special_baggage" {{ in_array('special_baggage', old('opsi_transportasi_pulang', [])) ? 'checked' : '' }}>
                                                        <span class="form-check-label">Special Baggage</span>
                                                    </label>
                                                    <label class="form-check form-check-inline">
                                                        <input type="checkbox" class="form-check-input" name="opsi_transportasi_pulang[]" value="special_charge" {{ in_array('special_charge', old('opsi_transportasi_pulang', [])) ? 'checked' : '' }}>
                                                        <span class="form-check-label">Special Charge</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Transportasi Lokal Pulang</label>
                                            </div>
                                            <div class="col-md-9">
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="transportasi_lokal_pulang" value="1" {{ old('transportasi_lokal_pulang') == '1' ? 'checked' : '' }}>
                                                    <span class="form-check-label">Ya</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="radio" class="form-check-input" name="transportasi_lokal_pulang" value="0" {{ old('transportasi_lokal_pulang') == '0' ? 'checked' : '' }}>
                                                    <span class="form-check-label">Tidak</span>
                                                </label>
                                                @error('transportasi_lokal_pulang')
                                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label class="form-label mb-0 mt-2">Opsi Tambahan Pulang</label>
                                            </div>
                                            <div class="col-md-9">
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" name="opsi_transportasi_pulang[]" value="insurance" {{ in_array('insurance', old('opsi_transportasi_pulang', [])) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Insurance</span>
                                                </label>
                                                <label class="form-check form-check-inline">
                                                    <input type="checkbox" class="form-check-input" name="opsi_transportasi_pulang[]" value="refundable" {{ in_array('refundable', old('opsi_transportasi_pulang', [])) ? 'checked' : '' }}>
                                                    <span class="form-check-label">Refundable</span>
                                                </label>
                                                @error('opsi_transportasi_pulang')
                                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                                @enderror
                                            </div>
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
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input hotel-toggle" name="hotel_reservasi" value="1" {{ old('hotel_reservasi') == '1' ? 'checked' : '' }}>
                                                <span class="form-check-label">Ya</span>
                                            </label>
                                            <label class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input hotel-toggle" name="hotel_reservasi" value="0" {{ old('hotel_reservasi') == '0' ? 'checked' : '' }}>
                                                <span class="form-check-label">Tidak</span>
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

        // ===== Lokasi manual =====
        function transportLocationLabel(moda) {
            if (moda === 'pesawat') return 'Bandara';
            if (moda === 'kereta' || moda === 'whoosh') return 'Stasiun';
            if (moda === 'kapal') return 'Pelabuhan';
            if (moda === 'bus') return 'Terminal';
            return 'Lokasi';
        }

        function updateLocationLabels() {
            var label = transportLocationLabel($('#moda_transportasi').val());
            $('#label_asal').text(label + ' Asal');
            $('#kota_asal').attr('placeholder', 'Masukkan ' + label.toLowerCase() + ' asal');
        }

        function updateReturnLocationLabels() {
            var label = transportLocationLabel($('#moda_transportasi_pulang').val());
            $('#label_asal_pulang').text(label + ' Asal Pulang');
            $('#label_tujuan_pulang').text(label + ' Tujuan Pulang');
            $('#kota_asal_pulang').attr('placeholder', 'Masukkan ' + label.toLowerCase() + ' asal pulang');
            $('#kota_tujuan_pulang').attr('placeholder', 'Masukkan ' + label.toLowerCase() + ' tujuan pulang');
        }

        function syncRoute() {
            var origin = $.trim($('#kota_asal').val());
            var destination = $.trim($('#kota_tujuan').val());
            $('#rute').val(origin && destination ? origin + ' - ' + destination : '');
        }

        function syncReturnRoute() {
            var origin = $.trim($('#kota_asal_pulang').val());
            var destination = $.trim($('#kota_tujuan_pulang').val());
            $('#rute_pulang').val(origin && destination ? origin + ' - ' + destination : '');
        }

        $('#kota_asal, #kota_tujuan').on('input', syncRoute);
        $('#kota_asal_pulang, #kota_tujuan_pulang').on('input', syncReturnRoute);
        $('#moda_transportasi').on('change', updateLocationLabels);
        $('#moda_transportasi_pulang').on('change', updateReturnLocationLabels);
        updateLocationLabels();
        updateReturnLocationLabels();
        syncRoute();
        syncReturnRoute();

        function setApplicantType(clearFields) {
            var isInternal = $('input[name="jenis_pemohon"]:checked').val() === 'internal';

            $('#nama_lengkap').prop('disabled', !isInternal).prop('required', isInternal);
            $('#nama_lengkap').next('.select2-container').toggle(isInternal);
            $('#nama_lengkap_eksternal').toggle(!isInternal).prop('disabled', isInternal).prop('required', !isInternal);
            $('#nama-lengkap-help').text(isInternal
                ? 'Ketik minimal 2 huruf untuk mencari karyawan.'
                : 'Masukkan nama lengkap pemohon eksternal.');
            $('#id-karyawan-group').toggle(isInternal);
            $('#id_karyawan').prop('disabled', !isInternal).prop('required', isInternal);
            $('#id-karyawan-required').toggle(isInternal);
            $('#departemen').prop('readonly', isInternal);
            $('#email').prop('readonly', isInternal);

            if (clearFields) {
                $('#nama_lengkap').val(null).trigger('change');
                $('#nama_lengkap_eksternal').val('');
                $('#id_karyawan').val('');
                $('#departemen').val('');
                $('#email').val('');
                $('#nik_ktp').val('');
            }
        }

        $('input[name="jenis_pemohon"]').on('change', function() {
            setApplicantType(true);
        });
        setApplicantType(false);

        // Jika ada nilai old (setelah error validasi), set nilai awal
        @if(old('nama_lengkap') && old('jenis_pemohon', 'internal') === 'internal')
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

        function updateTravelRole() {
            var isTeam = $('input[name="role_perjalanan"]:checked').val() === 'team';
            $('#nama_anggota_team_group').toggle(isTeam);
            if (!isTeam) {
                $('#nama_anggota_team_inputs').empty();
                $('#new-team-member-name').val('');
            }
            refreshTeamInputs();
        }

        function refreshTeamInputs() {
            var isTeam = $('input[name="role_perjalanan"]:checked').val() === 'team';
            var rows = $('#nama_anggota_team_inputs .team-member-row');
            $('#new-team-member-name').prop('required', isTeam && rows.length === 0).prop('disabled', !isTeam);
            rows.find('input[name="nama_anggota_team[]"]').prop('disabled', !isTeam);
            $('#jumlah_anggota_team_display').text(rows.length);
        }

        $('#add-team-member').on('click', function() {
            var name = $.trim($('#new-team-member-name').val());
            if (!name) {
                $('#new-team-member-name').trigger('focus');
                return;
            }

            var row = $('<div class="d-flex mb-2 team-member-row"></div>');
            var memberName = $('<span class="form-control"></span>').text(name);
            var input = $('<input type="hidden" name="nama_anggota_team[]">').val(name);
            var removeButton = $('<button type="button" class="btn btn-outline-danger remove-team-member" aria-label="Hapus anggota" style="margin-left: 8px;">Hapus</button>');
            row.append(memberName, input, removeButton);
            $('#nama_anggota_team_inputs').append(row);
            $('#new-team-member-name').val('');
            refreshTeamInputs();
            $('#new-team-member-name').trigger('focus');
        });

        $('#new-team-member-name').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#add-team-member').trigger('click');
            }
        });

        $('#nama_anggota_team_inputs').on('click', '.remove-team-member', function() {
            $(this).closest('.team-member-row').remove();
            refreshTeamInputs();
        });

        $('input[name="role_perjalanan"]').on('change', updateTravelRole);
        updateTravelRole();

        function updateTicketTypeFields() {
            var isRoundTrip = $('input[name="jenis_tiket"]:checked').val() === 'pulang_pergi';
            $('#tanggal_kepulangan').prop('required', isRoundTrip);
            $('#return-date-required-marker').toggle(isRoundTrip);
            $('#return-transport-group').toggle(isRoundTrip);
            $('#moda_transportasi_pulang').prop('required', isRoundTrip);
            if (!isRoundTrip) {
                $('#moda_transportasi_pulang').val('');
                $('#kota_asal_pulang').val('');
                $('#kota_tujuan_pulang').val('');
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
        updateTicketTypeFields();
    });
</script>

@endsection
