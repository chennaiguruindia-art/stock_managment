<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';
    protected $guarded = [];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function nextOrderId(): string
    {
        $last = self::orderByDesc('id')->value('order_id');
        $seq = $last ? (int) substr($last, 4) + 1 : 1;

        return 'ORD-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
