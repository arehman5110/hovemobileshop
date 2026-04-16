<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosSale extends Model
{
    protected $fillable = [
        'customer_id','customer_name','subtotal','discount_type',
        'discount_value','voucher_code','voucher_amount','total',
        'paid','change_given','payment_method','payment_notes',
        'status','notes','user_id',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function items(): HasMany      { return $this->hasMany(PosSaleItem::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }

    public function customerLabel(): string
    {
        return $this->customer?->name ?? $this->customer_name ?? 'Walk-in';
    }
}

class PosSaleItem extends Model
{
    protected $fillable = [
        'pos_sale_id','type','name','quantity','unit_price','total','ref_id',
    ];

    public function sale(): BelongsTo { return $this->belongsTo(PosSale::class); }
}