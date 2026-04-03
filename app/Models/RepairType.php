<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairType extends Model
{
    protected $fillable = ['name', 'icon', 'color'];

    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class);
    }
}
