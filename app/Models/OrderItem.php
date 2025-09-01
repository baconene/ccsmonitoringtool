<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'inventory_id', 'quantity', 'price'];

    // Belongs to an order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Belongs to inventory
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
