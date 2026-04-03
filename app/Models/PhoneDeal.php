<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhoneDeal extends Model
{
    protected $fillable = [
        'customer_id','type','status','payment_type',
        'notes','id_card_path','terms_agreed','terms_snapshot','deal_date'
    ];

    protected $casts = ['deal_date'=>'date','terms_agreed'=>'boolean'];

    public function customer(): BelongsTo  { return $this->belongsTo(Customer::class); }
    public function items(): HasMany       { return $this->hasMany(DealItem::class); }
    public function payments(): HasMany    { return $this->hasMany(DealPayment::class); }

    public function totalPrice(): float    { return (float) $this->items()->sum('price'); }
    public function totalPaid(): float     { return (float) $this->payments()->sum('amount'); }
    public function balanceDue(): float    { return max(0, $this->totalPrice() - $this->totalPaid()); }
    public function isPaidInFull(): bool   { return $this->totalPrice() > 0 && $this->totalPaid() >= $this->totalPrice(); }

    public function typeBadgeClass(): string { return $this->type==='buy'?'badge-info':'badge-success'; }
    public function typeLabel(): string      { return $this->type==='buy'?'📥 Buying':'📤 Selling'; }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'Completed'                    => 'badge-success',
            'Accepted','Deposit Paid','Reserved' => 'badge-info',
            'Pending Check','Offered'      => 'badge-warning',
            'Rejected'                     => 'badge-danger',
            default                        => 'badge-secondary',
        };
    }

    public static function buyStatuses(): array  { return ['Pending Check','Offered','Accepted','Rejected','Completed']; }
    public static function sellStatuses(): array { return ['Reserved','Deposit Paid','Completed']; }

    public function deviceSummary(): string
    {
        return $this->items->map(fn($i) => $i->fullName())->join(', ') ?: '—';
    }
}
