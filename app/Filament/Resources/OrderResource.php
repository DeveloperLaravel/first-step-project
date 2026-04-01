<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;

use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
 protected static ?int $navigationSort = 3;
protected static ?string $navigationGroup = 'ادارت الطلبات';

    // ================= FORM =================
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // 👤 اختيار المستخدم
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->label('المستخدم')
                    ->required(),

                // 📦 عناصر الطلب
                Repeater::make('items')
                    ->relationship()
                              ->label('عناصر الطلب')
                    ->schema([

                        Select::make('medicine_id')
                            ->relationship('medicine', 'name')
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $medicine = \App\Models\Medicine::find($state);
                                if ($medicine) {
                                    $set('price', $medicine->price);
                                }
                            }),

                        TextInput::make('price')
                            ->numeric()
                              ->label('السعر')
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                              ->label('الكمية')
                            ->required(),

                    ])
                    ->columns(3)
                    ->required(),

            ]);
    }

    // ================= TABLE =================
    public static function table(Table $table): Table
    {
        return $table->columns([

            Tables\Columns\TextColumn::make('user.name')
                ->label('المستخدم'),

            Tables\Columns\TextColumn::make('total')
              ->label('المحموع')
                ->money('LYD'),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime(),

        ]);
    }

    // ================= BEFORE CREATE 🔥 =================


    // ================= AFTER CREATE 🔥 =================

    // ================= PAGES =================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
