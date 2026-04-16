<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosModel extends Model
{
    protected $table    = 'pos_models';
    protected $fillable = ['brand_id', 'name', 'sort_order'];

    public function brand()
    {
        return $this->belongsTo(PosBrand::class, 'brand_id');
    }

    public function stock()
    {
        return $this->hasMany(PosStock::class, 'model_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->brand?->name . ' ' . $this->name;
    }
}
