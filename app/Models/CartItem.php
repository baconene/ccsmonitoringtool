<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'inventory_id', 'quantity'];

    // Belongs to a cart
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    // Belongs to an inventory item
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}