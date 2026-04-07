{{--
    Partial: jobs/_payments_list.blade.php
    Reusable payments list with Add/Edit/Delete buttons.
    Variables: $job — Job model with payments loaded
--}}
<div class="mb-2">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="fw-semibold small">💳 Payments</span>
        <button type="button" class="btn btn-success btn-sm" onclick="openAddPaymentModal()"
            style="border-radius:8px;"><i class="bi bi-plus-lg me-1"></i>Add</button>
    </div>

    @if($job->payments->isEmpty())
    <div class="text-secondary small text-center py-2">No payments yet</div>
    @else
    <div class="d-flex flex-column gap-1">
        @foreach($job->payments as $pmt)
        @php
            $isSplit   = $pmt->notes && str_starts_with(trim($pmt->notes), '[');
            $typeLabel = $isSplit ? '✂️ Split' : $pmt->payment_type;
        @endphp
        <div class="d-flex align-items-center justify-content-between p-2 rounded-2"
            style="background:var(--bs-tertiary-bg);font-size:12px;">
            <div>
                <span class="fw-semibold">{{ $typeLabel }}</span>
                @if($isSplit)
                @php try { $sp = json_decode($pmt->notes, true); } catch(\Exception $e){ $sp = []; } @endphp
                <div class="text-secondary" style="font-size:11px;">
                    @foreach($sp ?? [] as $s)
                        {{ ($s['type'] ?? '').': £'.number_format($s['amount'] ?? 0, 2).($loop->last ? '' : ' + ') }}
                    @endforeach
                </div>
                @endif
                <div class="text-secondary" style="font-size:10px;">
                    {{ $pmt->created_at->format('d M Y') }}
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <span class="fw-semibold text-success">£{{ number_format($pmt->amount, 2) }}</span>
                @if(!$isSplit)
                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1"
                    style="font-size:10px;border-radius:4px;"
                    onclick="openEditPaymentModal({{ $pmt->id }}, '{{ $pmt->payment_type }}', {{ $pmt->amount }})">
                    <i class="bi bi-pencil"></i>
                </button>
                @else
                <button type="button" class="btn btn-xs btn-outline-secondary py-0 px-1"
                    style="font-size:10px;border-radius:4px;"
                    onclick="openEditSplitModal({{ $pmt->id }}, {{ $pmt->amount }}, {{ json_encode($pmt->notes) }})">
                    <i class="bi bi-pencil"></i>
                </button>
                @endif
                <form method="POST" action="{{ route('payments.destroy', $pmt) }}" class="d-inline"
                    onsubmit="return confirm('Delete this payment?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-1"
                        style="font-size:10px;border-radius:4px;"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
