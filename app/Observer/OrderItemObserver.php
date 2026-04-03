<?php
namespace App\Observer;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class OrderItemObserver
{
    public function created(OrderItem $item): void
    {
        DB::transaction(function () use ($item) {

            $medicine = Product::lockForUpdate()->find($item->medicine_id);

            if (!$medicine) {
                throw ValidationException::withMessages([
                    'medicine' => 'الدواء غير موجود'
                ]);
            }

            if ($medicine->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'stock' => 'الكمية غير متوفرة في المخزون'
                ]);
            }

            $medicine->decrement('stock', $item->quantity);
        });
    }

    public function updated(OrderItem $item): void
    {
        DB::transaction(function () use ($item) {

            $medicine = Product::lockForUpdate()->find($item->medicine_id);

            $oldQty = $item->getOriginal('quantity');
            $newQty = $item->quantity;

            $difference = $newQty - $oldQty;

            if ($difference > 0) {
                if ($medicine->stock < $difference) {
                    throw ValidationException::withMessages([
                        'stock' => 'لا يوجد مخزون كافي للتعديل'
                    ]);
                }

                $medicine->decrement('stock', $difference);
            } else {
                $medicine->increment('stock', abs($difference));
            }
        });
    }

    public function deleted(OrderItem $item): void
    {
        DB::transaction(function () use ($item) {

            $medicine = Product::lockForUpdate()->find($item->product_id);

            $medicine->increment('stock', $item->quantity);
        });
    }
}
