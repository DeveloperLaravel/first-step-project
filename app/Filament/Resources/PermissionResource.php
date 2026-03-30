<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use App\Filament\Resources\PermissionResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Permission;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

  protected static ?string $navigationIcon = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'الصلاحيات';
    protected static ?string $modelLabel = 'صلاحية';
        protected static ?string $pluralModelLabel = 'الصلاحيات';
 // 🔐 التحكم في الوصول
    public static function canViewAny(): bool
    {
    return Auth::user()?->can('permissions.view') ?? false;
    }

    public static function canCreate(): bool
    {
    return Auth::user()?->can('permissions.create') ?? false;
    }

    public static function canEdit($record): bool
    {
     return Auth::user()?->can('permissions.update') ?? false;

    }

    public static function canDelete($record): bool
    {
            return Auth::user()?->can('permissions.delete') ?? false;

    }
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->label('اسم الصلاحية')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Forms\Components\Select::make('guard_name')
                ->label('Guard')
                ->default('web')
                ->options([
                    'web' => 'web',
                    'api' => 'api',
                ])
                ->required(),
        ]);
    }


     // 📊 الجدول
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('guard_name')
                    ->label('Guard'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Permission $record): bool => static::canEdit($record))->after(function () {
                        Notification::make()
                            ->title('تم تعديل الصلاحية بنجاح')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        Notification::make()
                            ->title('تم حذف الصلاحية بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermissions::route('/'),
            'create' => Pages\CreatePermission::route('/create'),
            'edit' => Pages\EditPermission::route('/{record}/edit'),
        ];
    }
}
