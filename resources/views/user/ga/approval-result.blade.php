@extends('layouts.usermasterga')

@section('styles')
@endsection

@section('content')
<section>
    <div class="bannerimg cover-image" data-bs-image-src="{{asset('assets/images/photos/banner1.jpg')}}">
        <div class="header-text mb-0">
            <div class="container">
                <div class="row text-white">
                    <div class="col">
                        <h1 class="mb-0">{{lang('Hasil Persetujuan GA')}}</h1>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('ga.index')}}" class="text-white-50">{{lang('Home')}}</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="#" class="text-white">{{lang('Hasil Persetujuan')}}</a>
                            </li>
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
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-8 col-md-10">
                    <div class="card">
                        <div class="card-body text-center p-5">
                            @if($success)
                                <span class="avatar avatar-xxl bg-success-transparent text-success rounded-circle mb-4 d-inline-flex align-items-center justify-content-center" style="width:90px;height:90px;">
                                    <i class="fe fe-check-circle fs-40"></i>
                                </span>
                                <h3 class="mt-2 text-success">{{ lang('Berhasil') }}</h3>
                            @else
                                <span class="avatar avatar-xxl bg-danger-transparent text-danger rounded-circle mb-4 d-inline-flex align-items-center justify-content-center" style="width:90px;height:90px;">
                                    <i class="fe fe-x-circle fs-40"></i>
                                </span>
                                <h3 class="mt-2 text-danger">{{ lang('Gagal') }}</h3>
                            @endif

                            <p class="fs-16 mt-3">{{ $message }}</p>

                            @if(isset($gaRequest) && $gaRequest)
                                <table class="table table-bordered text-start mt-4">
                                    <tr>
                                        <th>No. Request</th>
                                        <td>{{ $gaRequest->request_no }}</td>
                                    </tr>
                                    <tr>
                                        <th>Pemohon</th>
                                        <td>{{ $gaRequest->nama_lengkap }}</td>
                                    </tr>
                                    <tr>
                                        <th>Total</th>
                                        <td>Rp {{ number_format($gaRequest->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>{{ $gaRequest->statusLabel() }}</td>
                                    </tr>
                                </table>
                            @endif

                            <a href="{{ route('ga.index') }}" class="btn btn-primary btn-lg mt-3">
                                <i class="fe fe-home"></i> {{lang('Kembali ke Dashboard')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
@endsection