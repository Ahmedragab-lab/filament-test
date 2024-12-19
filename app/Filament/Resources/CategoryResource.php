<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\CategoryResource\RelationManagers\PostsRelationManager;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Faker\Core\File;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getForm(bool $view = false): array {
        return [
            Section::make([
                Grid::make()->schema([
                    TextInput::make('name')
                        ->label(__('admin.categoryname'))
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (string $operation, $state,Set $set) 
                                => $set('slug', Str::slug($state)))
                        ->minLength(1)
                        ->maxLength(150),
                    TextInput::make('slug')
                        ->label(__('admin.slug'))
                        ->required()
                        ->disabled()
                        ->unique(Category::class, 'slug', ignoreRecord: true)
                        ->dehydrated()
                        ->minLength(1)
                        ->maxLength(150),
                ]),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('categories')
                    ->required(),
                Toggle::make('is_active')
                    ->default(true)
                    ->label(__('Active'))
                    ->required()
                    ->onIcon('heroicon-m-check-circle')
                    ->offIcon('heroicon-m-x-circle'),
            ])
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
           ->schema(CategoryResource::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->toggleable(),
                TextColumn::make('name')->label(__('admin.categoryname'))->searchable()->sortable(),
                TextColumn::make('slug')->label(__('admin.slug'))->searchable()->sortable(),
                CheckboxColumn::make('is_active')->toggleable()->label("Active"),
                TextColumn::make('created_at')->date()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_at')->label('Date added')
                            ->placeholder(fn($state): string => now()->format('M d, Y')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query

                            ->when(
                                $data['created_at'] ?? null,
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_at'] ?? null) {
                            $indicators['created_at'] = 'Date added ' . Carbon::parse($data['created_at'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->slideOver()
                        ->recordTitle(fn($record) => __('Category : ') . $record->id)
                        ->form(CategoryResource::getForm(true)),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            PostsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
