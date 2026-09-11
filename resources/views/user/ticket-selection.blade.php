@extends('layouts.custommaster')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-20">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Pilih Layanan Tiket</h2>
                <p class="text-muted">Silakan pilih jenis tiket yang ingin Anda akses</p>
            </div>

            <div class="row g-4">
                <!-- Card IT Helpdesk -->
                <div class="col-md-15">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-3">
                                <i class="fe fe-monitor display-3 text-primary"></i>
                            </div>
                            <h5 class="card-title fw-bold">IT Helpdesk</h5>
                            <p class="card-text text-muted">Ajukan dan kelola tiket untuk permasalahan IT, perangkat keras, dan jaringan.</p>
                            <a href="{{ route('client.dashboard') }}" class="btn btn-primary btn-lg w-100">
                                <i class="fe fe-arrow-right me-2"></i> Masuk IT Helpdesk
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card PAPD -->
                <div class="col-md-15">
                    <div class="card h-100 shadow-sm border-0 hover-card">
                        <div class="card-body text-center p-4">
                            <div class="icon-wrapper mb-3">
                                <i class="fe fe-file-text display-3 text-success"></i>
                            </div>
                            <h5 class="card-title fw-bold">PAPD</h5>
                            <p class="card-text text-muted">Kelola tiket PAPD (Permintaan Perjalanan Dinas) secara terpusat.</p>
                            <a href="{{ route('papd.index') }}" class="btn btn-success btn-lg w-100">
                                <i class="fe fe-arrow-right me-2"></i> Masuk PAPD
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('logout') }}" class="text-muted" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fe fe-log-out me-1"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.12) !important;
    }
    .icon-wrapper i {
        font-size: 4rem;
        line-height: 1;
    }
    .btn-lg {
        padding: 0.8rem 1.2rem;
        font-size: 1.1rem;
    }
</style>
@endsection