<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
          ->visible(fn (): bool => Auth::user()?->can('roles.delete')),

        ];
    }
     protected function afterSave(): void
    {
        Notification::make()
            ->title('تم تعديل الادوار')
            ->success()
            ->send();
    }
}
