{{--
    Partial: jobs/_customer_preview.blade.php
    Shared between jobs/create and jobs/edit.

    Variables:
      $selectId     — id of the <select> element  (default: 'customer_select')
      $previewId    — id prefix for preview elements (default: 'customer')
      $customer     — optional existing Customer model (edit mode)
--}}
@php
    $selectId  = $selectId  ?? 'customer_select';
    $previewId = $previewId ?? 'customer';
    $hasCustomer = isset($customer) && $customer;
@endphp

<div id="{{ $previewId }}-preview" class="mt-3" style="{{ $hasCustomer ? '' : 'display:none;' }}">
    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:var(--bs-tertiary-bg);border:1px solid var(--bs-border-color);">

        {{-- Avatar --}}
        <div id="{{ $previewId }}-avatar"
            style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#0d6efd,#0099ff);
                   display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:#fff;flex-shrink:0;">
            {{ $hasCustomer ? strtoupper(substr($customer->name,0,1)) : '' }}
        </div>

        {{-- Name + contact --}}
        <div class="flex-grow-1 min-width-0">
            <div class="fw-bold" id="{{ $previewId }}-preview-name" style="font-size:14px;">
                {{ $hasCustomer ? $customer->name : '' }}
            </div>
            <div class="d-flex flex-wrap gap-3 mt-1">
                <span id="{{ $previewId }}-preview-phone" class="text-secondary" style="font-size:11px;{{ ($hasCustomer && $customer->phone) ? '' : 'display:none;' }}">
                    <i class="bi bi-telephone me-1"></i><span>{{ $hasCustomer ? $customer->phone : '' }}</span>
                </span>
                <span id="{{ $previewId }}-preview-email" class="text-secondary" style="font-size:11px;{{ ($hasCustomer && $customer->email) ? '' : 'display:none;' }}">
                    <i class="bi bi-envelope me-1"></i><span>{{ $hasCustomer ? $customer->email : '' }}</span>
                </span>
                <span id="{{ $previewId }}-preview-address" class="text-secondary" style="font-size:11px;{{ ($hasCustomer && $customer->address) ? '' : 'display:none;' }}">
                    <i class="bi bi-geo-alt me-1"></i><span>{{ $hasCustomer ? $customer->address : '' }}</span>
                </span>
            </div>
        </div>

        {{-- Stats + actions --}}
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <span id="{{ $previewId }}-history-loading" style="display:none;">
                <span class="spinner-border spinner-border-sm text-secondary"></span>
            </span>
            <div id="{{ $previewId }}-history-stats" style="display:none;text-align:right;">
                <div style="font-size:11px;font-weight:700;font-family:'Syne',sans-serif;" id="{{ $previewId }}-stat-spent" class="text-success"></div>
                <div style="font-size:10px;" id="{{ $previewId }}-stat-due" class="text-danger"></div>
            </div>
            <a id="{{ $previewId }}-view-jobs" href="#" target="_blank"
                class="btn btn-sm btn-outline-secondary" style="font-size:11px;display:none;"></a>
            <button type="button" onclick="openEditCustomerModal('{{ $selectId }}')"
                class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil"></i>
            </button>
        </div>
    </div>
</div>
