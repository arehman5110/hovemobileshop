@extends('layouts.app')
@section('title','Jobs')
@section('topbar-actions')
<a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>New Job</a>
@endsection

@push('styles')
<style>
/* ── Cards ─────────────────────────────────────────────────────── */
.pro-card {
    background:#fff;border:none;border-radius:14px;
    box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(0,0,0,.08);
}
[data-bs-theme="dark"] .pro-card {
    background:#1c1c1e;
    box-shadow:0 1px 3px rgba(0,0,0,.3),0 6px 20px rgba(0,0,0,.4);
}

/* ── Stat cards ─────────────────────────────────────────────────── */
.stat-card { padding:18px 20px;border-radius:14px;transition:transform .2s,box-shadow .2s; }
.stat-card:hover { transform:translateY(-2px);box-shadow:0 6px 24px rgba(0,0,0,.12); }
.stat-val { font-family:'Syne',sans-serif;font-size:30px;font-weight:800;line-height:1;margin:4px 0 2px; }
.stat-icon { width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0; }

/* ── Filter card ────────────────────────────────────────────────── */
.filter-card { padding:14px 16px;border-radius:14px; }

/* ── Active filters bar ─────────────────────────────────────────── */
.active-filters {
    display:flex;align-items:center;gap:8px;flex-wrap:wrap;
    padding:10px 16px;
    background:rgba(13,110,253,.04);
    border-radius:10px;
    border:1px solid rgba(13,110,253,.1);
    font-size:12px;
}
[data-bs-theme="dark"] .active-filters {
    background:rgba(13,110,253,.08);
    border-color:rgba(13,110,253,.2);
}
.filter-tag {
    display:inline-flex;align-items:center;gap:4px;
    padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;
}
.filter-tag-status-in-progress  { background:rgba(13,110,253,.12);color:#0d6efd; }
.filter-tag-status-waiting-parts { background:rgba(255,193,7,.15);color:#cc9a00; }
.filter-tag-status-completed     { background:rgba(25,135,84,.12);color:#198754; }
.filter-tag-status-cancelled     { background:rgba(108,117,125,.12);color:#6c757d; }
.filter-tag-status-ready-for-collection { background:rgba(13,202,240,.12);color:#087990; }
[data-bs-theme="dark"] .filter-tag-status-ready-for-collection { color:#0dcaf0; }
[data-bs-theme="dark"] .filter-tag-status-waiting-parts { color:#ffc107; }

/* ── Table ──────────────────────────────────────────────────────── */
.table-card { border-radius:14px;overflow:hidden; }
.job-row { cursor:pointer;transition:background .15s; }
.job-row:hover { background:rgba(13,110,253,.05) !important; }
[data-bs-theme="dark"] .job-row:hover { background:rgba(13,110,253,.1) !important; }
/* Selected/expanded row — amber/orange tint so it's clearly different from hover */
.job-row.expanded {
    background:rgba(255,152,0,.08) !important;
    border-left:3px solid #ff9800;
    box-shadow:inset 0 0 0 1px rgba(255,152,0,.15);
}
[data-bs-theme="dark"] .job-row.expanded {
    background:rgba(255,152,0,.12) !important;
}

/* ── Expand row ─────────────────────────────────────────────────── */
.expand-row { display:none; }
.expand-row.show { display:table-row; }
.expand-cell { padding:0 !important;border-top:none !important; }
.expand-inner {
    padding:20px 28px 22px;
    background:rgba(255,152,0,.04);
    border-bottom:2px solid rgba(255,152,0,.25);
    border-left:3px solid #ff9800;
}
[data-bs-theme="dark"] .expand-inner {
    background:rgba(255,152,0,.06);
}
.expand-grid {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(180px,1fr));
    gap:16px;
    margin-bottom:16px;
}
.expand-section { display:flex;flex-direction:column;gap:4px; }
.expand-lbl {
    font-size:10px;font-weight:700;text-transform:uppercase;
    letter-spacing:.07em;opacity:.45;margin-bottom:3px;
}
.expand-val { font-size:13px;font-weight:500; }
.expand-sub { font-size:11px;color:var(--bs-secondary-color); }
.expand-divider { border-top:1px solid var(--bs-border-color);margin:14px 0 12px; }
.expand-actions { display:flex;gap:8px;flex-wrap:wrap; }

/* ── Chevron ────────────────────────────────────────────────────── */
.row-chevron { transition:transform .22s;font-size:11px;opacity:.35;display:block; }
.job-row.expanded .row-chevron { transform:rotate(180deg);opacity:.7; }

/* ── Results bar ────────────────────────────────────────────────── */
.results-bar {
    display:flex;align-items:center;justify-content:space-between;
    padding:12px 20px;
    border-bottom:1px solid var(--bs-border-color);
    font-size:12px;
}

/* ── Status badge helper ────────────────────────────────────────── */
.s-badge { display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:11px;font-weight:700; }
.s-in-progress  { background:rgba(13,110,253,.12);color:#0d6efd; }
.s-waiting       { background:rgba(255,193,7,.2);color:#a07000; }
.s-completed     { background:rgba(25,135,84,.12);color:#198754; }
.s-cancelled     { background:rgba(108,117,125,.1);color:#6c757d; }
.s-ready         { background:rgba(13,202,240,.12);color:#087990; }
[data-bs-theme="dark"] .s-ready { color:#0dcaf0; }
[data-bs-theme="dark"] .s-waiting { color:#ffc107; }
</style>
@endpush

@section('content')
@php
    $selectedStatuses = request()->has('status')
        ? (array)request('status')
        : ['In Progress','Waiting Parts'];
    $allStatuses = ['In Progress','Waiting Parts','Completed','Cancelled'];
    $isAll       = count(array_intersect($selectedStatuses,$allStatuses)) === count($allStatuses);
    $hasSearch   = request()->filled('search');
    $hasDateFrom = request()->filled('date_from');
    $hasDateTo   = request()->filled('date_to');
    $hasFilters  = $hasSearch || $hasDateFrom || $hasDateTo || (!$isAll);

    $statsTotal   = \App\Models\Job::count();
    $statsInProg  = \App\Models\Job::where('status','In Progress')->count();
    $statsWaiting = \App\Models\Job::where('status','Waiting Parts')->count();
    $statsDone    = \App\Models\Job::where('status','Completed')->count();
    $statsCancelled = \App\Models\Job::where('status','Cancelled')->count();
@endphp

{{-- Page Header --}}
<div class="d-flex align-items-start justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h2 class="syne mb-0" style="font-size:24px;font-weight:800;">🔧 Repair Jobs</h2>
        <div class="text-secondary" style="font-size:13px;margin-top:2px;">
            Manage and track all customer repair jobs
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('jobs.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>New Job
        </a>
    </div>
</div>

{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="pro-card stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="text-secondary" style="font-size:12px;">Total Jobs</div>
                    <div class="stat-val">{{ $statsTotal }}</div>
                    <div class="text-secondary" style="font-size:11px;">All time</div>
                </div>
                <div class="stat-icon" style="background:rgba(13,110,253,.1);color:#0d6efd;">🗂️</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="text-secondary" style="font-size:12px;">In Progress</div>
                    <div class="stat-val text-primary">{{ $statsInProg }}</div>
                    <div class="text-secondary" style="font-size:11px;">Active now</div>
                </div>
                <div class="stat-icon" style="background:rgba(13,110,253,.1);color:#0d6efd;">🔧</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="text-secondary" style="font-size:12px;">Waiting Parts</div>
                    <div class="stat-val text-warning">{{ $statsWaiting }}</div>
                    <div class="text-secondary" style="font-size:11px;">On hold</div>
                </div>
                <div class="stat-icon" style="background:rgba(255,193,7,.12);color:#cc9a00;">⏳</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="pro-card stat-card">
            <div class="d-flex align-items-start justify-content-between">
                <div>
                    <div class="text-secondary" style="font-size:12px;">Completed</div>
                    <div class="stat-val text-success">{{ $statsDone }}</div>
                    <div class="text-secondary" style="font-size:11px;">All time</div>
                </div>
                <div class="stat-icon" style="background:rgba(25,135,84,.1);color:#198754;">✅</div>
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="pro-card filter-card mb-3">
    <form method="GET" id="filter-form">
        <div class="row g-2 align-items-end">

            {{-- Search with live suggestions --}}
            <div class="col-12 col-sm-4 col-md-3">
                <label class="form-label">Search</label>
                <div class="position-relative">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search-input" class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Customer name or phone..."
                            autocomplete="off">
                    </div>
                    {{-- Suggestions dropdown --}}
                    <div id="search-suggestions"
                        style="display:none;position:absolute;top:100%;left:0;right:0;z-index:1050;
                               background:var(--bs-body-bg);border:1px solid var(--bs-border-color);
                               border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,.15);
                               max-height:220px;overflow-y:auto;margin-top:2px;">
                    </div>
                </div>
            </div>

            {{-- Status dropdown --}}
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label">Status</label>
                <div class="dropdown">
                    <button type="button"
                        class="btn btn-sm btn-outline-secondary dropdown-toggle w-100 text-start d-flex align-items-center justify-content-between"
                        style="font-size:12px;"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <span id="status-dd-label">
                            @if($isAll) All Statuses
                            @elseif(count($selectedStatuses)===1) {{ $selectedStatuses[0] }}
                            @else {{ count($selectedStatuses) }} selected
                            @endif
                        </span>
                        <i class="bi bi-chevron-down" style="font-size:10px;"></i>
                    </button>
                    <ul class="dropdown-menu p-2" style="min-width:190px;">
                        <li>
                            <label class="dropdown-item rounded d-flex align-items-center gap-2 py-2" style="cursor:pointer;">
                                <input type="checkbox" class="form-check-input m-0" id="chk-all"
                                    {{ $isAll ? 'checked' : '' }} onchange="toggleAll()">
                                <span class="fw-semibold" style="font-size:12px;">All Statuses</span>
                            </label>
                        </li>
                        <li><hr class="dropdown-divider my-1"></li>
                        @foreach(['In Progress'=>['text-primary','🔧'],'Waiting Parts'=>['text-warning','⏳'],'Completed'=>['text-success','✅'],'Cancelled'=>['text-secondary','✕'],'Ready for Collection'=>['text-info','📦']] as $st=>[$cls,$ic])
                        <li>
                            <label class="dropdown-item rounded d-flex align-items-center gap-2 py-2" style="cursor:pointer;">
                                <input type="checkbox" class="form-check-input m-0 status-chk" value="{{ $st }}"
                                    {{ in_array($st,$selectedStatuses)?'checked':'' }} onchange="updateStatusDropdown()">
                                <span class="{{ $cls }}" style="font-size:12px;">{{ $ic }} {{ $st }}</span>
                            </label>
                        </li>
                        @endforeach
                        <li><hr class="dropdown-divider my-1"></li>
                        <li class="px-2"><button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-funnel me-1"></i>Apply</button></li>
                    </ul>
                </div>
                <div id="status-inputs">
                    @foreach($selectedStatuses as $s)<input type="hidden" name="status[]" value="{{ $s }}">@endforeach
                </div>
            </div>

            {{-- Date From --}}
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control form-control-sm auto-submit" value="{{ request('date_from') }}">
            </div>

            {{-- Date To --}}
            <div class="col-6 col-sm-3 col-md-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control form-control-sm auto-submit" value="{{ request('date_to') }}">
            </div>

            {{-- Clear only --}}
            <div class="col-auto">
                <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg me-1"></i>Clear
                </a>
            </div>
        </div>
    </form>
</div>

{{-- Active Filters Bar — always visible so user knows what's selected --}}
<div class="active-filters mb-3">
    <span style="font-weight:700;opacity:.6;font-size:11px;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap;">Showing:</span>

    {{-- Status tags --}}
    @if($isAll)
        <span class="filter-tag" style="background:rgba(108,117,125,.1);color:var(--bs-secondary-color);">🗂️ All Statuses</span>
    @else
        @foreach($selectedStatuses as $s)
        @php
            $tagCls  = ['In Progress'=>'filter-tag-status-in-progress','Waiting Parts'=>'filter-tag-status-waiting-parts','Completed'=>'filter-tag-status-completed','Cancelled'=>'filter-tag-status-cancelled','Ready for Collection'=>'filter-tag-status-ready-for-collection'][$s] ?? '';
            $tagIcon = ['In Progress'=>'🔧','Waiting Parts'=>'⏳','Completed'=>'✅','Cancelled'=>'✕','Ready for Collection'=>'📦'][$s] ?? '';
        @endphp
        <span class="filter-tag {{ $tagCls }}">{{ $tagIcon }} {{ $s }}</span>
        @endforeach
    @endif

    @if($hasSearch)
    <span class="filter-tag" style="background:rgba(13,110,253,.08);color:#0d6efd;">
        <i class="bi bi-search" style="font-size:10px;"></i> "{{ request('search') }}"
    </span>
    @endif

    @if($hasDateFrom)
    <span class="filter-tag" style="background:rgba(102,16,242,.08);color:#6610f2;">
        <i class="bi bi-calendar" style="font-size:10px;"></i> From {{ \Carbon\Carbon::parse(request('date_from'))->format('d M Y') }}
    </span>
    @endif

    @if($hasDateTo)
    <span class="filter-tag" style="background:rgba(102,16,242,.08);color:#6610f2;">
        <i class="bi bi-calendar" style="font-size:10px;"></i> To {{ \Carbon\Carbon::parse(request('date_to'))->format('d M Y') }}
    </span>
    @endif

    @if($hasFilters)
    <a href="{{ route('jobs.index') }}" class="ms-auto text-secondary text-decoration-none d-flex align-items-center gap-1" style="font-size:11px;white-space:nowrap;">
        <i class="bi bi-x-circle"></i> Clear filters
    </a>
    @endif
</div>

{{-- Table --}}
<div class="pro-card table-card">

    {{-- Results bar --}}
    <div class="results-bar">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold" style="font-size:13px;">
                {{ $jobs->total() }} job{{ $jobs->total()!==1?'s':'' }} found
            </span>
            @if($jobs->total() > 0)
            <span class="text-secondary">· page {{ $jobs->currentPage() }} of {{ $jobs->lastPage() }}</span>
            @endif
        </div>
        <div class="text-secondary d-flex align-items-center gap-1" style="font-size:11px;">
            <i class="bi bi-info-circle"></i> Click a row to expand details
        </div>
    </div>

    <div class="table-responsive">
        <table class="table mb-0 align-middle" style="font-size:13px;">
            <thead style="background:var(--bs-tertiary-bg);">
                <tr>
                    <th style="width:36px;border:none;padding:10px 12px;"></th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">#</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Customer</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Devices</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Date In</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Status</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Total</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 8px;">Balance</th>
                    <th style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.55;border:none;padding:10px 16px 10px 8px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($jobs as $job)
            @php
                $statusMap = [
                    'In Progress'   => ['s-in-progress','bi-tools','🔧'],
                    'Waiting Parts' => ['s-waiting','bi-clock','⏳'],
                    'Completed'     => ['s-completed','bi-check-circle','✅'],
                    'Cancelled'     => ['s-cancelled','bi-x-circle','✕'],
                    'Ready for Collection' => ['s-ready','bi-bag-check','📦'],
                ];
                $sc = $statusMap[$job->status] ?? ['s-cancelled','bi-circle','?'];
            @endphp

            {{-- Main Row --}}
            <tr class="job-row border-top" id="row-{{ $job->id }}" onclick="toggleExpand({{ $job->id }},this)">
                <td style="padding:12px 12px;text-align:center;width:36px;">
                    <i class="bi bi-chevron-down row-chevron"></i>
                </td>
                <td style="padding:12px 8px;">
                    <span class="text-secondary fw-semibold">#{{ $job->id }}</span>
                </td>
                <td style="padding:12px 8px;">
                    <div class="fw-semibold" style="line-height:1.3;">{{ $job->customer->name }}</div>
                    @if($job->customer->phone)
                    <div class="text-secondary" style="font-size:11px;">{{ $job->customer->phone }}</div>
                    @endif
                </td>
                <td style="padding:12px 8px;">
                    @foreach($job->devices->take(2) as $d)
                    <div style="line-height:1.4;">📱 {{ $d->name }}</div>
                    @endforeach
                    @if($job->devices->count() > 2)
                    <div class="text-secondary" style="font-size:11px;">+{{ $job->devices->count()-2 }} more</div>
                    @endif
                </td>
                <td style="padding:12px 8px;" class="text-secondary">
                    {{ $job->date_in->format('d M Y') }}
                    @if($job->date_out)
                    <div style="font-size:11px;">Out: {{ $job->date_out->format('d M') }}</div>
                    @endif
                </td>
                <td style="padding:12px 8px;">
                    <span class="s-badge {{ $sc[0] }}">
                        <i class="bi {{ $sc[1] }}"></i> {{ $job->status }}
                    </span>
                </td>
                <td style="padding:12px 8px;" class="fw-semibold">
                    £{{ number_format($job->totalAfterDiscount(),2) }}
                    @if($job->discount_type)
                    <div class="text-success" style="font-size:10px;">
                        @if($job->discount_type==='percent') -{{ $job->discount_value }}%
                        @else -£{{ number_format($job->discount_value,2) }}
                        @endif
                    </div>
                    @endif
                </td>
                <td style="padding:12px 8px;">
                    @if($job->isPaidInFull())
                    <span class="s-badge s-completed"><i class="bi bi-check"></i> Paid</span>
                    @elseif($job->totalPaid() > 0)
                    <div class="text-danger fw-bold">£{{ number_format($job->balanceDue(),2) }}</div>
                    <div class="text-secondary" style="font-size:10px;">Partial</div>
                    @else
                    <span class="text-danger fw-bold">£{{ number_format($job->balanceDue(),2) }}</span>
                    @endif
                </td>
                <td style="padding:12px 16px 12px 8px;text-align:right;" onclick="event.stopPropagation()">
                    <div class="d-flex gap-1 justify-content-end">
                        <a href="{{ route('jobs.show',$job) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                        <a href="{{ route('jobs.edit',$job) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form method="POST" action="{{ route('jobs.destroy',$job) }}" class="d-inline" onsubmit="return confirm('Delete job #{{ $job->id }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>

            {{-- Expanded Detail Row --}}
            <tr class="expand-row" id="expand-{{ $job->id }}">
                <td colspan="9" class="expand-cell">
                    <div class="expand-inner">
                        <div class="expand-grid">

                            {{-- Customer --}}
                            <div class="expand-section">
                                <span class="expand-lbl">👤 Customer</span>
                                <span class="expand-val">{{ $job->customer->name }}</span>
                                @if($job->customer->phone)
                                <span class="expand-sub"><i class="bi bi-telephone me-1"></i>{{ $job->customer->phone }}</span>
                                @endif
                                @if($job->customer->email)
                                <span class="expand-sub"><i class="bi bi-envelope me-1"></i>{{ $job->customer->email }}</span>
                                @endif
                            </div>

                            {{-- Devices --}}
                            <div class="expand-section">
                                <span class="expand-lbl">📱 Devices & Repairs</span>
                                @foreach($job->devices as $device)
                                <div style="margin-bottom:4px;">
                                    <span class="expand-val">{{ $device->name }}</span>
                                    @if($device->imei)
                                    <div class="expand-sub">IMEI: {{ $device->imei }}</div>
                                    @endif
                                    @foreach($device->repairItems as $ri)
                                    @if($ri->repairType)
                                    <div class="expand-sub">↳ {{ $ri->repairType->icon ?? '🔧' }} {{ $ri->repairType->name }}</div>
                                    @endif
                                    @endforeach
                                </div>
                                @endforeach
                            </div>

                            {{-- Dates --}}
                            <div class="expand-section">
                                <span class="expand-lbl">📅 Timeline</span>
                                <span class="expand-val"><i class="bi bi-box-arrow-in-right me-1 text-primary"></i>{{ $job->date_in->format('d M Y') }}</span>
                                @if($job->date_out)
                                <span class="expand-sub"><i class="bi bi-box-arrow-right me-1"></i>{{ $job->date_out->format('d M Y') }}</span>
                                @php $days = $job->date_in->diffInDays($job->date_out); @endphp
                                <span class="expand-sub">Duration: {{ $days }} day{{ $days!=1?'s':'' }}</span>
                                @else
                                <span class="text-warning expand-sub"><i class="bi bi-clock me-1"></i>In progress since {{ $job->date_in->diffForHumans() }}</span>
                                @endif
                            </div>

                            {{-- Payments --}}
                            <div class="expand-section">
                                <span class="expand-lbl">💳 Payments</span>
                                @if($job->payments->isEmpty())
                                <span class="text-warning expand-sub">No payments recorded</span>
                                @else
                                @foreach($job->payments as $pmt)
                                @php $isSplit = $pmt->notes && str_starts_with(trim($pmt->notes),'['); @endphp
                                <span class="expand-val">£{{ number_format($pmt->amount,2) }} <span class="expand-sub">{{ $isSplit?'✂️ Split':$pmt->payment_type }}</span></span>
                                @endforeach
                                @endif
                            </div>

                            {{-- Financial --}}
                            <div class="expand-section">
                                <span class="expand-lbl">💰 Financial</span>
                                <span class="expand-val">Total: £{{ number_format($job->totalAfterDiscount(),2) }}</span>
                                @if($job->discount_type)
                                <span class="text-success expand-sub">Discount: {{ $job->discount_type==='percent'?$job->discount_value.'%':'£'.number_format($job->discount_value,2) }} off</span>
                                @endif
                                @if($job->voucher_code)
                                <span class="text-success expand-sub">🎟️ {{ $job->voucher_code }} applied</span>
                                @endif
                                <span class="expand-sub">Paid: £{{ number_format($job->totalPaid(),2) }}</span>
                                @if($job->isPaidInFull())
                                <span class="text-success fw-bold expand-sub">✅ Fully Paid</span>
                                @else
                                <span class="text-danger fw-bold expand-sub">Balance: £{{ number_format($job->balanceDue(),2) }}</span>
                                @endif
                            </div>

                            {{-- Notes --}}
                            @if($job->notes)
                            <div class="expand-section">
                                <span class="expand-lbl">📋 Notes</span>
                                <span class="expand-val" style="font-size:12px;line-height:1.5;">{{ Str::limit($job->notes,150) }}</span>
                            </div>
                            @endif

                        </div>

                        <div class="expand-divider"></div>

                        {{-- Quick Actions --}}
                        <div class="expand-actions">
                            <a href="{{ route('jobs.show',$job) }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i>Full Details
                            </a>
                            <a href="{{ route('jobs.edit',$job) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            @if(!$job->isPaidInFull())
                            <a href="{{ route('jobs.show',$job) }}" class="btn btn-outline-success btn-sm">
                                <i class="bi bi-credit-card me-1"></i>Add Payment
                            </a>
                            @endif
                            @if($job->status !== 'Completed')
                            <form method="POST" action="{{ route('jobs.update-status',$job) }}" class="d-inline" onclick="event.stopPropagation()">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="Completed">
                                <button type="submit" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-check-circle me-1"></i>Mark Complete
                                </button>
                            </form>
                            @endif
                            @if($job->status !== 'Cancelled')
                            <form method="POST" action="{{ route('jobs.update-status',$job) }}" class="d-inline" onclick="event.stopPropagation()">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="Cancelled">
                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Cancel this job?')">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </td>
            </tr>

            @empty
            <tr>
                <td colspan="9" class="text-center py-5">
                    <div style="font-size:40px;opacity:.2;margin-bottom:12px;">🔧</div>
                    <div class="fw-semibold mb-1">No jobs found</div>
                    <div class="text-secondary small mb-3">
                        @if($hasFilters) Try adjusting your filters @else No repair jobs yet @endif
                    </div>
                    @if($hasFilters)
                    <a href="{{ route('jobs.index') }}" class="btn btn-outline-secondary btn-sm me-2">Clear Filters</a>
                    @endif
                    <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Create First Job</a>
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($jobs->hasPages())
    <div class="px-4 py-3 border-top d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="text-secondary" style="font-size:12px;">
            Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }} jobs
        </div>
        <div>{{ $jobs->links() }}</div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// ── Status dropdown ───────────────────────────────────────────────
var allStatuses = ['In Progress','Waiting Parts','Completed','Cancelled'];

function updateStatusDropdown() {
    var checked = Array.from(document.querySelectorAll('.status-chk:checked')).map(function(c){return c.value;});
    var allChk  = document.getElementById('chk-all');
    allChk.checked = (checked.length === allStatuses.length);

    var container = document.getElementById('status-inputs');
    container.innerHTML = '';
    (allChk.checked ? allStatuses : checked).forEach(function(s) {
        var inp = document.createElement('input');
        inp.type='hidden'; inp.name='status[]'; inp.value=s;
        container.appendChild(inp);
    });

    var label = document.getElementById('status-dd-label');
    if (allChk.checked) { label.textContent = 'All Statuses'; }
    else if (checked.length === 0) { label.textContent = 'Select...'; }
    else if (checked.length === 1) { label.textContent = checked[0]; }
    else { label.textContent = checked.length + ' selected'; }
}

function toggleAll() {
    var isOn = document.getElementById('chk-all').checked;
    document.querySelectorAll('.status-chk').forEach(function(c){ c.checked = isOn; });
    updateStatusDropdown();
}

// Auto-submit when status Apply clicked (already handled by button type=submit)
// Auto-submit on date change
document.querySelectorAll('.auto-submit').forEach(function(el) {
    el.addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });
});

// ── Live search suggestions ───────────────────────────────────────
var searchInput       = document.getElementById('search-input');
var suggestionsBox    = document.getElementById('search-suggestions');
var searchTimer       = null;
var selectedSuggIdx   = -1;
var currentSuggestions = [];

searchInput.addEventListener('input', function() {
    clearTimeout(searchTimer);
    var q = this.value.trim();
    if (q.length < 2) {
        hideSuggestions();
        return;
    }
    searchTimer = setTimeout(function() { fetchSuggestions(q); }, 250);
});

searchInput.addEventListener('keydown', function(e) {
    var items = suggestionsBox.querySelectorAll('.sugg-item');
    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedSuggIdx = Math.min(selectedSuggIdx + 1, items.length - 1);
        highlightSugg(items);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedSuggIdx = Math.max(selectedSuggIdx - 1, -1);
        highlightSugg(items);
    } else if (e.key === 'Enter') {
        if (selectedSuggIdx >= 0 && items[selectedSuggIdx]) {
            e.preventDefault();
            items[selectedSuggIdx].click();
        } else {
            hideSuggestions();
            document.getElementById('filter-form').submit();
        }
    } else if (e.key === 'Escape') {
        hideSuggestions();
    }
});

// Close suggestions when clicking outside
document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
        hideSuggestions();
    }
});

function fetchSuggestions(q) {
    fetch('{{ route("jobs.index") }}?search_suggest=1&q=' + encodeURIComponent(q), {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) { renderSuggestions(data, q); })
    .catch(function() { hideSuggestions(); });
}

function renderSuggestions(items, q) {
    currentSuggestions = items;
    selectedSuggIdx = -1;
    suggestionsBox.innerHTML = '';

    if (!items || items.length === 0) {
        suggestionsBox.innerHTML = '<div style="padding:10px 14px;font-size:12px;color:var(--bs-secondary-color);">No customers found</div>';
        suggestionsBox.style.display = 'block';
        return;
    }

    items.forEach(function(item) {
        var div = document.createElement('div');
        div.className = 'sugg-item';
        div.style.cssText = 'padding:9px 14px;cursor:pointer;display:flex;align-items:center;gap:10px;font-size:13px;border-bottom:1px solid var(--bs-border-color);transition:background .1s;';
        div.onmouseenter = function() { div.style.background = 'rgba(13,110,253,.08)'; };
        div.onmouseleave = function() { div.style.background = ''; };

        // Highlight matching text
        var name  = highlightMatch(item.name, q);
        var phone = item.phone ? highlightMatch(item.phone, q) : '';

        div.innerHTML =
            '<span style="width:30px;height:30px;border-radius:50%;background:rgba(13,110,253,.12);color:#0d6efd;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0;">'
            + item.name.charAt(0).toUpperCase()
            + '</span>'
            + '<div style="min-width:0;">'
            + '<div class="fw-semibold" style="line-height:1.2;">' + name + '</div>'
            + (phone ? '<div style="font-size:11px;color:var(--bs-secondary-color);">' + phone + '</div>' : '')
            + '<div style="font-size:10px;color:var(--bs-secondary-color);">' + item.jobs_count + ' job' + (item.jobs_count !== 1 ? 's' : '') + '</div>'
            + '</div>';

        div.onclick = function() {
            searchInput.value = item.name;
            hideSuggestions();
            document.getElementById('filter-form').submit();
        };
        suggestionsBox.appendChild(div);
    });

    suggestionsBox.style.display = 'block';
}

function highlightMatch(text, q) {
    var escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    return text.replace(new RegExp('(' + escaped + ')', 'gi'), '<mark style="background:rgba(255,193,7,.4);border-radius:2px;padding:0 1px;">$1</mark>');
}

function highlightSugg(items) {
    items.forEach(function(item, idx) {
        item.style.background = idx === selectedSuggIdx ? 'rgba(13,110,253,.1)' : '';
    });
    if (selectedSuggIdx >= 0 && items[selectedSuggIdx]) {
        // Peek the name into input
        var name = currentSuggestions[selectedSuggIdx] ? currentSuggestions[selectedSuggIdx].name : '';
        if (name) searchInput.value = name;
    }
}

function hideSuggestions() {
    suggestionsBox.style.display = 'none';
    selectedSuggIdx = -1;
}

// ── Row expand ────────────────────────────────────────────────────
var expandedRow = null;

function toggleExpand(jobId, rowEl) {
    var expandRow = document.getElementById('expand-'+jobId);

    if (expandedRow === jobId) {
        expandRow.classList.remove('show');
        rowEl.classList.remove('expanded');
        expandedRow = null;
        return;
    }

    if (expandedRow !== null) {
        var pr = document.getElementById('row-'+expandedRow);
        var pe = document.getElementById('expand-'+expandedRow);
        if (pr) pr.classList.remove('expanded');
        if (pe) pe.classList.remove('show');
    }

    expandRow.classList.add('show');
    rowEl.classList.add('expanded');
    expandedRow = jobId;

    setTimeout(function(){
        expandRow.scrollIntoView({behavior:'smooth',block:'nearest'});
    }, 60);
}
</script>
@endpush