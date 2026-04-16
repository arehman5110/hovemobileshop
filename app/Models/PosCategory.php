<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosCategory extends Model
{
    protected $fillable = ['name', 'icon', 'sort_order'];

    public function stock()
    {
        return $this->hasMany(PosStock::class, 'category_id');
    }

    public function stockCount(): int
    {
        return $this->stock()->sum('stock');
    }
}
