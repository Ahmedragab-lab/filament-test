<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\Order;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Tables\Columns\StateSwitcher;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-s-users';
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 2;
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'publish'
        ];
    }
    public static function getForm(bool $view = false): array
    {
        return [
            TextInput::make('name')
                ->label(__('Full name'))
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->required()
                ->email()
                ->maxLength(255)
                ->unique(User::class, 'email', ignoreRecord: true),
            TextInput::make('phone')
                ->required()
                ->tel()
                ->maxLength(255)
                ->unique(User::class, 'phone', ignoreRecord: true),
            TextInput::make('password')
                ->same('passwordConfirmation')
                ->visible(!$view)
                ->password()
                ->revealable()
                ->maxLength(255)
                ->required(fn($component, $get, $model, $record, $set, $state) => $record === null)
                ->default(function ($component, $operation, $get, $model, $record, $set, $state) {
                    if ($operation === 'create') {
                        return Str::password(12);
                    }
                })
                ->dehydrated(fn($state) => !empty($state)), // Ensure password is only included if not empty
            TextInput::make('passwordConfirmation')
                ->visible(!$view)
                ->password()
                ->revealable()
                ->dehydrated(false)
                ->maxLength(255),
            Toggle::make('status')
                ->default(true)
                ->label(__('Active'))
                ->onIcon('heroicon-m-check-circle')
                ->offIcon('heroicon-m-x-circle'),
            Select::make('roles')
                ->relationship('roles', 'name')
                ->required()
                ->multiple()
                ->preload()
                ->searchable(),
            FileUpload::make('profile_photo_path')
                ->label(__('Profile photo'))
                ->disk('public')
                ->directory('users')
                ->required(),
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(UserResource::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                ImageColumn::make('profile_photo_path')
                    ->label('Profile photo')
                    ->toggleable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->alignLeft(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->color('gray')
                    ->alignLeft(),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable()
                    ->color('gray')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label(__('Created at'))
                    ->date()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                CheckboxColumn::make('status')
                    ->toggleable()
                    ->label("State"),
                TextColumn::make('roles.name')
                    ->toggleable()
                    ->badge(),
            ])
            ->filters([
                Filter::make('status')->query(
                    function (Builder $query): Builder {
                        return $query->where('status', true);
                    }
                ),
                SelectFilter::make('roles')->label(__('Role'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('Status')->label(__('State'))
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive'
                    ])
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // CommentsRelationManager::class,
            OrdersRelationManager::class
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email', 'phone'];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
