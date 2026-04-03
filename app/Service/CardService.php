<?php

namespace App\Service;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;
use App\Models\User;
use App\Models\Transaction;

class CardService
{
    public function redeem(string $code): array
    {
        $user = Auth::user();

        if (!$user) {
            throw new \Exception('يجب تسجيل الدخول أولاً');
        }

        return DB::transaction(function () use ($code, $user) {

            // 🔒 قفل الصف لمنع التكرار
            $card = Card::where('code', $code)
                ->lockForUpdate()
                ->first();

            if (!$card) {
                throw new \Exception('الكرت غير موجود');
            }

            if ($card->status !== 'active') {
                throw new \Exception('الكرت مستخدم أو غير صالح');
            }

            if ($card->expires_at && now()->greaterThan($card->expires_at)) {
                throw new \Exception('الكرت منتهي الصلاحية');
            }

            // 💰 تحديث الرصيد (Atomic)
            $user->increment('balance', $card->amount);

            // 🔄 تحديث حالة الكرت
            $card->update([
                'status'   => 'used',
                'used_by'  => $user->id,
                'used_at'  => now(),
            ]);

            // 🧾 تسجيل العملية
            // Transaction::create([
            //     'user_id' => $user->id,
            //     'type'    => 'recharge',
            //     'amount'  => $card->amount,
            //     'description' => 'شحن باستخدام كرت: ' . $card->code,

            // ]);

            // 🔄 تحديث المستخدم
            // $user->refresh();

            return [
                'success' => true,
                'amount'            => $card->amount,
                'balance'           => $user->balance,
                'amount_formatted'  => number_format($card->amount, 2),
                'balance_formatted' => number_format($user->balance, 2),
            ];
        });
    }


}
