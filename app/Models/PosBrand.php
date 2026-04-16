<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosBrand extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function models()
    {
        return $this->hasMany(PosModel::class, 'brand_id');
    }
}
