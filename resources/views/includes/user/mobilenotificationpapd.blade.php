<div class="dropdown me-0 pe-1 header-message">
    <a class="nav-link icon p-0 mt-1" data-bs-toggle="dropdown">
        <i class="feather feather-bell header-icon"></i>
        <!-- Counter - Alerts -->
        @php
            $user = Auth::guard('customer')->user();
            $papdNotifyCount = $user ? $user->unreadNotifications()->where('data->papd_id', '!=', null)->count() : 0;
        @endphp
        @if($papdNotifyCount == '0')
            <span class="badge badge-gray">0</span>
        @else
            <span class="badge badge-success badge-counter pulse-success side-badge">{{ $papdNotifyCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow p-0 notification-dropdown-container">
        <div class="dropdown-header border-bottom d-flex justify-content-between">
            <div>
                <span class="font-weight-semibold fs-14">{{ lang('New Notifications', 'notification') }}({{ $papdNotifyCount }})</span>
            </div>
            <div>
                @if($papdNotifyCount == '0')
                    <span class="mark-read-none fs-13">{{ lang('Mark all as read', 'notification') }}</span>
                @else
                    <span class="mark-read text-primary fs-13" style="cursor:pointer;" data-url="{{ route('papd.markAllRead') }}">{{ lang('Mark all as read', 'notification') }}</span>
                @endif
            </div>
        </div>
        @if($user)
            @php
                $papdNotifications = $user->unreadNotifications()
                    ->where('data->papd_id', '!=', null)
                    ->orderBy('created_at', 'desc')
                    ->paginate(2);
            @endphp
            @forelse($papdNotifications as $notification)
                @php
                    $papdId = $notification->data['papd_id'] ?? null;
                    $papd = $papdId ? \App\Models\Papd\PapdRequest::find($papdId) : null;
                    $status = $papd ? $papd->status : 'unknown';
                    $badgeClass = $status == 'approved' ? 'success' : ($status == 'rejected' ? 'danger' : ($status == 'expired' ? 'secondary' : 'warning'));
                    $iconClass = $status == 'approved' ? 'text-success' : ($status == 'rejected' ? 'text-danger' : ($status == 'expired' ? 'text-secondary' : 'text-warning'));
                    $iconColor = $status == 'approved' ? '#28a745' : ($status == 'rejected' ? '#dc3545' : ($status == 'expired' ? '#6c757d' : '#ffc107'));
                @endphp

                <a class="dropdown-item border-bottom mark-as-read" href="{{ route('papd.show', $papdId ?? '#') }}" data-id="{{ $notification->id }}">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <span class="bg-{{ $badgeClass }}-transparent brround fs-12 notifications">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $iconColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <polyline points="10 9 9 9 8 9"></polyline>
                                </svg>
                            </span>
                        </div>
                        <div class="d-flex">
                            <div class="ps-3">
                                <h6 class="mb-1">
                                    {{ $notification->data['mailsubject'] ?? 'Notifikasi PAPD' }}
                                    <span class="badge badge-{{ $badgeClass }} ms-1">{{ strtoupper($status) }}</span>
                                </h6>
                                <p class="fs-13 mb-1 text-wrap">
                                    {{ Str::limit($notification->data['mailtext'] ?? '', 80) }}
                                    @if($papd)
                                        <br><small class="text-muted">Pemohon: {{ $papd->nama_lengkap }}</small>
                                    @endif
                                </p>
                                <div class="small text-muted">
                                    {{ $notification->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <a class="dropdown-item border-bottom notification-dropdown" href="">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="d-flex">
                            <div class="ps-3 text-center">
                                <img src="{{ asset('assets/images/nonotification.png') }}" alt="">
                                <p class="fs-13 mb-1 text-muted">{{ lang('There are no new notifications to display', 'notification') }}</p>
                            </div>
                        </div>
                    </div>
                </a>
            @endforelse
        @endif

        <div class="text-center p-2">
            <a href="{{ route('papd.notifications') }}" class="">{{ lang('See All Notifications', 'notification') }}</a>
        </div>
    </div>
</div>