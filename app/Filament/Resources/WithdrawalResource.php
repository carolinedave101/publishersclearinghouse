<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithdrawalResource\Pages;
use App\Helpers\EmailHelper;
use App\Mail\WinnerNotification;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Winner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class WithdrawalResource extends Resource
{
    protected static ?string $model = Withdrawal::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewWithdrawals() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Withdrawal Information')
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
                        Forms\Components\Select::make('payment_method_id')
                            ->relationship('paymentMethod', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('fee')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('net_amount')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                    ])->columns(2),
                Forms\Components\Section::make('Account Details')
                    ->schema([
                        Forms\Components\KeyValue::make('account_details')
                            ->label('Account / Payment Details')
                            ->addActionLabel('Add field'),
                    ]),
                Forms\Components\Section::make('Status & Notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'completed' => 'Completed',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\RichEditor::make('admin_notes')
                            ->label('Admin Notes'),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Request Date (backdate)'),
                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Approved Date (backdate)'),
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completed Date (backdate)'),
                        Forms\Components\DateTimePicker::make('rejected_at')
                            ->label('Rejected Date (backdate)'),
                        Forms\Components\Textarea::make('notes')
                            ->label('Winner Notes'),
                    ])->columns(2),
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
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Method')
                    ->badge()
                    ->toggleable()
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
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        if ($record->wasChanged('status') && $record->winner) {
                            $newStatus = $record->status;
                            $statusLabels = ['approved' => 'Approved', 'completed' => 'Completed', 'rejected' => 'Rejected'];
                            $label = $statusLabels[$newStatus] ?? ucfirst($newStatus);
                            EmailHelper::send(
                                new WinnerNotification(
                                    $record->winner,
                                    "Withdrawal {$label}",
                                    "Hi {$record->winner->first_name}, your withdrawal request for \${$record->amount} has been {$label}."
                                ),
                                $record->winner->email,
                                $record->winner->first_name
                            );
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWithdrawals::route('/'),
            'create' => Pages\CreateWithdrawal::route('/create'),
            'edit' => Pages\EditWithdrawal::route('/{record}/edit'),
        ];
    }
}
