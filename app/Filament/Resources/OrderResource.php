<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Service\OrderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'إدارة الطلبات';
    protected static ?string $navigationLabel = 'الطلبات';

    protected static ?string $modelLabel = 'طلب';
    protected static ?string $pluralModelLabel = 'الطلبات';

    // ================= FORM =================
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('معلومات الطلب')
                ->schema([

                    Forms\Components\Select::make('user_id')
                        ->label('المستخدم')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\TextInput::make('order_number')
                        ->label('رقم الطلب')
                        ->disabled()
                        ->dehydrated(false),

                ])->columns(2),

            Forms\Components\Section::make('الحالة والدفع')
                ->schema([

                    Forms\Components\Select::make('status')
                        ->label('حالة الطلب')
                        ->options([
                            'pending' => 'قيد الانتظار',
                            'processing' => 'قيد التنفيذ',
                            'completed' => 'مكتمل',
                            'cancelled' => 'ملغي',
                        ])
                        ->default('pending')
                        ->required()
                        ->native(false),

                    Forms\Components\Select::make('payment_status')
                        ->label('حالة الدفع')
                        ->options([
                            'unpaid' => 'غير مدفوع',
                            'paid' => 'مدفوع',
                            'failed' => 'فشل الدفع',
                        ])
                        ->default('unpaid')
                        ->required()
                        ->native(false),

                ])->columns(2),

            Forms\Components\Section::make('تفاصيل إضافية')
                ->schema([

                    Forms\Components\TextInput::make('total')
                        ->label('الإجمالي')
                        ->numeric()
                        ->prefix('LYD')
                        ->disabled() // 💎 لا تخليه يدوي
                        ->dehydrated(false),

                    Forms\Components\Textarea::make('notes')
                        ->label('ملاحظات')
                        ->rows(3)
                        ->columnSpanFull(),

                ]),
        ]);
    }

    // ================= TABLE =================
   public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('order_number')
                ->label('رقم الطلب')
                ->searchable(),

            TextColumn::make('user.name')
                ->label('المستخدم')
                ->searchable(),

        TextColumn::make('total')
    ->label('الإجمالي')
    ->money('LYD')
  ->getStateUsing(fn ($record) =>
        $record->items->sum('subtotal')
    ),

            TextColumn::make('status')
                ->label('الحالة')
                ->badge(),

            TextColumn::make('created_at')
                ->label('تاريخ الإنشاء')
                ->dateTime(),
        ])

        ->actions([
            // ✏️ تعديل
            EditAction::make(),

            // 🗑️ حذف
            DeleteAction::make(),

            // 💳 دفع / تنفيذ الطلب
          Action::make('pay')
    ->label('تنفيذ الدفع')
    ->icon('heroicon-o-credit-card')
    ->color('success')

    // يظهر فقط إذا لم يتم الدفع
    // ->visible(fn ($record) => $record->payment_status === 'unpaid')

    // تأكيد قبل التنفيذ
    ->requiresConfirmation()
    ->modalHeading('تأكيد الدفع')
    ->modalDescription('هل أنت متأكد من تنفيذ عملية الدفع؟')
    ->modalSubmitActionLabel('نعم، نفذ الدفع')

    ->action(function ($record) {

        try {
            app(OrderService::class)->payOrder($record);

            Notification::make()
                ->title('تم الدفع بنجاح ✅')
                ->success()
                ->send();

        } catch (\Exception $e) {

            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }
    }),

            // 👁️ عرض التفاصيل
            // Action::make('view')
            //     ->label('عرض')
            //     ->icon('heroicon-o-eye')
            //     ->color('primary')
            //     ->url(fn ($record) => route('orders.show', $record)),
        ]);
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
