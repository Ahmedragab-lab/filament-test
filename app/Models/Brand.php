<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';
    protected $guarded = [];
    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
