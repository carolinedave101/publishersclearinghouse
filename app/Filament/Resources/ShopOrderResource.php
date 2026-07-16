<?php

namespace App\Filament\Resources;

use App\Helpers\EmailHelper;
use App\Mail\WinnerNotification;
use App\Models\ShopOrder;
use App\Models\Winner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class ShopOrderResource extends Resource
{
    protected static ?string $model = ShopOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Shop';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewShopOrders() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->required(),
                        Forms\Components\TextInput::make('customer_email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('address'),
                        Forms\Components\TextInput::make('city'),
                        Forms\Components\TextInput::make('state'),
                        Forms\Components\TextInput::make('zip'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Order Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required(),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('$'),
                                Forms\Components\TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(1),
                                Forms\Components\TextInput::make('line_total')
                                    ->numeric()
                                    ->prefix('$'),
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->hiddenOn('edit'),
                        Forms\Components\Placeholder::make('items_display')
                            ->label('')
                            ->content(fn ($record) => view('filament.forms.shop-order-items', ['items' => $record?->items ?? []]))
                            ->hiddenOn('create'),
                    ]),
                Forms\Components\Section::make('Payment & Status')
                    ->schema([
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options(fn () => \App\Models\PaymentMethod::pluck('name', 'slug')->toArray())
                            ->searchable(),
                        Forms\Components\TextInput::make('total')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required(),
                        Forms\Components\FileUpload::make('payment_proof')
                            ->label('Payment Proof')
                            ->directory('payment-proofs')
                            ->image()
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/*', 'application/pdf'])
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order #')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('total')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->formatStateUsing(fn ($state) => \App\Models\PaymentMethod::where('slug', $state)->value('name') ?? ucfirst($state))
                    ->toggleable(),
                Tables\Columns\IconColumn::make('payment_proof')
                    ->label('Proof')
                    ->boolean()
                    ->state(fn ($record) => !empty($record->payment_proof))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->url(fn ($record) => $record->payment_proof ? url('storage/' . $record->payment_proof) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options(fn () => \App\Models\PaymentMethod::pluck('name', 'slug')->toArray()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        if ($record->wasChanged('status')) {
                            $newStatus = $record->status;
                            $statusLabels = [
                                'pending' => 'Pending',
                                'processing' => 'Processing',
                                'shipped' => 'Shipped',
                                'delivered' => 'Delivered',
                                'cancelled' => 'Cancelled',
                            ];
                            $label = $statusLabels[$newStatus] ?? ucfirst($newStatus);

                            $winner = Winner::where('email', $record->customer_email)->first();
                            if ($winner && $winner->email) {
                                EmailHelper::send(
                                    new WinnerNotification(
                                        $winner,
                                        "Order #{$record->id} {$label}",
                                        "Hi {$winner->first_name}, your order #{$record->id} has been updated to: {$label}."
                                    ),
                                    $winner->email,
                                    $winner->first_name
                                );
                            } elseif ($record->customer_email) {
                                EmailHelper::send(
                                    new \App\Mail\AdminEmail(
                                        "Order #{$record->id} {$label}",
                                        "Your order #{$record->id} status has been updated to: {$label}."
                                    ),
                                    $record->customer_email,
                                    $record->customer_name
                                );
                            }
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ShopOrderResource\Pages\ListShopOrders::route('/'),
            'create' => \App\Filament\Resources\ShopOrderResource\Pages\CreateShopOrder::route('/create'),
            'view' => \App\Filament\Resources\ShopOrderResource\Pages\ViewShopOrder::route('/{record}'),
            'edit' => \App\Filament\Resources\ShopOrderResource\Pages\EditShopOrder::route('/{record}/edit'),
        ];
    }
}
