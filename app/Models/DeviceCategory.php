<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceCategory extends Model
{
    protected $fillable = ['name', 'icon'];

    // Categories are used on deal_items, not phone_deals directly
    public function dealItems()  { return $this->hasMany(DealItem::class); }
    public function inventory()  { return $this->hasMany(InventoryDevice::class); }

    // Count how many deal items use this category
    public function getDealItemsCountAttribute(): int
    {
        return $this->dealItems()->count();
    }
}
