<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use App\Helpers\JobStatus;

class Job extends Model
{
    protected $fillable = [
        'customer_id', 'date_in', 'date_out', 'status', 'device_location', 'notes',
        'discount_type', 'discount_value', 'voucher_code', 'voucher_amount'
    ];

    protected $casts = [
        'date_in'  => 'date',
        'date_out' => 'date',
    ];

    // ── Relationships ─────────────────────────────────────────────
    public function customer(): BelongsTo  { return $this->belongsTo(Customer::class); }
    public function devices(): HasMany     { return $this->hasMany(Device::class)->orderBy('sort_order'); }
    public function payments(): HasMany    { return $this->hasMany(Payment::class); }
    public function refunds(): HasMany     { return $this->hasMany(\App\Models\Refund::class); }

    public function repairItems(): HasManyThrough
    {
        return $this->hasManyThrough(RepairItem::class, Device::class);
    }

    // ── Status helpers ────────────────────────────────────────────

    /** Get all available statuses from single source of truth */
    public static function statuses(): array
    {
        return JobStatus::all();
    }

    /** Get colour/icon config for this job's status */
    public function statusConfig(): array
    {
        return JobStatus::config($this->status);
    }

    /** Bootstrap badge class for this status */
    public function statusBadgeClass(): string
    {
        return $this->statusConfig()['badge'];
    }

    /** Bootstrap text colour class for this status */
    public function statusTextClass(): string
    {
        return $this->statusConfig()['text'];
    }

    /** Status icon emoji */
    public function statusIcon(): string
    {
        return $this->statusConfig()['icon'];
    }

    // ── Financial calculations ────────────────────────────────────
    public function subtotal(): float
    {
        return (float) $this->repairItems()->sum('price');
    }

    public function discountAmount(): float
    {
        if (!$this->discount_type || $this->discount_value <= 0) return 0;
        if ($this->discount_type === 'percent') {
            return round($this->subtotal() * ($this->discount_value / 100), 2);
        }
        return (float) $this->discount_value;
    }

    public function totalAfterDiscount(): float
    {
        return max(0, $this->subtotal() - $this->discountAmount() - (float) $this->voucher_amount);
    }

    public function totalPaid(): float     { return (float) $this->payments()->sum('amount'); }
    public function totalRefunded(): float  { return (float) $this->refunds()->sum('amount'); }

    public function cashRefunded(): float
    {
        return (float) $this->refunds()->whereIn('refund_method', ['Cash', 'Card'])->sum('amount');
    }

    public function storeCreditIssued(): float
    {
        return (float) $this->refunds()->where('refund_method', 'Store Credit')->sum('amount');
    }

    public function balanceDue(): float
    {
        $netPaid = $this->totalPaid() - $this->cashRefunded();
        $settled = $netPaid + $this->storeCreditIssued();
        return max(0, $this->totalAfterDiscount() - $settled);
    }

    public function isPaidInFull(): bool
    {
        return $this->totalAfterDiscount() > 0 && $this->balanceDue() <= 0;
    }
}