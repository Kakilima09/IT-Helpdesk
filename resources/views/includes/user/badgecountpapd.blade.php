@php
    $badgecount = auth()->guard('customer')->user()
        ? auth()->guard('customer')->user()->unreadNotifications()
            ->where('data->papd_id', '!=', null)
            ->count()
        : 0;
@endphp

@if($badgecount > 0)
    <span class="badge badge-success badge-counter pulse-success side-badge papd-badge">{{ $badgecount }}</span>
@else
    <span class="badge badge-gray papd-badge">0</span>
@endif