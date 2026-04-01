<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderItemResource\Pages;
use App\Models\OrderItem;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    /* =========================
        NAVIGATION (عربي)
    ========================== */
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $label = 'عنصر طلب';
    protected static ?string $pluralLabel = 'عناصر الطلبات';
 protected static ?int $navigationSort = 4;
protected static ?string $navigationGroup = 'ادارت الطلبات';

    /* =========================
        FORM (إنشاء / تعديل)
    ========================== */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Select::make('order_id')
                    ->label('رقم الطلب')
                    ->relationship('order', 'id')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('medicine_id')
                    ->label('الدواء')
                    ->relationship('medicine', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $medicine = \App\Models\Medicine::find($state);

                        if ($medicine) {
                            $set('price', $medicine->price);
                        }
                    }),

                TextInput::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->default(1),

                TextInput::make('price')
                    ->label('سعر الوحدة')
                    ->numeric()
                    ->required()
                    ->prefix('د.ل'),

            ]);
    }

    /* =========================
        TABLE (عرض البيانات)
    ========================== */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('order.id')
                    ->label('رقم الطلب')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('medicine.name')
                    ->label('الدواء')
                    ->sortable()
                    ->searchable()
                    ->wrap(),

                TextColumn::make('quantity')
                    ->label('الكمية')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('price')
                    ->label('سعر الوحدة')
                    ->money('LYD')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('الإجمالي')
                    ->state(fn ($record) => $record->quantity * $record->price)
                    ->money('LYD')
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(),

            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل')
                    ->icon('heroicon-o-pencil-square'),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف')
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->label('حذف المحدد'),
            ]);
    }

    /* =========================
        PAGES
    ========================== */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
