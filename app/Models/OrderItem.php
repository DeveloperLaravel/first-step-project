<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'card_id'

    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
 public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function card()
{
    return $this->belongsTo(Card::class);
}
    //     public function getTotalAttribute()
    // {
    //     return $this->quantity * $this->price;
    // }
    public function items()
{
    return $this->hasMany(OrderItem::class);
}

protected static function booted()
{
    static::saving(function ($item) {
        $item->subtotal = $item->quantity * $item->price;
    });
}
}
