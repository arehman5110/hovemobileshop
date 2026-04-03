<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryDevice extends Model
{
    protected $fillable = [
        'device_category_id','brand','model','color','storage','imei',
        'condition','grade','cost_price','asking_price','status','notes'
    ];

    public function category(): BelongsTo { return $this->belongsTo(DeviceCategory::class, 'device_category_id'); }
    public function deals()               { return $this->hasMany(PhoneDeal::class); }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'Available' => 'badge-success',
            'Sold'      => 'badge-secondary',
            'Reserved'  => 'badge-warning',
            default     => 'badge-secondary',
        };
    }

    public function fullName(): string
    {
        return trim(($this->brand ?? '') . ' ' . $this->model);
    }
}
