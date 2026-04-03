<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermsCondition extends Model
{
    protected $fillable = ['type', 'title', 'content', 'is_active'];

    public static function activeFor(string $type): ?self
    {
        return static::where('type', $type)->where('is_active', true)->latest()->first();
    }
}
