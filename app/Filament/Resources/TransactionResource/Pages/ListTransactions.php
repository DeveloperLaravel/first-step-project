<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('redeem_card')
    ->label('Recharge via Card')
    ->icon('heroicon-o-qr-code')
    ->form([
        TextInput::make('code')
            ->label('Card Code')
            ->required()
    ])
    ->action(function (array $data) {

        app(\App\Services\CardService::class)
            ->redeem($data['code'], Auth::user());

    })
    ->successNotificationTitle('تم الشحن بنجاح'),
        ];
    }
}
