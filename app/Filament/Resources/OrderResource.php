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
    protected static ?string $navigationGroup = 'Orders';

    // ================= FORM =================
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // 👤 اختيار المستخدم
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),

                // 📦 عناصر الطلب
                Repeater::make('items')
                    ->relationship()
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
                            ->required(),

                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
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
                ->label('User'),

            Tables\Columns\TextColumn::make('total')
                ->money('LYD'),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime(),

        ]);
    }

    // ================= BEFORE CREATE 🔥 =================
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $user = User::find($data['user_id']);

        // 🧮 حساب المجموع
        $total = collect($data['items'])->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        // ❌ منع إذا الرصيد غير كافي
        if ($user->balance < $total) {

            Notification::make()
                ->title('الرصيد غير كافي')
                ->danger()
                ->send();

            throw new \Exception('Balance not enough');
        }

        // حفظ المجموع
        $data['total'] = $total;

        return $data;
    }

    // ================= AFTER CREATE 🔥 =================
    public static function afterCreate($record)
    {
        // خصم الرصيد + تسجيل transaction
        app(\App\Services\PurchaseService::class)
            ->buy(
                $record->user,
                $record->total,
                'Order #' . $record->id
            );
    }

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
