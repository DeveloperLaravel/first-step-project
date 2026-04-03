<?php
namespace App\Service;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(User $user, array $items)
    {
        return DB::transaction(function () use ($user, $items) {

            $user = User::where('id', $user->id)
                ->lockForUpdate()
                ->first();

            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . time(),
                'total' => 0,
                'status' => 'pending',
            ]);

            $total = 0;

            foreach ($items as $item) {

                // 💡 هنا المكان الصحيح
                $product = Product::findOrFail($item['product_id']);

                $price = $product->price; // ✅ سعر حقيقي من قاعدة البيانات

                $subtotal = $item['quantity'] * $price;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $order->update(['total' => $total]);

            if ($user->balance < $total) {
                throw new \Exception('الرصيد غير كافي');
            }

            $user->balance -= $total;
            $user->save();

            Transaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'amount' => $total,
                'type' => 'purchase',
                'status' => 'completed',
                'description' => 'Order Purchase',
                'reference' => 'TRX-' . uniqid(),
            ]);

            return $order;
        });
    }

    public function payOrder($order)
{
    return DB::transaction(function () use ($order) {

        $user = User::where('id', $order->user_id)
            ->lockForUpdate()
            ->first();

        // ❌ لا تدفع مرتين
        if ($order->payment_status === 'paid') {
            throw new \Exception('تم دفع هذا الطلب مسبقًا');
        }

        $total = $order->items()->sum('subtotal');

        // 💰 تحقق من الرصيد
        if ($user->balance < $total) {
            throw new \Exception('الرصيد غير كافي');
        }

        // 📦 تحقق من المخزون
        foreach ($order->items as $item) {
            if ($item->product->stock < $item->quantity) {
                throw new \Exception("المنتج {$item->product->name} غير متوفر");
            }
        }


        // 🔥 خصم المخزون
        foreach ($order->items as $item) {
            $item->product->decrement('stock', $item->quantity);
        }

        // 💸 خصم الرصيد
        $user->decrement('balance', $total);

        // 💳 تسجيل العملية
        // Transaction::create([
        //     'user_id' => $user->id,
        //     'order_id' => $order->id,
        //     'amount' => $total,
        //     'type' => 'purchase',
        //     'status' => 'completed',
        //     'description' => 'دفع طلب رقم ' . $order->order_number,
        //     'reference' => 'TRX-' . uniqid(),
        // ]);

        // ✅ تحديث الطلب
        $order->update([
            'status' => 'completed',
            'payment_status' => 'paid',
        ]);

        return true;
    });
}
}
