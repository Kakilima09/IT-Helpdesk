<?php
    $user = Auth::guard('customer')->user();
    $notifys = $user ? $user->unreadNotifications()
        ->where('data->papd_id', '!=', null)
        ->orderBy('created_at', 'desc')
        ->paginate(5) : collect();
    $badgecount = $user ? $user->unreadNotifications()
        ->where('data->papd_id', '!=', null)
        ->count() : 0;
?>

<div class="dropdown me-0 pe-1 header-message">
    <a class="nav-link icon p-0 mt-1" data-bs-toggle="dropdown">
        <i class="feather feather-bell header-icon"></i>
        <!-- Counter - Alerts -->
        <?php if($badgecount == 0): ?>
            <span class="badge badge-gray">0</span>
        <?php else: ?>
            <span class="badge badge-success badge-counter pulse-success side-badge notifypapd-badge"><?php echo e($badgecount); ?></span>
        <?php endif; ?>
    </a>
    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow p-0 notification-dropdown-container">
        <div class="dropdown-header border-bottom d-flex justify-content-between">
            <div>
                <span class="font-weight-semibold fs-14"><?php echo e(lang('Notifikasi PAPD', 'notification')); ?> (<?php echo e($badgecount); ?>)</span>
            </div>
            <div>
                <?php if($badgecount == 0): ?>
                    <span class="mark-read-none fs-13"><?php echo e(lang('Mark all as read', 'notification')); ?></span>
                <?php else: ?>
                    <span class="mark-read-papd text-primary fs-13" style="cursor:pointer;" data-url="<?php echo e(route('papd.markAllRead')); ?>">
                        <?php echo e(lang('Mark all as read', 'notification')); ?>

                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php $__empty_1 = true; $__currentLoopData = $notifys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $papdId = $notification->data['papd_id'] ?? null;
                $papd = $papdId ? \App\Models\Papd\PapdRequest::find($papdId) : null;
                $status = $papd ? $papd->status : 'unknown';
                $statusText = $status ? strtoupper($status) : '';
                $badgeClass = $status == 'approved' ? 'success' : ($status == 'rejected' ? 'danger' : ($status == 'expired' ? 'secondary' : 'warning'));
                $iconColor = $status == 'approved' ? '#28a745' : ($status == 'rejected' ? '#dc3545' : ($status == 'expired' ? '#6c757d' : '#ffc107'));
            ?>

            <a class="dropdown-item border-bottom mark-as-read" href="<?php echo e(route('papd.show', $papdId ?? '#')); ?>" data-id="<?php echo e($notification->id); ?>">
                <div class="d-flex align-items-center">
                    <div class="">
                        <span class="bg-<?php echo e($badgeClass); ?>-transparent brround fs-12 notifications">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="<?php echo e($iconColor); ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
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
                                <?php echo e($notification->data['mailsubject'] ?? 'Notifikasi PAPD'); ?>

                                <span class="badge badge-<?php echo e($badgeClass); ?> ms-1"><?php echo e($statusText); ?></span>
                            </h6>
                            <p class="fs-13 mb-1 text-wrap">
                                <?php echo e(Str::limit($notification->data['mailtext'] ?? '', 80)); ?>

                                <?php if($papd): ?>
                                    <br><small class="text-muted">Pemohon: <?php echo e($papd->nama_lengkap); ?></small>
                                <?php endif; ?>
                            </p>
                            <div class="small text-muted">
                                <?php echo e($notification->created_at->diffForHumans()); ?>

                            </div>
                        </div>
                    </div>
                </div>
            </a>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <a class="dropdown-item border-bottom notification-dropdown" href="">
                <div class="d-flex justify-content-center align-items-center">
                    <div class="d-flex">
                        <div class="ps-3 text-center">
                            <img src="<?php echo e(asset('assets/images/nonotification.png')); ?>" alt="">
                            <p class="fs-13 mb-1 text-muted"><?php echo e(lang('Tidak ada notifikasi PAPD baru', 'notification')); ?></p>
                        </div>
                    </div>
                </div>
            </a>
        <?php endif; ?>

        <div class="text-center p-2">
            <a href="<?php echo e(route('papd.notifications')); ?>" class=""><?php echo e(lang('See All Notifications', 'notification')); ?></a>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Mark As Read (per item)
    function sendMarkRequest(id = null) {
        return $.ajax("<?php echo e(route('customer.markNotification')); ?>", {
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
                var badge = $('.notifypapd-badge');
                var count = parseInt(badge.text()) - 1;
                if (count > 0) {
                    badge.text(count);
                } else {
                    badge.remove();
                    // Update header text
                    var header = $('.dropdown-header .font-weight-semibold');
                    if (header.length) {
                        header.text('<?php echo e(lang("Notifikasi PAPD", "notification")); ?> (0)');
                    }
                }
                // Jika tidak ada notifikasi, tampilkan pesan kosong
                if ($('.mark-as-read').length === 0) {
                    location.reload();
                }
            });
        });
    });

    // Mark All as Read (khusus PAPD)
    $(document).on('click', '.mark-read-papd', function(e) {
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
                    // Hapus semua item notifikasi dari dropdown
                    $('.mark-as-read').closest('.dropdown-item').fadeOut(300, function() {
                        $(this).remove();
                    });
                    // Hapus badge
                    $('.notifypapd-badge').remove();
                    // Update header text
                    var header = $('.dropdown-header .font-weight-semibold');
                    if (header.length) {
                        header.text('<?php echo e(lang("Notifikasi PAPD", "notification")); ?> (0)');
                    }
                    // Ganti teks mark all read menjadi none
                    $('.mark-read-papd').replaceWith('<span class="mark-read-none fs-13"><?php echo e(lang("Mark all as read", "notification")); ?></span>');
                    // Tampilkan pesan kosong jika perlu
                    if ($('.mark-as-read').length === 0) {
                        location.reload();
                    }
                } else {
                    toastr.error(response.message || 'Gagal menandai semua sebagai sudah dibaca.');
                }
            },
            error: function(xhr) {
                toastr.error('Terjadi kesalahan. Silakan coba lagi.');
            },
            complete: function() {
                $this.text('<?php echo e(lang("Mark all as read", "notification")); ?>').css('opacity', 1);
            }
        });
    });
</script><?php /**PATH C:\xampp\htdocs\it-helpdesk\resources\views/includes/user/allnotifypapd.blade.php ENDPATH**/ ?>