<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosStock extends Model
{
    protected $table    = 'pos_stock';
    protected $fillable = [
        'category_id', 'model_id', 'name', 'variant',
        'sku', 'cost_price', 'sell_price', 'stock',
        'low_stock_alert', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function category()
    {
        return $this->belongsTo(PosCategory::class, 'category_id');
    }

    public function model()
    {
        return $this->belongsTo(PosModel::class, 'model_id');
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->low_stock_alert && $this->stock > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function getDisplayNameAttribute(): string
    {
        $parts = [$this->name];
        if ($this->variant) $parts[] = '('.$this->variant.')';
        return implode(' ', $parts);
    }

    public function getMarginAttribute(): float
    {
        if (!$this->cost_price || !$this->sell_price) return 0;
        return round((($this->sell_price - $this->cost_price) / $this->sell_price) * 100, 1);
    }
}
