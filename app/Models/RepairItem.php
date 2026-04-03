<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepairItem extends Model
{
    protected $fillable = [
        'device_id', 'repair_type_id', 'part_id', 'issue', 'price', 'status'
    ];

    public function device(): BelongsTo     { return $this->belongsTo(Device::class); }
    public function repairType(): BelongsTo { return $this->belongsTo(RepairType::class); }
    public function part(): BelongsTo       { return $this->belongsTo(Part::class); }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'Completed'     => 'badge-success',
            'In Progress'   => 'badge-warning',
            'Waiting Parts' => 'badge-danger',
            'Cancelled'     => 'badge-secondary',
            default         => 'badge-secondary',
        };
    }
}
