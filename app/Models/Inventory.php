<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
     protected $fillable = ['product_name', 'sku', 'stock', 'price'];

    // This item can appear in many order items
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // This item can also appear in many cart items
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
