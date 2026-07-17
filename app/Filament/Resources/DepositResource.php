<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepositResource\Pages;
use App\Helpers\EmailHelper;
use App\Mail\DepositConfirmation;
use App\Mail\WinnerNotification;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\Winner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DepositResource extends Resource
{
    protected static ?string $model = Deposit::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewDeposits() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Deposit Information')
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
                Forms\Components\Section::make('Proof of Payment')
                    ->schema([
                        Forms\Components\TextInput::make('proof_file_name')
                            ->label('Filename')
                            ->disabled(),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('download')
                                ->label('Download Proof')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->url(fn ($record) => $record && $record->proof_file ? Storage::url($record->proof_file) : '#')
                                ->openUrlInNewTab()
                                ->visible(fn ($record) => $record && $record->proof_file),
                        ]),
                    ])->columns(2),
                Forms\Components\Section::make('Status & Notes')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                        Forms\Components\RichEditor::make('admin_notes')
                            ->label('Admin Notes'),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Deposit Date (backdate)'),
                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Approved Date (backdate)'),
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
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_proof')
                    ->label('Proof')
                    ->boolean()
                    ->state(fn ($record) => !empty($record->proof_file))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                TernaryFilter::make('has_proof')
                    ->label('Has Proof')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('proof_file'),
                        false: fn (Builder $query) => $query->whereNull('proof_file'),
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        if ($record->wasChanged('status') && $record->winner) {
                            $newStatus = $record->status;
                            $statusLabels = ['approved' => 'Approved', 'rejected' => 'Rejected'];
                            $label = $statusLabels[$newStatus] ?? ucfirst($newStatus);
                            EmailHelper::send(
                                new WinnerNotification(
                                    $record->winner,
                                    "Deposit {$label}",
                                    "Hi {$record->winner->first_name}, your deposit of \${$record->amount} has been {$label}."
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
            'index' => Pages\ListDeposits::route('/'),
            'create' => Pages\CreateDeposit::route('/create'),
            'edit' => Pages\EditDeposit::route('/{record}/edit'),
        ];
    }
}
