<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Repair extends Model
{
    protected $fillable = [
        'customer_id', 'repair_type_id', 'part_id', 'date_in', 'date_out',
        'status', 'issue', 'total_price', 'notes'
    ];

    protected $casts = [
        'date_in'  => 'date',
        'date_out' => 'date',
    ];

    public function customer(): BelongsTo   { return $this->belongsTo(Customer::class); }
    public function repairType(): BelongsTo { return $this->belongsTo(RepairType::class); }
    public function part(): BelongsTo       { return $this->belongsTo(Part::class); }
    public function payments(): HasMany     { return $this->hasMany(Payment::class); }

    public function totalPaid(): float   { return (float) $this->payments()->sum('amount'); }
    public function balanceDue(): float  { return max(0, $this->total_price - $this->totalPaid()); }
    public function isPaidInFull(): bool { return $this->total_price > 0 && $this->totalPaid() >= $this->total_price; }

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
