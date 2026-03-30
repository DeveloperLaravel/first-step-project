<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Events\RoleChanged;
use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
    protected function afterCreate(): void
    {
          event(new RoleChanged(
        'إنشاء صلاحية',
        $this->record->name
    ));
        Notification::make()
            ->title('تم إنشاء الصلاحية')
            ->success()
            ->send();
    }
}
