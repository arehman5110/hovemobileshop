<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Job extends Model
{
    protected $fillable = [
        'customer_id', 'date_in', 'date_out', 'status', 'notes',
        'discount_type', 'discount_value', 'voucher_code', 'voucher_amount'
    ];

    protected $casts = [
        'date_in'  => 'date',
        'date_out' => 'date',
    ];

    public function customer(): BelongsTo  { return $this->belongsTo(Customer::class); }
    public function devices(): HasMany     { return $this->hasMany(Device::class)->orderBy('sort_order'); }
    public function payments(): HasMany    { return $this->hasMany(Payment::class); }
    public function refunds(): HasMany     { return $this->hasMany(\App\Models\Refund::class); }

    // All repair items across all devices
    public function repairItems(): HasManyThrough
    {
        return $this->hasManyThrough(RepairItem::class, Device::class);
    }

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

    // Only Cash/Card refunds reduce what was paid (money left the shop)
    // Store Credit refunds settle the debt but the credit lives on the customer account
    public function cashRefunded(): float
    {
        return (float) $this->refunds()->whereIn('refund_method', ['Cash', 'Card'])->sum('amount');
    }

    public function storeCreditIssued(): float
    {
        return (float) $this->refunds()->where('refund_method', 'Store Credit')->sum('amount');
    }

    // Balance = what's owed after payments and any cash/card refunds given back
    // Store credit settles the balance (customer doesn't owe anymore) but cash didn't leave
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

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'Completed'     => 'badge-success',
            'In Progress'   => 'badge-warning',
            'Waiting Parts' => 'badge-danger',
            'Cancelled'     => 'badge-secondary',
            default         => 'badge-secondary',
        };
    }
}
