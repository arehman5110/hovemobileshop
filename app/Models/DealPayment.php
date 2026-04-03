<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealPayment extends Model
{
    protected $fillable = ['phone_deal_id','amount','payment_type','payment_label','notes','paid_date'];

    protected $casts = ['paid_date' => 'date'];

    public function deal(): BelongsTo { return $this->belongsTo(PhoneDeal::class, 'phone_deal_id'); }

    public function typeIcon(): string
    {
        return match($this->payment_type) {
            'Cash'  => '💵',
            'Card'  => '💳',
            'Bank'  => '🏦',
            'Trade' => '🔄',
            default => '💰',
        };
    }
}
