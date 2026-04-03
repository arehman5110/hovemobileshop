<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Screen extends Model
{
    protected $fillable = [
        'category_id', 'model', 'screen_type', 'quality',
        'stock', 'cost_price', 'sell_price', 'notes'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }

    public function usedCount(): int
    {
        return $this->repairs()->whereIn('status', ['In Progress', 'Completed'])->count();
    }

    public function remainingStock(): int
    {
        return max(0, $this->stock - $this->usedCount());
    }

    public function stockStatusClass(): string
    {
        $remaining = $this->remainingStock();
        if ($remaining <= 0) return 'danger';
        if ($remaining <= 2) return 'warning';
        return 'success';
    }
}
