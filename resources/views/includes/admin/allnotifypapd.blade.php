@php
    $notifys = auth()->user()->unreadNotifications()
        ->where('data->papd_id', '!=', null)
        ->orderBy('created_at', 'desc')
        ->paginate(5);
    $badgecount = auth()->user()->unreadNotifications()
        ->where('data->papd_id', '!=', null)
        ->count();
@endphp

@forelse($notifys as $notification)
    @php
        $papdId = $notification->data['papd_id'] ?? null;
        $papd = $papdId ? \App\Models\Papd\PapdRequest::find($papdId) : null;
        $status = $papd ? $papd->status : 'unknown';
        $badgeClass = $status == 'approved' ? 'success' : ($status == 'rejected' ? 'danger' : ($status == 'expired' ? 'secondary' : 'warning'));
        $badgeText = $status ? strtoupper($status) : '';
        $iconColor = $status == 'approved' ? '#28a745' : ($status == 'rejected' ? '#dc3545' : ($status == 'expired' ? '#6c757d' : '#ffc107'));
    @endphp

    @if(array_key_exists('papd_assign', $notification->data) && $notification->data['papd_assign'] == 'yes')
        <!-- PAPD Assigned -->
        <a class="dropdown-item border-bottom mark-as-read" href="{{ route('admin.papd.show', $papdId ?? '#') }}" data-id="{{ $notification->id }}">
            <div class="d-flex align-items-center">
                <div class="">
                    <span class="bg-success-transparent brround fs-12 notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $iconColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user-check">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="8.5" cy="7" r="4"></circle>
                            <polyline points="17 11 19 13 23 9"></polyline>
                        </svg>
                    </span>
                </div>
                <div class="d-flex">
                    <div class="ps-3">
                        <h6 class="mb-1">{{ Str::limit($notification->data['title'] ?? 'PAPD Assigned', '30') }}</h6>
                        <p class="fs-13 mb-1 text-wrap">
                            <span class="badge badge-{{ $badgeClass }}">{{ $badgeText }}</span>
                            {{ $notification->data['papd_id'] }} - {{ $papd ? $papd->nama_lengkap : 'Pemohon' }}
                        </p>
                        <div class="small text-muted">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            </div>
        </a>

    @elseif($notification->data['status'] == 'mail')
        <!-- PAPD Mail Notification -->
        <a class="dropdown-item border-bottom mark-as-read" href="{{ route('admin.papd.show', $papdId ?? '#') }}" data-id="{{ $notification->id }}">
            <div class="d-flex align-items-center">
                <div class="">
                    <span class="bg-success-transparent brround fs-12 notifications">
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
                            <span class="badge badge-{{ $badgeClass }} ms-1">{{ $badgeText }}</span>
                        </h6>
                        <p class="fs-13 mb-1 text-wrap">
                            {{ Str::limit($notification->data['mailtext'] ?? '', 100) }}
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

    @else
        <!-- PAPD General Notification -->
        <a class="dropdown-item border-bottom mark-as-read" href="{{ route('admin.papd.show', $papdId ?? '#') }}" data-id="{{ $notification->id }}">
            <div class="d-flex align-items-center">
                <div class="">
                    <span class="bg-{{ $badgeClass }}-transparent brround fs-12 notifications">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $iconColor }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                    </span>
                </div>
                <div class="d-flex">
                    <div class="ps-3">
                        <h6 class="mb-1">
                            {{ $notification->data['title'] ?? 'Notifikasi PAPD' }}
                            <span class="badge badge-{{ $badgeClass }} ms-1">{{ $badgeText }}</span>
                        </h6>
                        <p class="fs-13 mb-1 text-wrap">
                            {{ Str::limit($notification->data['mailtext'] ?? $notification->data['message'] ?? '', 80) }}
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
    @endif

@empty
    <a class="dropdown-item border-bottom mark-as-read notification-dropdown" href="">
        <div class="d-flex justify-content-center align-items-center">
            <div class="d-flex">
                <div class="ps-3 text-center">
                    <img src="{{ asset('assets/images/nonotification.png') }}" alt="">
                    <p class="fs-13 mb-1 text-muted">{{ lang('Tidak ada notifikasi PAPD baru', 'notification') }}</p>
                </div>
            </div>
        </div>
    </a>
@endforelse

<script type="text/javascript">
    // Mark As Read per item
    function sendMarkRequest(id = null) {
        return $.ajax("{{ route('admin.markNotification') }}", {
            method: 'GET',
            data: {
                id: id
            }
        });
    }

    $(document).on('click', '.mark-as-read', function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');
        var request = sendMarkRequest(id);
        request.done(function() {
            $this.closest('.dropdown-item').fadeOut(300, function() {
                $(this).remove();
                // Update badge count
                var badgeContainer = $('#badgecountpapd .badge');
                var count = parseInt(badgeContainer.text()) - 1;
                if (count > 0) {
                    badgeContainer.text(count);
                } else {
                    badgeContainer.removeClass('badge-success badge-counter pulse-success side-badge').addClass('badge-gray').text('0');
                }
                // Update header count
                var headerCount = $('#markasreadpapdcount .font-weight-semibold');
                var newCount = parseInt(headerCount.text().match(/\d+/)) - 1;
                if (newCount > 0) {
                    headerCount.text('Notifikasi PAPD (' + newCount + ')');
                } else {
                    headerCount.text('Notifikasi PAPD (0)');
                    $('#markasreadpapdcount .mark-read-papd').replaceWith('<span class="mark-read-none fs-13">{{ lang("Mark all as read", "notification") }}</span>');
                }
                // If no items left, reload to show empty message
                if ($('.mark-as-read').length === 0) {
                    location.reload();
                }
            });
        });
    });
</script>