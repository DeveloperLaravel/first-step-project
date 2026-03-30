<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class OnlineUsers extends BaseWidget
{
    protected function getCards(): array
    {
        $online = User::where('last_seen_at', '>=', now()->subMinutes(5))->count();

        return [
            Card::make('المستخدمين المتصلين الآن', $online)
                ->description(' متصل في اخر 5 دقائق')
                ->color('success'),
        ];
    }
}
