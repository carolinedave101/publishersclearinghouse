<?php

namespace App\Filament\Resources\WinnerResource\RelationManagers;

use App\Models\Deposit;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class WinnerDepositsRelationManager extends RelationManager
{
    protected static string $relationship = 'deposits';

    protected static ?string $title = 'Deposits';

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
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Deposit Date (backdate)'),
                Forms\Components\DateTimePicker::make('approved_at'),
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
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('has_proof')
                    ->label('Proof')
                    ->boolean()
                    ->state(fn ($record) => !empty($record->proof_file)),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Deposit')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['winner_id'] = $this->getOwnerRecord()->id;
                        if (!isset($data['net_amount']) || !$data['net_amount']) {
                            $data['net_amount'] = ($data['amount'] ?? 0) - ($data['fee'] ?? 0);
                        }
                        return $data;
                    })
                    ->after(function ($record) {
                        if ($record->status === 'approved') {
                            Transaction::create([
                                'winner_id' => $record->winner_id,
                                'type' => 'deposit',
                                'amount' => $record->amount,
                                'fee' => $record->fee,
                                'net_amount' => $record->net_amount,
                                'payment_method' => $record->paymentMethod?->name,
                                'reference_type' => 'deposit',
                                'reference_id' => $record->id,
                                'status' => 'completed',
                                'description' => "Deposit of \${$record->amount} via {$record->paymentMethod?->name}",
                            ]);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        if ($record->wasChanged('status') && $record->status === 'approved') {
                            $exists = Transaction::where('reference_type', 'deposit')
                                ->where('reference_id', $record->id)->exists();
                            if (!$exists) {
                                Transaction::create([
                                    'winner_id' => $record->winner_id,
                                    'type' => 'deposit',
                                    'amount' => $record->amount,
                                    'fee' => $record->fee,
                                    'net_amount' => $record->net_amount,
                                    'payment_method' => $record->paymentMethod?->name,
                                    'reference_type' => 'deposit',
                                    'reference_id' => $record->id,
                                    'status' => 'completed',
                                    'description' => "Deposit of \${$record->amount} via {$record->paymentMethod?->name}",
                                ]);
                            }
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
