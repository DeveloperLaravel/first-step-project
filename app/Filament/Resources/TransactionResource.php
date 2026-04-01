<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
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

            Select::make('user_id')
                ->label('المستخدم')
                ->relationship('user', 'name')
                ->searchable()
                ->required(),

            TextInput::make('amount')
                ->label('المبلغ')
                ->numeric()
                ->required()
                ->prefix('د.ل')
                ->minValue(0.01),

            Select::make('type')
                ->label('نوع العملية')
                ->options([
                    'recharge' => 'شحن',
                    'purchase' => 'شراء',
                ])
                ->required()
                ->native(false),

            Textarea::make('description')
                ->label('الوصف')
                ->placeholder('اكتب ملاحظة...')
                ->columnSpanFull(),
        ]);
    }

    // 📊 الجدول
    public static function table(Table $table): Table
    {
        return $table

            ->columns([
                TextColumn::make('user.name')
                    ->label('المستخدم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('LYD')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'recharge' => 'شحن',
                        'purchase' => 'شراء',
                    })
                    ->colors([
                        'success' => 'recharge',
                        'danger' => 'purchase',
                    ]),

                TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(30),

                TextColumn::make('created_at')
                    ->label('التاريخ')
                    ->dateTime()
                    ->sortable(),
            ])

            // 🔥 الأزرار العلوية (المهم)
            ->headerActions([

                // 🔋 شحن
                Action::make('redeem_card')
                    ->label('شحن')
                    ->icon('heroicon-o-qr-code')
                    ->form([
                        TextInput::make('code')
                            ->label('كود الكرت')
                            ->required()
                    ])
                    ->action(function (array $data) {

                        try {
                            app(\App\Services\CardService::class)
                                ->redeem($data['code'], FacadesAuth::user()->id);

                            Notification::make()
                                ->title('تم الشحن بنجاح')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }

                    }),

                // 🛒 شراء
                Action::make('purchase')
                    ->label('شراء')
                    ->icon('heroicon-o-shopping-cart')
                    ->form([
                        TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->required(),

                        Textarea::make('description')
                            ->label('الوصف'),
                    ])
                    ->action(function (array $data) {

                        try {
                            app(\App\Services\PurchaseService::class)
                                ->buy(
                                    FacadesAuth::user()->id,
                                    $data['amount'],
                                    $data['description'] ?? null
                                );

                            Notification::make()
                                ->title('تمت عملية الشراء')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title($e->getMessage())
                                ->danger()
                                ->send();
                        }

                    }),
            ])

            ->filters([
                SelectFilter::make('type')
                    ->label('نوع العملية')
                    ->options([
                        'recharge' => 'شحن',
                        'purchase' => 'شراء',
                    ]),
            ])

            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
                Tables\Actions\DeleteAction::make()->label('حذف'),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('حذف المحدد'),
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
