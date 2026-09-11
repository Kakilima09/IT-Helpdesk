@php
    $unreadPapd = auth()->user()->unreadNotifications()->where('data->papd_id', '!=', null)->count();
@endphp

<div class="d-flex justify-content-between align-items-center w-100">
    <span class="font-weight-semibold fs-14">{{ lang('Notifikasi PAPD', 'notification') }} ({{ $unreadPapd }})</span>
    <div>
        @if($unreadPapd == 0)
            <span class="mark-read-none fs-13">{{ lang('Mark all as read', 'notification') }}</span>
        @else
            <span class="mark-read-papd text-primary fs-13" style="cursor:pointer;" data-url="{{ route('admin.papd.markAllRead') }}">
                {{ lang('Mark all as read', 'notification') }}
            </span>
        @endif
    </div>
</div>