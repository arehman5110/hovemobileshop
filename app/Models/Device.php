<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = ['job_id','name','imei','color','warranty','note','notes','sort_order'];

    public function job(): BelongsTo       { return $this->belongsTo(Job::class); }
    public function repairItems(): HasMany { return $this->hasMany(RepairItem::class); }
    public function repairs(): HasMany     { return $this->hasMany(RepairItem::class); }

    public function subtotal(): float   { return (float) $this->repairItems()->sum('price'); }
    public function totalPrice(): float { return $this->subtotal(); }

    public function getNoteAttribute($value): ?string
    {
        return $value ?? $this->attributes['notes'] ?? null;
    }
}
