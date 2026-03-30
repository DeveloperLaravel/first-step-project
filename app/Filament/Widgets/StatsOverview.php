<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StatsOverview extends BaseWidget
{

     protected function getCards(): array
    {
        return [

            Card::make('عدد المستخدمين', User::count())
                ->description('إجمالي المستخدمين في النظام')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Card::make('المستخدمين النشطين', User::where('is_active', true)->count())
                ->description('إجمالي النشطين')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Card::make('المستخدمين الموقوفين', User::where('is_active', false)->count())
                ->description('إجمالي الموقوفين')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Card::make('عدد الأدوار', Role::count())
                ->description('الأدوار في النظام')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('warning'),
// Card::make('Users Growth', User::count())
//     ->description('+12% this month'),
            Card::make('عدد الصلاحيات', Permission::count())
                ->description('الصلاحيات في النظام')
                ->descriptionIcon('heroicon-m-lock-closed')
                ->color('info'),

        ];
    }
}
