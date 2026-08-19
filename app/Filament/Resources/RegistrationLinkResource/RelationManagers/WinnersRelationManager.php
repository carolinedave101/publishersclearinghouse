<?php

namespace App\Filament\Resources\RegistrationLinkResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WinnersRelationManager extends RelationManager
{
    protected static string $relationship = 'winners';

    protected static ?string $title = 'Winners Registered via This Link';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->orderBy('created_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unique_code')
                    ->label('Winner Code')
                    ->copyable()
                    ->copyMessage('Code copied'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'under_review' => 'warning',
                        'documents_needed' => 'info',
                        'processing' => 'primary',
                        'approved' => 'success',
                        'delivered' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('prize_amount')
                    ->money('usd'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'under_review' => 'Under Review',
                        'documents_needed' => 'Documents Needed',
                        'processing' => 'Processing',
                        'approved' => 'Approved',
                        'delivered' => 'Delivered',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('View Winner')
                    ->url(fn ($record) => \App\Filament\Resources\WinnerResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-arrow-top-right-on-square'),
            ]);
    }
}