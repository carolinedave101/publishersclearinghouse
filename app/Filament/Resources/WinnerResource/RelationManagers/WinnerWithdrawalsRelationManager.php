<?php

namespace App\Filament\Resources\WinnerResource\RelationManagers;

use App\Models\Transaction;
use App\Models\Withdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WinnerWithdrawalsRelationManager extends RelationManager
{
    protected static string $relationship = 'withdrawals';

    protected static ?string $title = 'Withdrawals';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Forms\Components\KeyValue::make('account_details')
                    ->label('Account Details'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Request Date (backdate)'),
                Forms\Components\DateTimePicker::make('approved_at'),
                Forms\Components\DateTimePicker::make('completed_at'),
                Forms\Components\DateTimePicker::make('rejected_at'),
                Forms\Components\RichEditor::make('admin_notes')
                    ->label('Admin Notes'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->searchable()
            ->searchPlaceholder('Search by method, amount, or status...')
            ->columns([
                Tables\Columns\TextColumn::make('paymentMethod.name')
                    ->label('Method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('USD')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fee')
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
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Withdrawal')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['winner_id'] = $this->getOwnerRecord()->id;
                        if (!isset($data['net_amount']) || !$data['net_amount']) {
                            $data['net_amount'] = ($data['amount'] ?? 0) - ($data['fee'] ?? 0);
                        }
                        return $data;
                    })
                    ->after(function ($record) {
                        if (in_array($record->status, ['approved', 'completed'])) {
                            Transaction::create([
                                'winner_id' => $record->winner_id,
                                'type' => 'withdrawal',
                                'amount' => $record->amount,
                                'fee' => $record->fee,
                                'net_amount' => -$record->net_amount,
                                'payment_method' => $record->paymentMethod?->name,
                                'reference_type' => 'withdrawal',
                                'reference_id' => $record->id,
                                'status' => $record->status === 'completed' ? 'completed' : 'processing',
                                'description' => "Withdrawal of \${$record->amount} to {$record->paymentMethod?->name}",
                            ]);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        $needsTx = in_array($record->status, ['approved', 'completed']);
                        $exists = Transaction::where('reference_type', 'withdrawal')
                            ->where('reference_id', $record->id)->exists();
                        if ($needsTx && !$exists) {
                            Transaction::create([
                                'winner_id' => $record->winner_id,
                                'type' => 'withdrawal',
                                'amount' => $record->amount,
                                'fee' => $record->fee,
                                'net_amount' => -$record->net_amount,
                                'payment_method' => $record->paymentMethod?->name,
                                'reference_type' => 'withdrawal',
                                'reference_id' => $record->id,
                                'status' => $record->status === 'completed' ? 'completed' : 'processing',
                                'description' => "Withdrawal of \${$record->amount} to {$record->paymentMethod?->name}",
                            ]);
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
