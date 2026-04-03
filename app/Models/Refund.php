<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $fillable = ['job_id', 'amount', 'reason', 'refund_method'];

    public function job(): BelongsTo { return $this->belongsTo(Job::class); }

    public function methodIcon(): string
    {
        return match($this->refund_method) {
            'Cash'         => '💵',
            'Card'         => '💳',
            'Store Credit' => '🏪',
            default        => '💰',
        };
    }
}
