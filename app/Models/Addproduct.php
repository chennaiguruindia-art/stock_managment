<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addproduct extends Model
{
    protected $table = 'addproducts';
    protected $guarded = [];

    protected $casts = [
        'has_dupatta' => 'boolean',
        'stock' => 'integer',
        'dupatta_discount' => 'float',
    ];
}
