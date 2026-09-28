<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $guarded = [];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Addproduct::class);
    }

    /** Units the customer still holds (sold qty minus units handed back). */
    public function netQty(): int
    {
        return max(0, (int) $this->qty - (int) $this->returned_qty);
    }

    /** Units handed back on this line. */
    public function returnedQty(): int
    {
        return (int) $this->returned_qty;
    }

    /** Value of the returned units at the price actually charged per unit. */
    public function returnedAmount(): float
    {
        return round((float) $this->price * (int) $this->returned_qty, 2);
    }

    /** Line value that still counts as a sale. */
    public function netAmount(): float
    {
        return round((float) $this->price * $this->netQty(), 2);
    }
}
