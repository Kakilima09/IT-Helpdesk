@extends('layouts.papdmaster')

@section('styles')
<style>
    .profile-avatar {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #1b86c7;
        cursor: pointer;
    }
    .profile-avatar:hover {
        opacity: 0.8;
    }
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }
    .file-input-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
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
                        <h1 class="mb-0">{{lang('Edit Profile')}}</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('papd.index')}}" class="text-white-50">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="#" class="text-white">{{lang('Edit Profile')}}</a>
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
                            <h4 class="card-title">{{lang('Edit Profile')}}</h4>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form method="POST" action="{{ route('papd.profile.update') }}" enctype="multipart/form-data">
                                @csrf

                                <!-- Foto Profil -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Foto Profil</label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="text-center">
                                                <div class="file-input-wrapper">
                                                    @if($user->image)
                                                        <img src="{{ asset('uploads/profile/'.$user->image) }}" class="profile-avatar" id="profileImagePreview" alt="Profile">
                                                    @else
                                                        <img src="{{ asset('uploads/profile/user-profile.png') }}" class="profile-avatar" id="profileImagePreview" alt="Default">
                                                    @endif
                                                    <input type="file" name="image" id="imageInput" accept="image/*">
                                                </div>
                                                <p class="text-muted mt-2">Klik foto untuk mengubah (maks: 2MB, format: jpeg, png, jpg, gif, svg)</p>
                                                @error('image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nama Depan -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama Depan <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('firstname') is-invalid @enderror"
                                                   name="firstname" value="{{ old('firstname', $user->firstname) }}" required>
                                            @error('firstname')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Nama Belakang -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Nama Belakang <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('lastname') is-invalid @enderror"
                                                   name="lastname" value="{{ old('lastname', $user->lastname) }}" required>
                                            @error('lastname')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Username -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Username <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('username') is-invalid @enderror"
                                                   name="username" value="{{ old('username', $user->username) }}" required>
                                            @error('username')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Email <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Change Password Section -->
                                <hr>
                                <h5 class="mb-3">Ganti Password (Opsional)</h5>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Password Saat Ini</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                                   name="current_password" placeholder="Kosongkan jika tidak ingin mengubah password">
                                            @error('current_password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Password Baru</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                   name="password" placeholder="Minimal 8 karakter">
                                            @error('password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">Konfirmasi Password Baru</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="password" class="form-control"
                                                   name="password_confirmation" placeholder="Ulangi password baru">
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-9">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fe fe-save"></i> {{lang('Simpan Perubahan')}}
                                            </button>
                                            <a href="{{ route('papd.index') }}" class="btn btn-secondary btn-lg">
                                                {{lang('Batal')}}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    // Preview image before upload
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('profileImagePreview').src = event.target.result;
        }
        reader.readAsDataURL(e.target.files[0]);
    });

    // Trigger click on image when clicking the preview
    document.querySelector('.profile-avatar').addEventListener('click', function() {
        document.getElementById('imageInput').click();
    });
</script>
@endsection