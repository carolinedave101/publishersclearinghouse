<?php

namespace App\Filament\Resources\SpinAndWinResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SegmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'segments';

    protected static ?string $title = 'Wheel Segments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Segment Details')
                    ->schema([
                        Forms\Components\TextInput::make('label')
                            ->required()
                            ->maxLength(100)
                            ->helperText('Display label on the wheel segment (e.g. "$10 Cash", "Try Again")'),
                        Forms\Components\ColorPicker::make('color')
                            ->required()
                            ->default('#D4AF37'),
                        Forms\Components\Select::make('prize_type')
                            ->required()
                            ->options([
                                'cash' => 'Cash',
                                'coupon' => 'Coupon',
                                'physical' => 'Physical Prize',
                                'points' => 'Points',
                                'free_spin' => 'Free Spin',
                                'nothing' => 'No Prize',
                            ])
                            ->default('nothing'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Prize Details')
                    ->schema([
                        Forms\Components\TextInput::make('prize_value')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->label('Prize Value ($ or points)'),
                        Forms\Components\Textarea::make('prize_description')
                            ->label('Prize Description'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Probability & Display')
                    ->schema([
                        Forms\Components\TextInput::make('weight')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(1000)
                            ->default(10)
                            ->label('Weight (probability)')
                            ->helperText('Higher weight = more likely to land here. Total of all weights = 100%.'),
                        Forms\Components\Toggle::make('is_jackpot')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\TextColumn::make('prize_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('prize_value')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_jackpot')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_jackpot')
                    ->label('Jackpot'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
