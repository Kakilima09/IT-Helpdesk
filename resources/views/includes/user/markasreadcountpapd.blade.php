@php
    $user = Auth::guard('customer')->user();
    $papdUnreadCount = $user ? $user->unreadNotifications()->where('data->papd_id', '!=', null)->count() : 0;
@endphp

@if($papdUnreadCount == 0)
    <span class="mark-read-none fs-13">{{ lang('Mark all as read', 'notification') }}</span>
@else
    <span class="mark-read-papd text-primary fs-13" style="cursor:pointer;" data-url="{{ route('papd.markAllRead') }}">
        {{ lang('Mark all as read', 'notification') }}
    </span>
@endif