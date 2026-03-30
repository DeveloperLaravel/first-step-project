<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Events\Login;
use App\Listeners\UpdateLastLogin;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
use SoftDeletes;

    /** @use HasFactory<UserFactory> */
    use HasFactory,HasRoles, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'deactivated_at',
        'last_login_at',
        'last_login_ip',
        'last_seen_at',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $listen = [
    Login::class => [
        UpdateLastLogin::class,
    ],
];
 protected $casts = [
        'is_active' => 'boolean',
        'deactivated_at' => 'datetime',
    ];

    // 🔥 هنا المكان الصحيح
    protected static function booted()
    {
        static::updating(function ($user) {

            // إذا تغيرت حالة التفعيل
            if ($user->isDirty('is_active')) {

                if ($user->is_active) {
                    // ✅ تم التفعيل
                    $user->deactivated_at = null;
                } else {
                    // ❌ تم التعطيل
                    $user->deactivated_at = now();
                }

            }

        });
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

public function getFilamentName(): string
{
    return $this->name . ' 🔥';
}
public function getFilamentAvatarUrl(): ?string
{
    return $this->avatar
        ? asset('storage/' . $this->avatar)
        : 'https://ui-avatars.com/api/?name=' . $this->name;
}
}
