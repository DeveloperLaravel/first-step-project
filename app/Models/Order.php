<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Services\PurchaseService;
use Illuminate\Support\Facades\DB as FacadesDB;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'total',
        'status',
        'order_number',
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

    // 🧮 حساب الإجمالي بشكل آمن
    public function calculateTotal()
    {
        return $this->items()->sum(FacadesDB::raw('price * quantity'));
    }

    // ⚙️ Events
    protected static function booted()
    {
        // 🔢 توليد رقم الطلب
        static::creating(function ($order) {
            $order->order_number = 'ORD-' . date('Y') . '-' . strtoupper(Str::random(6));
        });

        // 💰 تنفيذ عملية الشراء بعد حفظ الطلب
        static::saved(function ($order) {

            // نحسب الإجمالي
            $total = $order->calculateTotal();

            // نحدث المجموع فقط لو تغير
            if ($order->total != $total) {
                $order->updateQuietly([
                    'total' => $total
                ]);
            }

            // تنفيذ الدفع فقط لو مكتمل
            if ($order->status === 'completed' && $total > 0) {

                app(PurchaseService::class)->buy(
                    $order->user,
                    $total,
                    'طلب رقم ' . $order->order_number
                );
            }
        });
    }
}
