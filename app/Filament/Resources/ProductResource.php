<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([
                        TextInput::make('name')
                            ->label(__('Product Name'))
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state,Set $set) 
                                    => $set('slug', Str::slug($state)))
                            ->minLength(1)
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label(__('Product Slug'))
                            ->required()
                            ->disabled()
                            ->unique(Product::class, 'slug', ignoreRecord: true)
                            ->dehydrated()
                            ->minLength(1)
                            ->maxLength(150),
                        MarkdownEditor::make('description')
                            ->required()
                            ->fileAttachmentsDirectory('products')
                            ->columnSpanFull(),
                    ])->columns(2),
                    Section::make('Product Images')->schema([
                        FileUpload::make('images')
                            ->multiple()
                            ->disk('public')
                            ->directory('products')
                            ->maxFiles(5)
                            ->reorderable()
                            ->required(),
                    ])
                ])->columnSpan(2),
                Group::make()->schema([
                    Section::make('Product Price')->schema([
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('$'),
                    ]),
                    Section::make('Product Details')->schema([
                        Select::make('category_id')
                        ->label('Category')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                        Select::make('brand_id')
                        ->label('Brand')
                        ->relationship('brand', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                    ]),
                    Section::make('Product Status')->schema([
                        Toggle::make('is_active')
                        ->default(true)
                        ->label(__('Active'))
                        ->required()
                        ->onIcon('heroicon-m-check-circle')
                        ->offIcon('heroicon-m-x-circle'),
                        Toggle::make('is_featured')
                        ->default(false)
                        ->label(__('Featured'))
                        ->required()
                        ->onIcon('heroicon-m-check-circle')
                        ->offIcon('heroicon-m-x-circle'),
                        Toggle::make('in_stock')
                        ->default(true)
                        ->label(__('In Stock'))
                        ->required()
                        ->onIcon('heroicon-m-check-circle')
                        ->offIcon('heroicon-m-x-circle'),
                        Toggle::make('on_sale')
                        ->default(false)
                        ->label(__('On Sale'))
                        ->required()
                        ->onIcon('heroicon-m-check-circle')
                        ->offIcon('heroicon-m-x-circle'),
                    ])->columns(2)
                ])->columnSpan(1),         
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable()->toggleable(),
                TextColumn::make('category.name')->searchable()->sortable()->toggleable(),
                TextColumn::make('brand.name')->searchable()->sortable()->toggleable(),
                TextColumn::make('price')->money()->sortable()->toggleable(),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('is_featured')->boolean(),
                IconColumn::make('in_stock')->boolean(),
                IconColumn::make('on_sale')->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active'),
                TernaryFilter::make('is_featured'),
                TernaryFilter::make('in_stock'),
                TernaryFilter::make('on_sale'),
                SelectFilter::make('category')
                ->relationship('category', 'name')
                ->searchable()
                ->multiple()
                ->preload(),
                SelectFilter::make('brand')
                ->relationship('brand', 'name')
                ->searchable()
                ->multiple()
                ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->slideOver()
                        ->recordTitle(fn($record) => __('Product : ') . $record->id),

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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
