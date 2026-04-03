<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address', 'notes'];

    public function repairs(): HasMany { return $this->hasMany(Repair::class); }
    public function jobs(): HasMany    { return $this->hasMany(Job::class); }

    // Total store credit issued across all jobs
    public function totalStoreCredit(): float
    {
        return (float) \App\Models\Refund::whereHas('job', fn($q) => $q->where('customer_id', $this->id))
            ->where('refund_method', 'Store Credit')
            ->sum('amount');
    }
}
