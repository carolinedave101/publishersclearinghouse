<?php

namespace App\Filament\Resources\SpinAndWinResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ResultsRelationManager extends RelationManager
{
    protected static string $relationship = 'results';

    protected static ?string $title = 'Spin Results';

    protected static ?string $icon = 'heroicon-o-trophy';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('spin_wheel_segment_id')
                    ->label('Segment')
                    ->relationship('segment', 'label')
                    ->required(),
                Forms\Components\TextInput::make('winner_name'),
                Forms\Components\TextInput::make('winner_email'),
                Forms\Components\TextInput::make('prize_label'),
                Forms\Components\Select::make('prize_type')
                    ->options([
                        'cash' => 'Cash',
                        'coupon' => 'Coupon',
                        'physical' => 'Physical',
                        'points' => 'Points',
                        'free_spin' => 'Free Spin',
                        'nothing' => 'Nothing',
                    ]),
                Forms\Components\TextInput::make('prize_value')->numeric(),
                Forms\Components\Toggle::make('is_claimed')
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $state ? $set('claimed_at', now()) : $set('claimed_at', null)),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('prize_label')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('segment.label')
                    ->label('Segment')
                    ->sortable(),
                Tables\Columns\TextColumn::make('prize_label')
                    ->label('Prize')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prize_type')
                    ->badge(),
                Tables\Columns\TextColumn::make('prize_value')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('winner_name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_claimed')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Played')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_claimed'),
                Tables\Filters\SelectFilter::make('prize_type')
                    ->options([
                        'cash' => 'Cash',
                        'coupon' => 'Coupon',
                        'physical' => 'Physical',
                        'points' => 'Points',
                        'free_spin' => 'Free Spin',
                        'nothing' => 'Nothing',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data): array => [
                        ...$data,
                        'spin_and_win_id' => $this->getOwnerRecord()->id,
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
