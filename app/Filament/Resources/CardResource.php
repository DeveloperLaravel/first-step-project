<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CardResource\Pages;
use App\Models\Card;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\BulkAction;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Filament\Tables\Columns\ImageColumn;
class CardResource extends Resource
{
    protected static ?string $model = Card::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'الكروت';
    protected static ?string $pluralModelLabel = 'الكروت';

    // ================= FORM =================
    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\TextInput::make('code')
                ->label('الكود')
                // ->default(fn () => strtoupper(Str::random(12)))
                ->disabled()
                ->dehydrated(false),

            Forms\Components\TextInput::make('amount')
                ->label('القيمة')
                ->numeric()
                ->required()
                ->minValue(1),

            Forms\Components\Select::make('status')
                ->label('الحالة')
                ->options([
                    'active' => 'نشط',
                    'used' => 'مستخدم',
                    'expired' => 'منتهي',
                ])
                ->default('active')
                ->required(),

            Forms\Components\DateTimePicker::make('expires_at')
                ->label('تاريخ الانتهاء'),
        ]);
    }

    // ================= TABLE =================
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('القيمة')
                    ->money('LYD')
                    ->sortable(),
ImageColumn::make('qr_path')
    ->label('QR')
    ->size(40)
    ->disk('public'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'used',
                        'warning' => 'expired',
                    ]),

                Tables\Columns\TextColumn::make('usedBy.name')
                    ->label('استخدم بواسطة')
                    ->default('—'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime(),
            ])

            // ================= ACTIONS =================
            ->actions([

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                // 🚀 استخدام الكرت (مع منع إعادة الاستخدام)
                Tables\Actions\Action::make('pdf')
                      ->label('تحميل PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(function ($record) {

                        // 🚫 منع إعادة الاستخدام
                        if ($record->status !== 'active') {
                            throw new \Exception('لا يمكن استخدام هذا الكرت لأنه مستخدم أو منتهي');
                        }  // 🟢 إنشاء QR
        $qr = base64_encode(
            QrCode::format('svg')
                ->size(150)
                ->generate($record->code)
        );
// 🧾 إنشاء PDF
        $pdf = Pdf::loadView('pdf.card', [
            'card' => $record,
            'qr' => $qr,
        ]);
           return response()->streamDownload(
            fn () => print($pdf->output()),
            "card-{$record->code}.pdf"
        );
         }),
Tables\Actions\Action::make('scratch')
    ->label('عرض الكرت')
    ->icon('heroicon-o-eye')
    ->modalContent(fn ($record) => view('pdf.scratch', [
        'card' => $record
    ]))
    ->modalWidth('md'),
Tables\Actions\Action::make('use_card')
    ->label('استخدام الكرت')
    ->color('success')
    ->icon('heroicon-o-check')
    ->visible(fn ($record) => $record->status === 'active')
    ->requiresConfirmation()
    ->action(function ($record) {

    $updated = Card::where('id', $record->id)
            ->where('status', Card::STATUS_ACTIVE)
            ->update([
                'status' => Card::STATUS_USED,
                'used_by' => Auth::id(),
                'used_at' => now(),
            ]);
        // 🔥 حماية قوية
        if ($record->status !== 'active') {
            \Filament\Notifications\Notification::make()
                ->title('خطأ')
                ->body('الكرت مستخدم أو منتهي')
                ->danger()
                ->send();

            return;
        }

        $record->update([
            'status' => 'used',
            'used_by' => Auth::user()->id,
            'used_at' => now(),
        ]);
    }),
                Tables\Actions\DeleteAction::make(),
            ])

            // ================= BULK ACTIONS =================
            ->bulkActions([

                Tables\Actions\DeleteBulkAction::make(),



            ]);
    }

    // ================= RELATIONS =================
    public static function getRelations(): array
    {
        return [];
    }

    // ================= PAGES =================
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCards::route('/'),
            'create' => Pages\CreateCard::route('/create'),
            'edit' => Pages\EditCard::route('/{record}/edit'),
        ];
    }
}
