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

    /** Units handed back across every line of this order. */
    public function returnedQty(): int
    {
        return (int) $this->items->sum('returned_qty');
    }

    /** Units still counted as sold. */
    public function netQty(): int
    {
        return (int) $this->items->sum(fn (OrderItem $i) => $i->netQty());
    }

    /** Value of the returned units. */
    public function returnedTotal(): float
    {
        return round((float) $this->items->sum(fn (OrderItem $i) => $i->returnedAmount()), 2);
    }

    /** Order value after returns — what sales history should count. */
    public function netTotal(): float
    {
        return round(max(0, (float) $this->total - $this->returnedTotal()), 2);
    }

    /** none | partial | full */
    public function returnState(): string
    {
        $sold = (int) $this->items->sum('qty');
        $back = $this->returnedQty();

        if ($sold <= 0 || $back <= 0) {
            return 'none';
        }

        return $back >= $sold ? 'full' : 'partial';
    }

    public static function nextOrderId(): string
    {
        $last = self::orderByDesc('id')->value('order_id');
        $seq = $last ? (int) substr($last, 4) + 1 : 1;

        return 'ORD-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
