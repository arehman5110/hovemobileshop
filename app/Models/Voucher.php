<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_spend',
        'expires_at', 'is_active', 'uses_limit', 'uses_count', 'notes', 'customer_id'
    ];

    protected $casts = [
        'expires_at' => 'date',
        'is_active'  => 'boolean',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }

    public function isValid(float $spend = 0, ?int $customerId = null): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->uses_limit && $this->uses_count >= $this->uses_limit) return false;
        if ($spend < $this->min_spend) return false;
        // If voucher is assigned to a specific customer, check it matches
        if ($this->customer_id && $customerId && $this->customer_id !== $customerId) return false;
        return true;
    }

    public function discountFor(float $subtotal): float
    {
        if ($this->type === 'percent') {
            return round($subtotal * ($this->value / 100), 2);
        }
        return min((float) $this->value, $subtotal);
    }

    public function statusLabel(): string
    {
        if (!$this->is_active) return 'Inactive';
        if ($this->expires_at && $this->expires_at->isPast()) return 'Expired';
        if ($this->uses_limit && $this->uses_count >= $this->uses_limit) return 'Used Up';
        return 'Active';
    }

    public function statusBadgeClass(): string
    {
        return match($this->statusLabel()) {
            'Active'  => 'badge-success',
            default   => 'badge-danger',
        };
    }
}
