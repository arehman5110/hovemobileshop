<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PhoneModel extends Model
{
    protected $fillable = ['brand','name','sort_order'];

    public function parts()
    {
        return $this->hasMany(Part::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->brand . ' ' . $this->name;
    }
}
