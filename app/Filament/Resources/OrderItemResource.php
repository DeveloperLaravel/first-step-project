<?php

namespace App\Filament\Resources;
use App\Filament\Resources\OrderItemResource\Pages;

use App\Models\OrderItem;
use App\Models\Product;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class OrderItemResource extends Resource
{
    protected static ?string $model = OrderItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
      protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'إدارة الطلبات';

    protected static ?string $navigationLabel = 'عناصر الطلبات';
    protected static ?string $pluralModelLabel = 'عناصر الطلبات';
    protected static ?string $modelLabel = 'عنصر طلب';

    // ================= FORM =================
    public static function form(Form $form): Form
    {
        return $form->schema([

            Grid::make(2)->schema([

                Select::make('order_id')
                    ->label('الطلب')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->required(),

                Select::make('product_id')
                    ->label('المنتج')
                    ->relationship('product', 'name')
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $product = Product::find($state);
                        if ($product) {
                            $set('price', $product->price);
                        }
                    })
                    ->required(),

                TextInput::make('quantity')
                    ->label('الكمية')
                    ->numeric()
                    ->default(1)
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('subtotal', $state * $get('price'));
                    }),

                TextInput::make('price')
                    ->label('السعر')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $set('subtotal', $state * $get('quantity'));
                    }),

                TextInput::make('subtotal')
                    ->label('الإجمالي')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(true),

                Select::make('card_id')
                    ->label('الكرت (اختياري)')
                    ->relationship('card', 'code')
                    ->searchable()
                    ->nullable(),

            ]),
        ]);
    }

    // ================= TABLE =================
    public static function table(Table $table): Table
    {
        return $table->columns([

            TextColumn::make('order.order_number')
                ->label('رقم الطلب')
                ->searchable(),

            TextColumn::make('product.name')
                ->label('المنتج')
                ->searchable(),

            TextColumn::make('quantity')
                ->label('الكمية'),

            TextColumn::make('price')
                ->label('السعر')
                ->money('USD'),

            TextColumn::make('subtotal')
                ->label('الإجمالي')
                ->money('LYD'),

            TextColumn::make('card.code')
                ->label('الكرت')
                ->default('-'),

            TextColumn::make('created_at')
                ->label('تاريخ')
                ->dateTime(),

        ])
        ->filters([
            Tables\Filters\SelectFilter::make('order_id')
                ->relationship('order', 'order_number')
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    // ================= AUTO CALCULATE =================




    // ================= PAGES =================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderItems::route('/'),
            'create' => Pages\CreateOrderItem::route('/create'),
            'edit' => Pages\EditOrderItem::route('/{record}/edit'),
        ];
    }
}
