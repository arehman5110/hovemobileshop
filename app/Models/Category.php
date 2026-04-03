<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'color', 'icon', 'description'];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    // Keep screens() as alias so any leftover views don't break
    public function screens(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function totalStock(): int
    {
        return $this->parts()->sum('stock');
    }
}
