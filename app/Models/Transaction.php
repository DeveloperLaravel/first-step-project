<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'type',
        'description'
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
{
    static::created(function ($transaction) {
        if ($transaction->type === 'recharge') {
            $transaction->user->increment('balance', $transaction->amount);
        }

        if ($transaction->type === 'purchase') {
            $transaction->user->decrement('balance', $transaction->amount);
        }
    });
}
}
