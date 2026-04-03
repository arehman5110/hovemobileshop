<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = ['repair_id', 'job_id', 'payment_type', 'amount', 'notes'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }

    public function typeIcon(): string
    {
        return match($this->payment_type) {
            'Cash'  => '💵', 'Card' => '💳', 'Trade' => '🔄', default => '💰',
        };
    }

    public function badgeClass(): string
    {
        return match($this->payment_type) {
            'Cash'  => 'badge-success', 'Card' => 'badge-info', 'Trade' => 'badge-warning', default => 'badge-secondary',
        };
    }
}
