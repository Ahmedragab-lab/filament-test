<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('create a post')
                ->description('Create a new post')
                ->collapsible()
                ->schema([
                TextInput::make('title')->required()->rules('min:3|max:10'),
                TextInput::make('slug')->required()->unique(Post::class, 'slug', ignoreRecord: true),
                ColorPicker::make('color')->required(),
                MarkdownEditor::make('content')->required()->columnSpanFull(),
            ])->columnSpan(2)->columns(2),
            Group::make()->schema([
                Section::make('image')
                         ->schema([
                            FileUpload::make('thumbnail')
                            ->disk('public')
                            ->directory('posts')
                            ->required(),
                ])->columnSpan(1),
                Section::make('meta')
                         ->schema([
                            TagsInput::make('tags')->required(),
                            Toggle::make('published')
                                    ->onIcon('heroicon-m-check-circle')
                                    ->offIcon('heroicon-m-x-circle'),
                ])->columnSpan(1),
            ]),
            // Checkbox::make('published'),
        ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('id')->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('thumbnail')->toggleable(),
                TextColumn::make('title')->searchable()->sortable()->toggleable(),
                TextColumn::make('slug')->searchable()->sortable()->toggleable(),
                ColorColumn::make('color')->searchable()->toggleable()
                            ->sortable()
                            ->copyable()
                            ->copyMessage('Color code copied')
                            ->copyMessageDuration(1500),
                TextColumn::make('tags')->toggleable(),
                // CheckboxColumn::make('published'),
                ToggleColumn::make('published')->toggleable()
                            ->onIcon('heroicon-m-check-circle')
                            ->offIcon('heroicon-m-x-circle'),
                TextColumn::make('category.name')->searchable()->sortable()->toggleable(),
                TextColumn::make('created_at')->date()->searchable()->sortable()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
