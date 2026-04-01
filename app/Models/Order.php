<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\PurchaseService;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
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

    // 🧮 حساب المجموع
    public function calculateTotal()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    // 🔥 الاحتراف الحقيقي هنا
    protected static function booted()
    {
        static::created(function ($order) {

            DB::transaction(function () use ($order) {

                // تحديث المجموع
                $total = $order->calculateTotal();
 // 🔥 2. تحديث الطلب
                $order->update([
                    'total' => $total
                ]);

                // خصم الرصيد + تسجيل transaction
                app(PurchaseService::class)->buy(
                    $order->user,
                    $total,
                    'Order #' . $order->id
                );

            });

        });
    }
}
