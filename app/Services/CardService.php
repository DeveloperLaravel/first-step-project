<?php


namespace App\Services;

use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class CardService
{
    public function redeem($code, $user)
    {
        return DB::transaction(function () use ($code, $user) {

            $card = Card::where('code', $code)->lockForUpdate()->first();

            if (!$card) {
                throw new Exception('الكرت غير موجود');
            }

            if ($card->status !== 'unused') {
                throw new Exception('الكرت مستخدم بالفعل');
            }

            if ($card->expires_at && now()->gt($card->expires_at)) {
                throw new Exception('الكرت منتهي');
            }

            // تحديث الكرت
            $card->update([
                'status' => 'used',
                'used_by' => $user->id,
                'used_at' => now(),
            ]);

            // تحديث الرصيد
            $user->increment('balance', $card->amount);

            // إنشاء transaction
            Transaction::create([
                'user_id' => $user->id,
                'amount' => $card->amount,
                'type' => 'recharge',
                'description' => 'Recharge via card: ' . $card->code,
            ]);

            return true;
        });
    }
}
