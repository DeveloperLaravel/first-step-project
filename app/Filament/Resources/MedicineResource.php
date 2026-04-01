<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicineResource\Pages;
use App\Models\Medicine;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MedicineResource extends Resource
{
    protected static ?string $model = Medicine::class;

    // 🎯 تعريب القائمة
    protected static ?string $navigationLabel = 'الأدوية';
    protected static ?string $modelLabel = 'دواء';
    protected static ?string $pluralModelLabel = 'الأدوية';

    protected static ?string $navigationIcon = 'heroicon-o-beaker';

    // 📌 ترتيب في القائمة
    protected static ?int $navigationSort = 1;
protected static ?string $navigationGroup = 'ادارة المخزون';

    // 🧾 الفورم
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make('name')
                ->label('اسم الدواء')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('price')
                ->label('السعر (دينار)')
                ->numeric()
                ->required()
                ->prefix('LYD')
                ->minValue(0),

            Forms\Components\TextInput::make('quantity')
                ->label('الكمية')
                ->numeric()
                ->required()
                ->minValue(0),

            Forms\Components\FileUpload::make('image')
                ->label('صورة الدواء')
                ->image()
                  ->disk('public') // مهم
                ->directory('images')
                ->imagePreviewHeight('150')
                ->downloadable()
                ->openable(),

        ]);
    }

    // 📊 الجدول
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الدواء')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('السعر')
                    ->money('LYD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('الكمية')
                    ->badge()
                    ->color(fn ($state) => $state < 10 ? 'danger' : 'success'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d'),

            ])

            ->filters([
                // ممكن تضيف فلترة لاحقًا
            ])

            ->actions([

                Tables\Actions\ViewAction::make(),

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(),

            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    // 📄 الصفحات
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedicines::route('/'),
            'create' => Pages\CreateMedicine::route('/create'),
            'edit' => Pages\EditMedicine::route('/{record}/edit'),
        ];
    }
}
