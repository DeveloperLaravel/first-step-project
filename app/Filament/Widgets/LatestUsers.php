<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Table;

class LatestUsers extends BaseWidget
{
    protected static ?string $heading = 'آخر المستخدمين';

    public function table(Table $table): Table
    {
        return $table
            ->query(User::latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم'),
                Tables\Columns\TextColumn::make('email')->label('البريد'),
                Tables\Columns\BadgeColumn::make('is_active')
                    ->label('الحالة')
                    ->formatStateUsing(fn ($state) => $state ? 'نشط' : 'موقوف')
                    ->colors([
                        'success' => fn ($state) => $state,
                        'danger' => fn ($state) => !$state,
                    ]),
            ]);
    }
}
