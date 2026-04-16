<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $fillable = ['category_id', 'name', 'part_type', 'quality', 'stock', 'cost_price', 'sell_price', 'notes'];

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
        return $this->repairs()
            ->whereIn('status', ['In Progress', 'Completed'])
            ->count();
    }

    public function remainingStock(): int
    {
        return max(0, $this->stock - $this->usedCount());
    }

    public function stockStatusClass(): string
    {
        $r = $this->remainingStock();
        if ($r <= 0) {
            return 'danger';
        }
        if ($r <= 2) {
            return 'warning';
        }
        return 'success';
    }

    public static function partTypes(): array
    {
        return [
            'Screen' => '🖥️',
            'Battery' => '🔋',
            'Charging Port' => '🔌',
            'Speaker' => '🔊',
            'Back Glass' => '🪟',
            'Camera' => '📷',
            'Microphone' => '🎙️',
            'Other' => '🔩',
        ];
    }
    public function productType()
    {
        return $this->belongsTo(\App\Models\ProductType::class);
    }
    public function phoneModel()
    {
        return $this->belongsTo(\App\Models\PhoneModel::class);
    }
}
