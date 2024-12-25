<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected string|int|array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(OrderResource::getEloquentQuery())
            ->defaultPaginationPageOption(5)
            ->defaultSort('created_at', 'desc')

            ->columns([
                TextColumn::make('id')->searchable()->sortable()->toggleable()->label('Order #'),
                TextColumn::make('user.name')->searchable()->sortable()->toggleable()->label('Customer'),
                TextColumn::make('grand_total')->searchable()->sortable()->toggleable()->money('USD'),
                TextColumn::make('payment_method')->searchable()->sortable()->toggleable(),
                TextColumn::make('payment_status')->searchable()->sortable()->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new' => 'primary',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'info',
                        'cancelled' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-s-truck',
                        'shipped' => 'heroicon-s-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-s-x-circle',
                    })

                    ->searchable()->sortable()->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->actions([
                Action::make('view Order')
                ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                ->color('info')
                ->icon('heroicon-s-eye'),
            ]);
    }
}
