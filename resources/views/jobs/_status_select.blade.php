{{--
    Partial: jobs/_status_select.blade.php
    Usage:   @include('jobs._status_select', ['selected' => $job->status ?? 'In Progress', 'name' => 'status'])
--}}
@php
    $selectedStatus = $selected ?? old('status', 'In Progress');
    $fieldName      = $name ?? 'status';
    $extraClass     = $class ?? '';
@endphp
<select class="form-select {{ $extraClass }}" name="{{ $fieldName }}">
    @foreach(\App\Helpers\JobStatus::all() as $s)
    <option value="{{ $s }}" {{ $selectedStatus === $s ? 'selected' : '' }}>
        {{ \App\Helpers\JobStatus::config($s)['icon'] }} {{ $s }}
    </option>
    @endforeach
</select>
