<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class OrderStatus extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('New Orders', Order::query()->where('status', 'new')->count()),
            Stat::make('Processing Orders', Order::where('status', 'processing')->count()),
            Stat::make('Shipped Orders', Order::where('status', 'shipped')->count()),
            Stat::make('Delivered Orders', Order::where('status', 'delivered')->count()),
            Stat::make('Cancelled Orders', Order::where('status', 'cancelled')->count()),
            Stat::make('Average Price', Number::currency(Order::average('grand_total'), 'USD')),
        ];
    }
}
