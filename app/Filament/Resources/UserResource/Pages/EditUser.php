<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
            ->visible(fn (): bool => Auth::user()?->can('users.delete')),

            Actions\ForceDeleteAction::make()
            ->visible(fn (): bool => Auth::user()?->can('users.forceDelete')),

            Actions\RestoreAction::make()
            ->visible(fn (): bool => Auth::user()?->can('users.restore')),

        ];
    }
}
