<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReturn extends Model
{
    protected $table = 'returns';
    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Addproduct::class);
    }
}
