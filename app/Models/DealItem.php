<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealItem extends Model
{
    protected $fillable = [
        'phone_deal_id','inventory_device_id','device_category_id',
        'brand','model','color','storage','imei','condition','grade','warranty','price','notes'
    ];

    public function deal(): BelongsTo            { return $this->belongsTo(PhoneDeal::class, 'phone_deal_id'); }
    public function deviceCategory(): BelongsTo  { return $this->belongsTo(DeviceCategory::class); }
    public function inventoryDevice(): BelongsTo { return $this->belongsTo(InventoryDevice::class); }

    public function fullName(): string { return trim(($this->brand ?? '') . ' ' . $this->model); }
}
