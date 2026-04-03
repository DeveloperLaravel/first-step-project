<?php

namespace App\Filament\Resources\CardResource\Pages;

use App\Filament\Resources\CardResource;
use App\Models\Card;
use App\Service\CardService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
class ListCards extends ListRecords
{
    protected static string $resource = CardResource::class;

    protected function getHeaderActions(): array
    {
        return [
Action::make('redeem')
    ->label('شحن كرت')
    ->icon('heroicon-o-credit-card')
    ->color('success')
    ->form([
        TextInput::make('code')
            ->label('كود الكرت')
            ->required()
            ->minLength(6)
            ->maxLength(50)
            ->placeholder('أدخل كود الكرت')
            ->autocomplete(false),
    ])
    ->action(function (array $data, CardService $cardService) {

        try {
            $result = $cardService->redeem($data['code']);

            Notification::make()
                ->title('تم الشحن بنجاح ✅')
                ->body("تم إضافة {$result['amount_formatted']} إلى حسابك\nرصيدك الحالي: {$result['balance']}")
                ->success()
                ->duration(5000)
                ->send();

        } catch (\Throwable $e) {

            report($e); // تسجيل الخطأ في اللوق

            Notification::make()
                ->title('فشل العملية ❌')
                ->body($e->getMessage())
                ->danger()
                ->duration(5000)
                ->send();
        }
    }),
            Actions\CreateAction::make(),
            Action::make('generate_cards')
                ->label('توليد كروت')
                ->icon('heroicon-o-bolt')
                ->color('success')

                // 🧾 فورم إدخال
                ->form([
                    \Filament\Forms\Components\TextInput::make('count')
                        ->label('عدد الكروت')
                        ->numeric()
                        ->default(100)
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('amount')
                        ->label('قيمة الكرت')
                        ->numeric()
                        ->default(10)
                        ->required(),
                ])

                // 🚀 التنفيذ
                ->action(function (array $data) {

                    $cards = [];

                    for ($i = 0; $i < $data['count']; $i++) {

                        $code = Card::generateUniqueCode();
   // 📌 اسم ملف QR
        $fileName = 'images/' . Str::uuid() . '.svg';

        // 📌 توليد QR وحفظه في storage
        $qrImage = QrCode::format('svg')
            ->size(200)
            ->generate($code);

        Storage::disk('public')->put($fileName, $qrImage);
                        $cards[] = [
                            'code' => $code,
                            'hash' => bcrypt($code),
                            'amount' => $data['amount'],
                            'status' => 'active',
                             'qr_path' => $fileName, // ⭐ هنا الإضافة
                            'created_by' => Auth::id(),
                            'expires_at' => now()->addMonths(3),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    Card::insert($cards);
                }),
        ];
    }
}
