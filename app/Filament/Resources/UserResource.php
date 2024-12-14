<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use App\Tables\Columns\StateSwitcher;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';

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
                TextColumn::make('created_at')->label(__('Created at')),
                TextColumn::make('status')
                    ->toggleable()
                    ->label("State")
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->slideOver()
                        ->recordTitle(fn($record) => __('User : ') . $record->id)
                        ->form(UserResource::getForm(true)),
                    Tables\Actions\EditAction::make()
                        ->slideOver()
                        ->recordTitle(fn($record) => __('User : ') . $record->id)
                        ->form(UserResource::getForm())
                        ->mutateFormDataUsing(function ($record, array $data) {
                            if (empty($data['password'])) {
                                unset($data['password']);
                            }
                            return $data;
                        }),
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
            //
        ];
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
