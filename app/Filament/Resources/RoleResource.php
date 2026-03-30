<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\{
    EditAction,
    DeleteAction,
    BulkActionGroup,
    DeleteBulkAction
};

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'ادوار المستخدمين';
    protected static ?string $modelLabel = 'ادوار المستخدم';
        protected static ?string $pluralModelLabel = 'ادوار المستخدمين';
    /* ================================
     | FORM
     ================================= */
    public static function form(Form $form): Form
    {
        return $form->schema([

            TextInput::make('name')
                ->label('اسم الدور')
                ->required()
                ->unique(ignoreRecord: true),

            Select::make('permissions')
                ->label('الصلاحيات')
                ->multiple()
                ->relationship('permissions', 'name')
                ->preload()
                ->searchable(),

        ]);
    }

    /* ================================
     | TABLE
     ================================= */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('اسم الدور')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('permissions.name')
                    ->label('الصلاحيات')
                    ->badge()
                    ->separator(','),

            ])

            ->actions([

                /* ✏️ تعديل */
                EditAction::make()
                    ->visible(fn (Role $record) => static::canEdit($record))
                    ->after(function (Role $record) {

                        Notification::make()
                            ->title('تم تعديل الدور')
                            ->body("تم تعديل الدور: {$record->name}")
                            ->success()
                            ->send();
                    }),

                /* 🗑️ حذف */
                DeleteAction::make()
                    ->visible(fn (Role $record) => static::canDelete($record))
                    ->requiresConfirmation()
                    ->after(function (Role $record) {

                        Notification::make()
                            ->title('تم حذف الدور')
                            ->body("تم حذف الدور: {$record->name}")
                            ->danger()
                            ->send();
                    }),

            ])

            ->bulkActions([

                BulkActionGroup::make([

                    DeleteBulkAction::make()
                        ->visible(fn () => static::canDeleteAny())
                        ->after(function () {

                            Notification::make()
                                ->title('تم حذف مجموعة أدوار')
                                ->body('تم حذف عدة أدوار بنجاح')
                                ->danger()
                                ->send();
                        }),

                ]),
            ]);
    }

    /* ================================
     | PERMISSIONS
     ================================= */

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('roles.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->can('roles.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->can('roles.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->can('roles.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()?->can('roles.delete') ?? false;
    }

    /* ================================
     | تحسين الأمان
     ================================= */

    public static function canEditProtectedRole($record): bool
    {
        return $record->name !== 'admin';
    }

    public static function canDeleteProtectedRole($record): bool
    {
        return $record->name !== 'admin';
    }

    /* ================================
     | QUERY
     ================================= */



    /* ================================
     | PAGES
     ================================= */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
