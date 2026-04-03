<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'order_number',
        'payment_status',
        'notes',
    ];

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
public function transaction()
{
    return $this->hasOne(Transaction::class);
}
    // 🧮 حساب المجموع
    public function calculateTotal()
    {
        return $this->items->sum(function ($item) {
        $item->price * $item->quantity;

        });

    }

    // 🔥 الاحتراف الحقيقي هنا
protected static function boot()
{
    parent::boot();

    static::creating(function ($order) {
        $order->order_number =
            'ORD-' . date('Y') . '-' . str_pad(self::count() + 1, 5, '0', STR_PAD_LEFT);
    });
}

}
