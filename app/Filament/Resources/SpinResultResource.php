<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpinResultResource\Pages;
use App\Models\SpinResult;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SpinResultResource extends Resource
{
    protected static ?string $model = SpinResult::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewSpinResults() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Result Details')
                    ->schema([
                        Forms\Components\Select::make('spin_and_win_id')
                            ->label('Game')
                            ->relationship('spinAndWin', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\TextInput::make('winner_name')
                            ->label('Winner Name'),
                        Forms\Components\TextInput::make('winner_email')
                            ->label('Winner Email'),
                        Forms\Components\TextInput::make('prize_label')
                            ->label('Prize'),
                        Forms\Components\TextInput::make('prize_value')
                            ->label('Value')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('prize_type')
                            ->options([
                                'cash' => 'Cash',
                                'coupon' => 'Coupon',
                                'physical' => 'Physical Prize',
                                'points' => 'Points',
                                'free_spin' => 'Free Spin',
                                'nothing' => 'No Prize',
                            ]),
                        Forms\Components\Toggle::make('is_claimed')
                            ->label('Claimed')
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $state ? $set('claimed_at', now()) : $set('claimed_at', null)),
                        Forms\Components\DateTimePicker::make('claimed_at')
                            ->label('Claimed At'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('spinAndWin.title')
                    ->label('Game')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('prize_label')
                    ->label('Prize')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prize_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                Tables\Columns\TextColumn::make('prize_value')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('winner_name')
                    ->label('Winner')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('winner_email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_claimed')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('claimed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Played At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('prize_type')
                    ->options([
                        'cash' => 'Cash',
                        'coupon' => 'Coupon',
                        'physical' => 'Physical Prize',
                        'points' => 'Points',
                        'free_spin' => 'Free Spin',
                        'nothing' => 'No Prize',
                    ]),
                Tables\Filters\TernaryFilter::make('is_claimed')
                    ->label('Claimed'),
                Tables\Filters\SelectFilter::make('spin_and_win_id')
                    ->label('Game')
                    ->relationship('spinAndWin', 'title'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpinResults::route('/'),
            'create' => Pages\CreateSpinResult::route('/create'),
            'edit' => Pages\EditSpinResult::route('/{record}/edit'),
        ];
    }
}
