<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicineResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicineResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'المنتجات';
    protected static ?string $pluralModelLabel = 'المنتجات';
    protected static ?string $modelLabel = 'منتج';

    // ================= FORM =================
    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make(2)->schema([

                    TextInput::make('name')
                        ->label('اسم المنتج')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('price')
                        ->label('السعر')
                        ->numeric()
                        ->required(),

                    TextInput::make('stock')
                        ->label('المخزون')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    FileUpload::make('image')
                        ->label('صورة المنتج')
                        ->image()
                        ->directory('products')
                        ->disk('public'),

                ]),

                Forms\Components\Textarea::make('description')
                    ->label('الوصف')
                    ->rows(4)
                    ->columnSpanFull(),

            ]);
    }

    // ================= TABLE =================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->disk('public'),

                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable(),

                TextColumn::make('price')
                    ->label('السعر')
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('stock')
                    ->label('المخزون')
                    ->badge()
                    ->colors([
                        'danger' => fn ($state) => $state == 0,
                        'warning' => fn ($state) => $state < 20,
                        'success' => fn ($state) => $state >= 20,
                    ]),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime(),

            ])
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->label('مخزون قليل')
                    ->query(fn ($query) => $query->where('stock', '<', 10)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // ================= PAGES =================
    public static function getPages(): array
    {
        return [
            'index' =>  Pages\ListMedicines::route('/'),
            'create' =>  Pages\CreateMedicine::route('/create'),
            'edit' =>    Pages\EditMedicine::route('/{record}/edit'),
        ];
    }
}
