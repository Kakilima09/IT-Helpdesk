@extends('layouts.usermaster')

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
    .profile-actions .btn {
        margin-right: 5px;
    }
</style>
@endsection

@section('content')
<!-- Page header -->
<section>
    <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="mb-0">{{lang('Profile', 'menu')}}</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="{{route('client.dashboard')}}" class="text-white-50">{{lang('Home', 'menu')}}</a></li>
                            <li class="breadcrumb-item active"><a href="#" class="text-white">{{lang('Profile', 'menu')}}</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Edit Profile Page -->
<section>
    <div class="cover-image sptb">
        <div class="container">
            <div class="row">
                @include('includes.user.verticalmenu')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header border-0">
                            <h4 class="card-title">{{lang('Edit Profile', 'menu')}}</h4>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('customer.profile.update') }}" enctype="multipart/form-data">
                                @csrf

                                <!-- Foto Profil -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label mb-0 mt-2">{{lang('Profile Picture')}}</label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="text-center">
                                                <div class="file-input-wrapper">
                                                    @if($users->image)
                                                        <img src="{{ asset('uploads/profile/'.$users->image) }}" class="profile-avatar" id="profileImagePreview" alt="Profile">
                                                    @else
                                                        <img src="{{ asset('uploads/profile/user-profile.png') }}" class="profile-avatar" id="profileImagePreview" alt="Default">
                                                    @endif
                                                    <input type="file" name="image" id="imageInput" accept="image/*">
                                                </div>
                                                <p class="text-muted mt-2">Klik foto untuk mengubah (maks: 5MB, format: jpeg, png, jpg)</p>
                                                @error('image')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-danger btn-sm" id="removeImageBtn" data-url="{{ route('customer.profile.image.remove', $users->id) }}">
                                                        <i class="fe fe-x"></i> Hapus Foto
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nama Depan -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{lang('First Name')}} <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('firstname') is-invalid @enderror" name="firstname" value="{{ old('firstname', $users->firstname) }}" required>
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
                                            <label class="form-label">{{lang('Last Name')}} <span class="text-red">*</span></label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('lastname') is-invalid @enderror" name="lastname" value="{{ old('lastname', $users->lastname) }}" required>
                                            @error('lastname')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{lang('Email')}}</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="email" class="form-control" value="{{ $users->email }}" disabled>
                                        </div>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{lang('Mobile Number')}}</label>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $users->phone) }}">
                                            @error('phone')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Country -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{lang('Country')}}</label>
                                        </div>
                                        <div class="col-md-9">
                                            <select name="country" class="form-control select2">
                                                <option value="">{{lang('Select Country')}}</option>
                                                @foreach($countries as $country)
                                                    <option value="{{ $country->name }}" {{ $country->name == $users->country ? 'selected' : '' }}>{{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timezone -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{lang('Timezone')}}</label>
                                        </div>
                                        <div class="col-md-9">
                                            <select name="timezone" class="form-control select2">
                                                <option value="">{{lang('Select Timezone')}}</option>
                                                @foreach($timezones as $tz)
                                                    <option value="{{ $tz->timezone }}" {{ $tz->timezone == $users->timezone ? 'selected' : '' }}>{{ $tz->timezone }} {{ $tz->utc }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dark Mode Toggle (Optional) -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label class="form-label">{{lang('Dark Mode')}}</label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" id="darkModeSwitch" {{ $users->custsetting && $users->custsetting->darkmode == 1 ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="darkModeSwitch">{{lang('Enable Dark Mode')}}</label>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-primary mt-2" id="saveDarkMode">{{lang('Save Dark Mode')}}</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-3"></div>
                                        <div class="col-md-9">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fe fe-save"></i> {{lang('Save Changes')}}
                                            </button>
                                            <a href="{{ route('client.dashboard') }}" class="btn btn-secondary btn-lg">
                                                {{lang('Cancel')}}
                                            </a>
                                            <button type="button" class="btn btn-danger btn-lg" id="deleteAccountBtn" data-url="{{ route('customer.profile.delete', $users->id) }}">
                                                <i class="fe fe-trash"></i> {{lang('Delete Account')}}
                                            </button>
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
<script src="{{asset('assets/plugins/select2/select2.full.min.js')}}"></script>
<script>
    $(document).ready(function() {
        // Preview image
        $('#imageInput').on('change', function(e) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $('#profileImagePreview').attr('src', event.target.result);
            }
            reader.readAsDataURL(e.target.files[0]);
        });

        // Trigger file input on image click
        $('.profile-avatar').on('click', function() {
            $('#imageInput').click();
        });

        // Select2
        $('.select2').select2({
            minimumResultsForSearch: Infinity,
            width: '100%'
        });

        // Remove image
        $('#removeImageBtn').on('click', function() {
            if (confirm('Are you sure you want to remove your profile picture?')) {
                var url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.success);
                            location.reload();
                        } else {
                            toastr.error('Failed to remove image.');
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred.');
                    }
                });
            }
        });

        // Dark Mode
        $('#saveDarkMode').on('click', function() {
            var dark = $('#darkModeSwitch').is(':checked') ? 1 : 0;
            var cust_id = {{ $users->id }};
            $.ajax({
                url: '{{ route("customer.profile.setting") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    dark: dark,
                    cust_id: cust_id
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('Dark mode updated.');
                    } else {
                        toastr.error('Failed to update.');
                    }
                },
                error: function() {
                    toastr.error('An error occurred.');
                }
            });
        });

        // Delete Account
        $('#deleteAccountBtn').on('click', function() {
            if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                var url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success('Account deleted.');
                            window.location.href = '{{ route("home") }}';
                        } else {
                            toastr.error('Failed to delete account.');
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred.');
                    }
                });
            }
        });
    });
</script>
@endsection