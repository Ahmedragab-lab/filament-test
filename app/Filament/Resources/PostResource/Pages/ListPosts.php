<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
       return [
           Tab::make()->badge(Post::query()->where('published', true)->count())->badgeColor('success'),
           Tab::make()->badge(Post::query()->where('published', false)->count())->badgeColor('danger'),
           'All'=>Tab::make(),
           'Published'=>Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('published', true)),
           'Unpublished'=>Tab::make()->modifyQueryUsing(fn (Builder $query) => $query->where('published', false)),
           
           //    'Published'=>Tab::make()->modifyQueryUsing(function (Builder $query): Builder {
           //        return $query->where('published', true);
           //    }),
            
           //    'Unpublished'=>Tab::make()->modifyQueryUsing(function (Builder $query): Builder {
           //        return $query->where('published', false);
           //    }),
       ];
    }
    
}
