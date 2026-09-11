@php
    $user = auth()->user();
    // Ambil NOTIFIKASI YANG BELUM DIBACA saja (unreadNotifications)
    $papdNotifications = $user ? $user->unreadNotifications()
        ->where('data->papd_id', '!=', null)
        ->orderBy('created_at', 'desc')
        ->get() : collect();
    $unreadCount = $user ? $user->unreadNotifications()
        ->where('data->papd_id', '!=', null)
        ->count() : 0;
@endphp

<li class="dropdown header-notify">
    <a href="#" class="nav-link" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fe fe-bell header-icons"></i>
        @if($unreadCount > 0)
            <span class="badge badge-danger badge-notify pulse-badge">{{ $unreadCount }}</span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
        <div class="dropdown-header border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0">📬 Notifikasi PAPD</h6>
            <a href="{{ route('admin.papd.index') }}" class="small text-primary">Lihat Semua</a>
        </div>
        <div class="dropdown-body" style="max-height: 300px; overflow-y: auto;">
            @if($papdNotifications->count() > 0)
                @foreach($papdNotifications->take(5) as $notif)
                    @php
                        $papdId = $notif->data['papd_id'] ?? null;
                        $papd = $papdId ? \App\Models\Papd\PapdRequest::find($papdId) : null;
                        $status = $papd ? $papd->status : 'unknown';
                        $badgeClass = $status == 'approved' ? 'success' : ($status == 'rejected' ? 'danger' : ($status == 'expired' ? 'secondary' : 'warning'));
                        $badgeText = $status ? strtoupper($status) : '';
                    @endphp
                    <a href="{{ route('admin.papd.index') }}" class="dropdown-item d-flex align-items-start border-bottom py-2 mark-as-read" data-id="{{ $notif->id }}">
                        <span class="badge badge-{{ $badgeClass }} me-2 mt-1">{{ $badgeText }}</span>
                        <div class="flex-grow-1 text-truncate">
                            <div class="fw-semibold small">{{ $notif->data['mailsubject'] ?? 'Notifikasi PAPD' }}</div>
                            <div class="text-muted small text-truncate">{{ Str::limit($notif->data['mailtext'] ?? '', 60) }}</div>
                            @if($papd)
                                <div class="text-muted small">Pemohon: {{ $papd->nama_lengkap }}</div>
                            @endif
                            <div class="text-muted small">{{ $notif->created_at->diffForHumans() }}</div>
                        </div>
                    </a>
                @endforeach
                @if($papdNotifications->count() > 5)
                    <a href="{{ route('admin.papd.index') }}" class="dropdown-item text-center text-primary">Lihat semua notifikasi</a>
                @endif
                <div class="dropdown-footer border-top text-center p-2">
                    <a href="#" class="mark-read-papd-all text-primary" data-url="{{ route('admin.papd.markAllRead') }}">
                        <i class="fe fe-check-circle"></i> Tandai semua sudah dibaca
                    </a>
                </div>
            @else
                <div class="dropdown-item text-center text-muted py-3">
                    <i class="fe fe-inbox me-1"></i> Tidak ada notifikasi PAPD baru
                </div>
            @endif
        </div>
    </div>
</li>

<script type="text/javascript">
    // Mark as read per item
    $(document).on('click', '.mark-as-read', function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $this.data('id');

        $.ajax({
            url: '{{ route("admin.markNotification") }}',
            method: 'GET',
            data: { id: id },
            success: function() {
                $this.closest('.dropdown-item').fadeOut(300, function() {
                    $(this).remove();
                    // Update badge
                    var badge = $('.badge.badge-notify');
                    var count = parseInt(badge.text()) - 1;
                    if (count > 0) { badge.text(count); }
                    else { badge.remove(); }
                    if ($('.dropdown-item.border-bottom').length === 0) {
                        location.reload();
                    }
                });
            }
        });
    });

    // Mark all read
    $(document).on('click', '.mark-read-papd-all', function(e) {
        e.preventDefault();
        var $this = $(this);
        var url = $this.data('url');

        $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                $this.text('Memproses...').css('opacity', 0.5);
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Terjadi kesalahan.');
            },
            complete: function() {
                $this.text('Tandai semua sudah dibaca').css('opacity', 1);
            }
        });
    });
</script>