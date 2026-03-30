<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Events\RoleChanged;
use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

protected function afterCreate(): void
{
    event(new RoleChanged(
        'إنشاء الدوار',
        $this->record->name
    ));
      Notification::make()
        ->title('تم إنشاء دور جديد')
        ->success()
        ->send();
}
}
