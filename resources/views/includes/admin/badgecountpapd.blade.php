@php
    $badgecount = auth()->user()->unreadNotifications()->where('data->papd_id', '!=', null)->count();
@endphp

@if($badgecount == 0)
    <span class="badge badge-gray">0</span>
@else
    <span class="badge badge-success badge-counter pulse-success side-badge">{{ $badgecount }}</span>
@endif