<?php

namespace App\Filament\Resources;

use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Payment Methods';
    }

    public static function getPluralLabel(): string
    {
        return 'Payment Methods';
    }

    public static function getLabel(): string
    {
        return 'Payment Method';
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewPaymentMethods() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Payment Method Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\CheckboxList::make('purposes')
                            ->label('Purpose')
                            ->helperText('Where should this payment method be available?')
                            ->options(\App\Models\PaymentMethod::getPurposeOptions())
                            ->required()
                            ->bulkToggleable()
                            ->default(['deposit', 'withdrawal']),
                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'card' => 'Credit/Debit Card',
                                'paypal' => 'PayPal',
                                'bank' => 'Bank Transfer',
                                'crypto' => 'Cryptocurrency',
                                'offline' => 'Offline / Manual',
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Short Description')
                            ->helperText('Displayed to customers during checkout'),
                        Forms\Components\RichEditor::make('instructions')
                            ->label('Payment Instructions')
                            ->helperText('Step-by-step instructions for offline/manual payments'),
                        Forms\Components\FileUpload::make('logo')
                            ->label('Payment Logo')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('payment-methods')
                            ->columnSpanFull()
                            ->helperText('Optional logo icon (shown on checkout)'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Configuration')
                    ->schema([
                        Forms\Components\KeyValue::make('config')
                            ->label('API / Gateway Config')
                            ->helperText('Key-value pairs for API keys, webhook URLs, etc.')
                            ->addActionLabel('Add credential'),
                        Forms\Components\FileUpload::make('barcode')
                            ->label('Barcode / QR Code')
                            ->image()
                            ->disk('public')
                            ->directory('payment-methods')
                            ->helperText('Optional barcode or QR code image (e.g. crypto wallet QR for scanning)'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Active'),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->disk('public')
                    ->width(40)
                    ->height(40)
                    ->toggleable()
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purpose')
                    ->badge()
                    ->formatStateUsing(fn ($state) => implode(', ', array_map(
                        fn ($p) => \App\Models\PaymentMethod::getPurposeBadge($p),
                        array_filter(explode(',', $state))
                    )))
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'card' => 'Card',
                        'paypal' => 'PayPal',
                        'bank' => 'Bank Transfer',
                        'crypto' => 'Crypto',
                        'offline' => 'Offline',
                        default => ucfirst($state),
                    })
                    ->color(fn ($state) => match ($state) {
                        'card' => 'info',
                        'paypal' => 'primary',
                        'bank' => 'warning',
                        'crypto' => 'danger',
                        'offline' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\ImageColumn::make('barcode')
                    ->disk('public')
                    ->width(60)
                    ->height(60)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Barcode'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'card' => 'Credit/Debit Card',
                        'paypal' => 'PayPal',
                        'bank' => 'Bank Transfer',
                        'crypto' => 'Cryptocurrency',
                        'offline' => 'Offline / Manual',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\PaymentMethodResource\Pages\ListPaymentMethods::route('/'),
            'create' => \App\Filament\Resources\PaymentMethodResource\Pages\CreatePaymentMethod::route('/create'),
            'edit' => \App\Filament\Resources\PaymentMethodResource\Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
