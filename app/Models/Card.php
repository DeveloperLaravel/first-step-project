<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Card extends Model
{
    protected $fillable = [
        'code',
        'amount',
        'hash',
        'status',
        'created_by',
        'used_by',
        'expires_at',
        'used_at',
        'qr_path',
    ];

    // 🎯 حالات الكرت (أفضل من string مباشر)
    const STATUS_ACTIVE = 'active';
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';

   protected static function boot()
    {
        parent::boot();

        // 🔥 قبل الإنشاء
        static::creating(function ($card) {

            $card->code = self::generateUniqueCode();
            $card->hash = hash('sha256', $card->code);
            $card->created_by = Auth::user()->id;
            $card->expires_at = now()->addMonths(3);
        });

        // 🚀 بعد الإنشاء (هنا QR)
        static::created(function ($card) {

            $card->generateQr();
        });
    }


    public static function generateUniqueCode(): string
    {
        do {
            $code =      str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) . '-' .
            str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) . '-' .
            str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT) . '-' .
            str_pad(random_int(0, 999), 3, '0', STR_PAD_LEFT);
            //  strtoupper(
            //     Str::random(4) . '-' .
            //     Str::random(4) . '-' .
            //     Str::random(4) . '-' .
            //     Str::random(3)
            // );
        } while (self::where('code', $code)->exists());

        return $code;
    }



     // 🎯 توليد QR (دالة منفصلة احترافية)
    public function generateQr()
    {
        $path = "images/{$this->code}.svg";

        Storage::disk('public')->put(
            $path,
            QrCode::format('svg')
                ->size(200)
                ->generate($this->code)
        );

        // ⚠️ بدون loop لا نهائي
        $this->updateQuietly([
            'qr_path' => $path
        ]);
    }
    // العلاقات
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
