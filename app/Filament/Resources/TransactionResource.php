<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Observers\CardService ;
use App\Observers\PurchaseService;
use App\Service\PurchaseService as ServicePurchaseService;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $pluralLabel = 'العمليات';
 protected static ?int $navigationSort = 5;
protected static ?string $navigationGroup = 'ادترة العمليات المالية';

    // 🧾 الفورم
public static function form(Form $form): Form
{
    return $form->schema([

        TextInput::make('user.name')
            ->label('المستخدم')
            ->disabled(),

        TextInput::make('amount')
            ->label('المبلغ')
            ->disabled(),

        TextInput::make('type')
            ->label('نوع العملية')
            ->disabled(),

        TextInput::make('order.order_number')
            ->label('رقم الطلب')
            ->disabled(),

        TextInput::make('reference')
            ->label('رقم العملية')
            ->disabled(),

        Textarea::make('description')
            ->label('الوصف')
            ->disabled(),

        TextInput::make('created_at')
            ->label('التاريخ')
            ->disabled(),

    ]);
}

    // 📊 الجدول
   // ================= TABLE =================
    public static function table(Table $table): Table
    {
        return $table->columns([

            TextColumn::make('user.name')
                ->label('المستخدم')
                ->searchable(),

            TextColumn::make('amount')
                ->label('المبلغ')
                ->money('USD')
                ->sortable(),

            TextColumn::make('type')
                ->label('النوع')
                ->badge()
                ->colors([
                    'success' => 'recharge',
                    'danger' => 'purchase',
                    'warning' => 'refund',
                ]),

            TextColumn::make('order.order_number')
                ->label('رقم الطلب')
                ->default('-'),

            TextColumn::make('created_at')
                ->label('التاريخ')
                ->dateTime(),

        ])
        ->filters([
            Tables\Filters\SelectFilter::make('type')
                ->options([
                    'recharge' => 'شحن',
                    'purchase' => 'شراء',
                    'refund' => 'استرجاع',
                ])
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),

            // ❌ منع التعديل (مهم جدًا)
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    // 📄 الصفحات
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }
}
