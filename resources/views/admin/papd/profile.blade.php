@extends('layouts.adminmasterpapd')

@section('styles')
<style>
    .profile-avatar {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #1b86c7;
    }
    .profile-card {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<!-- Page header -->
<div class="page-header d-xl-flex d-block">
    <div class="page-leftheader">
        <h4 class="page-title">
            <span class="font-weight-normal text-muted ms-2">Profil Saya</span>
        </h4>
    </div>
    <div class="page-rightheader ms-md-auto">
        <a href="{{ route('admin.papd.index') }}" class="btn btn-secondary">
            <i class="fe fe-arrow-left"></i> Kembali ke Dashboard PAPD
        </a>
    </div>
</div>

<!-- Profile Content -->
<div class="row">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="mb-3 position-relative">
                    @if ($user->image)
                        <img src="{{ asset('uploads/profile/'.$user->image) }}" class="profile-avatar" id="profilePreview" alt="{{ $user->name }}">
                    @else
                        <img src="{{ asset('uploads/profile/user-profile.png') }}" class="profile-avatar" id="profilePreview" alt="default">
                    @endif
                    <!-- Tombol ganti foto -->
                    <button class="btn btn-primary btn-sm position-absolute bottom-0 end-0" style="transform: translate(10px, 10px);" onclick="document.getElementById('photoInput').click();">
                        <i class="fe fe-camera"></i>
                    </button>
                </div>
                <h4 class="mb-1">{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                @if(!empty($user->getRoleNames()[0]))
                    <span class="badge badge-primary">{{ $user->getRoleNames()[0] }}</span>
                @endif
            </div>
        </div>
        
        <!-- Form upload foto (tersembunyi) -->
        <form id="photoForm" action="{{ route('admin.papd.updatePhoto') }}" method="POST" enctype="multipart/form-data" style="display:none;">
            @csrf
            <input type="file" name="photo" id="photoInput" accept="image/*" onchange="document.getElementById('photoForm').submit();">
        </form>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Informasi Detail</h4>
            </div>
            <div class="card-body">
                <div class="profile-card">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nama Lengkap</div>
                        <div class="col-md-8">{{ $user->name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email</div>
                        <div class="col-md-8">{{ $user->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Role</div>
                        <div class="col-md-8">
                            @if(!empty($user->getRoleNames()[0]))
                                {{ $user->getRoleNames()[0] }}
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Terdaftar Sejak</div>
                        <div class="col-md-8">{{ $user->created_at->format('d-m-Y H:i') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 fw-bold">Terakhir Login</div>
                        <div class="col-md-8">{{ $user->updated_at->format('d-m-Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview foto sebelum upload (opsional)
    document.getElementById('photoInput')?.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('profilePreview').src = ev.target.result;
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
</script>
@endsection