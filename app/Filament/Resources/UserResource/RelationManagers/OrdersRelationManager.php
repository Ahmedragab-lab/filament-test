<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Number;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Group::make()->schema([
                //     Section::make('Order Information')->schema([
                //         Select::make('user_id')
                //             ->label('Customer')
                //             ->relationship('user', 'name')
                //             ->searchable()
                //             ->preload()
                //             ->required(),
                //         Select::make('payment_method')
                //             ->options([
                //                 'cash' => 'Cash on Delivery',
                //                 'card' => 'Card',
                //                 'bank' => 'Bank',
                //                 'stripe' => 'Stripe',
                //             ]),
                //         Select::make('payment_status')
                //             ->options([
                //                 'pending' => 'Pending',
                //                 'paid' => 'Paid',
                //                 'failed' => 'Failed',
                //             ])->default('pending')
                //             ->required(),
                //         ToggleButtons::make('status')
                //             ->options([
                //                 'new' => 'New',
                //                 'processing' => 'Processing',
                //                 'shipped' => 'Shipped',
                //                 'delivered' => 'Delivered',
                //                 'cancelled' => 'Cancelled',
                //             ])
                //             ->colors([
                //                 'new' => 'primary',
                //                 'processing' => 'warning',
                //                 'shipped' => 'success',
                //                 'delivered' => 'info',
                //                 'cancelled' => 'danger',
                //             ])
                //             ->icons([
                //                 'new' => 'heroicon-m-sparkles',
                //                 'processing' => 'heroicon-s-truck',
                //                 'shipped' => 'heroicon-s-truck',
                //                 'delivered' => 'heroicon-s-truck',
                //                 'cancelled' => 'heroicon-s-x-circle',
                //             ])
                //             ->inline()
                //             ->default('new')
                //             ->required(),
                //         Select::make('currency')
                //             ->options([
                //                 'USD' => 'USD',
                //                 'EUR' => 'EUR',
                //                 'EGP' => 'EGP',
                //             ])->default('USD')
                //             ->required(),
                //         Select::make('shipping_method')
                //             ->options([
                //                 'fedex' => 'FedEx',
                //                 'dhl' => 'DHL',
                //             ])->default('fedex')
                //             ->required(),
                //         TextInput::make('grand_total')
                //             ->required()
                //             ->numeric()
                //             ->default(0.00)
                //             ->prefix('$'),
                //         MarkdownEditor::make('notes')
                //             ->required()
                //             ->fileAttachmentsDirectory('orders')
                //             ->columnSpanFull(),
                //     ])->columns(2),
                //     Section::make('Order Items')->schema([
                //         Repeater::make('items')
                //             ->relationship()
                //             ->schema([
                //                 Select::make('product_id')
                //                     ->label('Product')
                //                     ->relationship('product', 'name')
                //                     ->searchable()
                //                     ->preload()
                //                     ->distinct()
                //                     ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                //                     ->required()
                //                     ->reactive()
                //                     ->afterStateUpdated(fn($state, Set $set)
                //                     => $set('unit_amount', Product::find($state)?->price ?? 0.00))
                //                     ->afterStateUpdated(fn($state, Set $set)
                //                     => $set('total_amount', Product::find($state)?->price ?? 0.00))
                //                     ->columnSpan(4),
                //                 TextInput::make('quantity')
                //                     ->required()
                //                     ->numeric()
                //                     ->minValue(1)
                //                     ->default(1)
                //                     ->reactive()
                //                     ->afterStateUpdated(fn($state, Set $set, Get $get)
                //                     => $set('total_amount', $get('unit_amount') * $state))
                //                     ->columnSpan(2),
                //                 TextInput::make('unit_amount')
                //                     ->required()
                //                     ->numeric()
                //                     ->disabled()
                //                     ->dehydrated()
                //                     ->columnSpan(3),
                //                 TextInput::make('total_amount')
                //                     ->required()
                //                     ->numeric()
                //                     ->disabled()
                //                     ->dehydrated()
                //                     ->columnSpan(3),
                //             ])->columns(12),
                //         Placeholder::make('grand_total_placeholder')
                //             ->label('Grand Total')
                //             ->content(function (Get $get, Set $set) {
                //                 $total = 0;
                //                 if (!$repeaters = $get('items')) {
                //                     return $total;
                //                 }
                //                 foreach ($repeaters as $key => $repeater) {
                //                     $total += $get("items.{$key}.total_amount");
                //                 }
                //                 $set('grand_total', $total);
                //                 return Number::currency($total, 'USD');
                //             }),
                //         // TextInput::make('shipping_amount')
                //         //     ->required()
                //         //     ->numeric()
                //         //     ->default(0.00)
                //         //     ->columnSpan(3),
                //         Hidden::make('grand_total')
                //             ->default(0.00),
                //     ])
                // ])->columnSpan(2),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id')->searchable()->sortable()->toggleable()->label('Order #'),
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
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('view Order')
                     ->url(fn (Order $record): string => OrderResource::getUrl('view', ['record' => $record]))
                     ->color('info')
                     ->icon('heroicon-s-eye'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
