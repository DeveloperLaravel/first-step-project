<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use App\Filament\Traits\HasSoftDeletes;

use Filament\Forms\Components\{
    TextInput,
    Toggle,
    DateTimePicker,
    Select
};

use Filament\Tables\Columns\{
    TextColumn,
    BadgeColumn
};

use Filament\Tables\Actions\{
    Action,
    EditAction,
    DeleteAction,
    RestoreAction,
    ActionGroup
};

use Filament\Tables\Filters\TrashedFilter;

class UserResource extends Resource
{
    use HasSoftDeletes;

    protected static ?string $model = User::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationGroup = 'ادارة المستخدمين';

    protected static ?string $navigationLabel = 'حسابات المستخدمين';
    protected static ?string $modelLabel = 'حساب المستخدم';
        protected static ?string $pluralModelLabel = 'حسابات المستخدمين';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    /* ================================
     | FORM
     ================================= */
    public static function form(Form $form): Form
    {
        return $form->schema([

            TextInput::make('name')
                ->label('الاسم')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('البريد')
                ->email()
                ->required()
                ->unique(ignoreRecord: true),

            TextInput::make('password')
                ->label('كلمة المرور')
                ->password()
                ->required(fn ($context) => $context === 'create')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->dehydrated(fn ($state) => filled($state)),
TextInput::make('balance')
->default(0)
    ->numeric() ->label('الرصيد')->disabled(),
            Select::make('roles')
                ->label('الدور')
                ->multiple()
                ->relationship('roles', 'name')
                ->preload()
                ->searchable(),

            Select::make('permissions')
                ->label('الصلاحيات')
                ->multiple()
                ->relationship('permissions', 'name')
                ->preload()
                ->searchable(),

            Toggle::make('is_active')
                ->label('الحالة')
                ->default(true)
                ->reactive()
                ->afterStateUpdated(fn ($state, $set) =>
                    $set('deactivated_at', $state ? null : now())
                ),

            DateTimePicker::make('deactivated_at')
                ->label('تاريخ التعطيل')
                ->disabled(),

            DateTimePicker::make('last_login_at')
                ->label('آخر تسجيل دخول')
                ->disabled(),

            TextInput::make('last_login_ip')
                ->label('IP')
                ->disabled(),

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
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('البريد')
                    ->searchable(),
TextColumn::make('balance')
    ->label('الرصيد')  ->sortable(),
                TextColumn::make('roles.name')
                    ->label('الدور')
                    ->badge()
                    ->separator(','),

                TextColumn::make('permissions.name')
                    ->label('الصلاحيات')
                    ->badge()
                    ->separator(','),

                BadgeColumn::make('is_active')
                    ->label('الحالة')
                    ->formatStateUsing(fn ($state) => $state ? 'مفعل' : 'موقوف')
                    ->colors([
                        'success' => fn ($state) => $state,
                        'danger' => fn ($state) => !$state,
                    ]),

                TextColumn::make('last_login_at')
                    ->label('آخر دخول')
                    ->since()
                    ->placeholder('—'),

                BadgeColumn::make('last_seen_at')
                    ->label('نشاط المستخدم')
                    ->formatStateUsing(fn ($state) =>
                        $state && now()->diffInMinutes($state) < 5
                            ? 'نشط الآن'
                            : 'غير نشط'
                    )
                    ->colors([
                        'success' => fn ($state) => $state && now()->diffInMinutes($state) < 5,
                        'danger' => fn ($state) => !$state || now()->diffInMinutes($state) >= 5,
                    ]),

            ])

            ->filters([
                TrashedFilter::make(),
            ])

            ->actions([

                /* 🔥 تفعيل / تعطيل */
                Action::make('toggle')
                    ->iconButton()
                    ->icon(fn ($record) =>
                        $record->is_active
                            ? 'heroicon-o-x-circle'
                            : 'heroicon-o-check-circle'
                    )
                    ->color(fn ($record) =>
                        $record->is_active ? 'danger' : 'success'
                    )
                    ->requiresConfirmation()
                    ->action(fn ($record) =>
                        $record->update([
                            'is_active' => !$record->is_active,
                            'deactivated_at' => $record->is_active ? now() : null,
                        ])
                    )
                    ->visible(fn ($record) => static::canEdit($record)),

                ActionGroup::make([

                    EditAction::make()
                        ->visible(fn ($record) => static::canEdit($record)),

                    DeleteAction::make()
                        ->visible(fn ($record) => static::canDelete($record)),

                    RestoreAction::make()
                        ->visible(fn ($record) => static::canRestore($record)),

                ]),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => static::canDeleteAny()),

                    Tables\Actions\RestoreBulkAction::make(),

                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->visible(fn () => static::canForceDeleteAny()),

                ]),
            ]);
    }

    /* ================================
     | PERMISSIONS
     ================================= */

    public static function canViewAny(): bool
    {
        return Auth::user()?->can('users.view') ?? false;
    }

    public static function canCreate(): bool
    {
        return Auth::user()?->can('users.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return Auth::user()?->can('users.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return Auth::user()?->can('users.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()?->can('users.delete') ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return Auth::user()?->can('users.forceDelete') ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return Auth::user()?->can('users.forceDelete') ?? false;
    }

    public static function canRestore($record): bool
    {
        return Auth::user()?->can('users.restore') ?? false;
    }

    /* ================================
     | PAGES
     ================================= */
    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\UserResource\Pages\ListUsers::route('/'),
            'create' => \App\Filament\Resources\UserResource\Pages\CreateUser::route('/create'),
            'edit' => \App\Filament\Resources\UserResource\Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
