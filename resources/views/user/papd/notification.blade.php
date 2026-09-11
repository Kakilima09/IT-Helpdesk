@extends('layouts.papdmaster')

@section('styles')
<style>
    .papd-notification-item {
        border-left: 4px solid #1b86c7;
        transition: all 0.2s;
    }
    .papd-notification-item:hover {
        background: #f8f9fa;
    }
    .papd-notification-item.unread {
        background: #f0f7ff;
        border-left-color: #ffc107;
    }
    .papd-notification-item .badge-status {
        font-size: 9px;
        padding: 3px 8px;
        border-radius: 12px;
    }
    .papd-notification-item .time {
        font-size: 11px;
        color: #999;
    }
    .notification-empty {
        text-align: center;
        padding: 60px 20px;
    }
    .notification-empty svg {
        width: 80px;
        height: 80px;
        fill: #ddd;
        margin-bottom: 20px;
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
                        <h1 class="mb-0">Notifikasi PAPD</h1>
                        <p class="mb-0 fs-14 opacity-75">Semua notifikasi terkait permintaan perjalanan dinas Anda.</p>
                    </div>
                    <div class="col col-auto">
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item">
                                <a href="{{route('papd.index')}}" class="text-white-50">Home</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <a href="#" class="text-white">Notifikasi PAPD</a>
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section -->
<section>
    <div class="cover-image sptb">
        <div class="container">
            <div class="row">
                @include('includes.user.verticalmenupapd')

                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header border-0 d-flex justify-content-between align-items-center">
                            <h4 class="card-title">📬 Notifikasi PAPD</h4>
                            <span class="badge badge-light">{{ $notifications->total() }} total</span>
                        </div>
                        <div class="card-body">

                            <!-- ========================================== -->
                            <!-- ===== FORM FILTER ===== -->
                            <!-- ========================================== -->
                            <form method="GET" action="{{ route('papd.notifications') }}" class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Cari notifikasi..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="read_status" class="form-select">
                                        <option value="">Semua (Baca/Unread)</option>
                                        <option value="unread" {{ request('read_status') == 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
                                        <option value="read" {{ request('read_status') == 'read' ? 'selected' : '' }}>Sudah Dibaca</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                                </div>
                            </form>

                            @if($notifications->count() > 0)
                                @foreach($notifications as $notification)
                                    @php
                                        $papd = $notification->papd ?? null;
                                        $isUnread = is_null($notification->read_at);
                                    @endphp
                                    <div class="papd-notification-item p-3 mb-3 rounded border {{ $isUnread ? 'unread' : '' }}">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="fw-bold me-2">
                                                        {{ $notification->data['mailsubject'] ?? 'Notifikasi PAPD' }}
                                                    </span>
                                                    @if($papd)
                                                        <span class="badge badge-status 
                                                            @if($papd->status == 'pending') badge-warning
                                                            @elseif($papd->status == 'approved') badge-success
                                                            @elseif($papd->status == 'rejected') badge-danger
                                                            @elseif($papd->status == 'expired') badge-secondary
                                                            @endif">
                                                            {{ strtoupper($papd->status) }}
                                                        </span>
                                                    @endif
                                                    @if($isUnread)
                                                        <span class="badge badge-primary ms-2">Baru</span>
                                                    @endif
                                                </div>

                                                <div class="text-muted mb-2" style="font-size: 13px;">
                                                    {!! Str::limit($notification->data['mailtext'] ?? '', 200) !!}
                                                </div>

                                                @if($papd)
                                                    <div class="d-flex flex-wrap gap-3 small text-muted">
                                                        <span><strong>Pemohon:</strong> {{ $papd->nama_lengkap ?? '-' }}</span>
                                                        <span><strong>Tujuan:</strong> {{ $papd->kota_tujuan ?? '-' }}</span>
                                                        <span><strong>Berangkat:</strong> 
                                                            @if($papd->tanggal_keberangkatan)
                                                                {{ \Carbon\Carbon::parse($papd->tanggal_keberangkatan)->format('d M Y') }}
                                                                {{ $papd->jam_keberangkatan ?? '' }}
                                                            @else
                                                                -
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="text-end ms-3" style="min-width: 100px;">
                                                <div class="time mb-2">
                                                    {{ $notification->created_at->timezone(auth()->user()->timezone ?? 'Asia/Jakarta')->format('d M Y H:i') }}
                                                </div>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    @if($papd)
                                                        <a href="{{ route('papd.show', $papd->id) }}" class="btn btn-outline-primary" title="Detail PAPD">
                                                            <i class="fe fe-eye"></i>
                                                        </a>
                                                        @if($papd->status == 'approved')
                                                            <a href="{{ route('papd.downloadPdf', $papd->id) }}" class="btn btn-outline-success" title="Download PDF">
                                                                <i class="fe fe-download"></i>
                                                            </a>
                                                        @endif
                                                    @endif
                                                    <a href="{{ route('customer.notiication.view', $notification->id) }}" class="btn btn-outline-secondary" title="Lihat detail notifikasi">
                                                        <i class="fe fe-file-text"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Pagination -->
                                <div class="mt-4">
                                    {{ $notifications->appends(request()->query())->links('admin.notificationpagination') }}
                                </div>
                            @else
                                <div class="notification-empty">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.9,21.1l-19-19C2.7,2,2.4,2,2.2,2.1C2,2.3,2,2.7,2.1,2.9l4.5,4.5C6.2,8.2,6,9.1,6,10v4.1c-1.1,0.2-2,1.2-2,2.4v2C4,18.8,4.2,19,4.5,19h3.7c0.5,1.7,2,3,3.8,3c1.8,0,3.4-1.3,3.8-3h2.5l2.9,2.9c0.1,0.1,0.2,0.1,0.4,0.1c0.1,0,0.3-0.1,0.4-0.1C22,21.7,22,21.3,21.9,21.1z"/></svg>
                                    <h5>Belum ada notifikasi PAPD</h5>
                                    <p class="text-muted">Anda akan menerima notifikasi terkait permintaan perjalanan dinas di sini.</p>
                                    <a href="{{ route('papd.create') }}" class="btn btn-primary mt-3">
                                        <i class="fe fe-plus"></i> Buat Permintaan Baru
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection