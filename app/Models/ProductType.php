<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = ['name','icon','color','sort_order'];

    public function parts()
    {
        return $this->hasMany(Part::class);
    }
}
