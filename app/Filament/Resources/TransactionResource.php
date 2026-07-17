<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Winner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 6;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewTransactions() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('winner_id')
                    ->relationship('winner', 'first_name')
                    ->searchable()
                    ->options(function (): array {
                        return Winner::orderBy('is_demo', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->limit(100)
                            ->get()
                            ->mapWithKeys(fn (Winner $w) => [
                                $w->id => "{$w->first_name} {$w->last_name} ({$w->unique_code})",
                            ])->toArray();
                    })
                    ->getSearchResultsUsing(function (string $search): array {
                        return Winner::where(function (Builder $q) use ($search) {
                            $q->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('unique_code', 'like', "%{$search}%");
                        })->orderBy('is_demo', 'desc')->orderBy('created_at', 'desc')
                        ->limit(50)->get()->mapWithKeys(fn (Winner $w) => [
                            $w->id => "{$w->first_name} {$w->last_name} ({$w->unique_code})",
                        ])->toArray();
                    })
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name} ({$record->unique_code})")
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'deposit' => 'Deposit',
                        'withdrawal' => 'Withdrawal',
                        'shop' => 'Shop Purchase',
                        'prize' => 'Prize',
                        'fee' => 'Fee',
                        'adjustment' => 'Adjustment',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('fee')
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                Forms\Components\TextInput::make('net_amount')
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                Forms\Components\TextInput::make('payment_method')
                    ->label('Payment Method'),
                Forms\Components\Select::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'processing' => 'Processing',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                    ])
                    ->default('completed'),
                Forms\Components\Textarea::make('description')
                    ->rows(2),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Transaction Date (backdate)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->searchPlaceholder('Search by winner name, code, or email...')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('winner.first_name')
                    ->label('Winner')
                    ->formatStateUsing(fn ($record) => $record->winner ? "{$record->winner->first_name} {$record->winner->last_name}" : '')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('winner', fn (Builder $q) => $q
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('unique_code', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"));
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'deposit' => 'success',
                        'withdrawal' => 'danger',
                        'shop' => 'primary',
                        'prize' => 'warning',
                        'fee' => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee')
                    ->money('USD')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('net_amount')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(40)
                    ->toggleable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'processing' => 'warning',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'deposit' => 'Deposit',
                        'withdrawal' => 'Withdrawal',
                        'shop' => 'Shop Purchase',
                        'prize' => 'Prize',
                        'fee' => 'Fee',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'completed' => 'Completed',
                        'processing' => 'Processing',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
