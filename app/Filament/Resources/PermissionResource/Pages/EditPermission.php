<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class EditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->visible(fn (): bool => Auth::user()?->can('permissions.update')),
        ];
    }
     protected function afterSave(): void
    {
        Notification::make()
            ->title('تم تعديل الصلاحية')
            ->success()
            ->send();
    }
}
