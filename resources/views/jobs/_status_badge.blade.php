{{--
    Partial: jobs/_status_badge.blade.php
    Usage:   @include('jobs._status_badge', ['status' => $job->status])
--}}
@php
    $cfg = \App\Helpers\JobStatus::config($status ?? '');
@endphp
<span class="badge {{ $cfg['badge'] }} rounded-pill" style="font-size:11px;">
    <i class="bi {{ $cfg['bi'] }} me-1"></i>{{ $status }}
</span>
