<?php

namespace App\Filament\Resources;

use App\Models\Giveaway;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Support\Str;

class GiveawayResource extends Resource
{
    protected static ?string $model = Giveaway::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewGiveaways() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Giveaway Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description')
                            ->required(),
                        Forms\Components\TextInput::make('prize')
                            ->required(),
                        Forms\Components\TextInput::make('prize_value')
                            ->numeric()
                            ->prefix('$'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Schedule & Limits')
                    ->schema([
                        Forms\Components\DateTimePicker::make('starts_at'),
                        Forms\Components\DateTimePicker::make('ends_at'),
                        Forms\Components\TextInput::make('max_entries')
                            ->numeric()
                            ->label('Max Entries'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'upcoming' => 'Upcoming',
                                'ended' => 'Ended',
                            ])
                            ->default('active'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Presentation')
                    ->schema([
                        Forms\Components\TextInput::make('image')
                            ->label('Image URL'),
                        Forms\Components\TextInput::make('color')
                            ->label('Theme Color')
                            ->placeholder('#D4AF37'),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('prize')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('prize_value')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('entry_count')
                    ->label('Entries')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'upcoming' => 'info',
                        'ended' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'upcoming' => 'Upcoming',
                        'ended' => 'Ended',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\GiveawayResource\RelationManagers\EntriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\GiveawayResource\Pages\ListGiveaways::route('/'),
            'create' => \App\Filament\Resources\GiveawayResource\Pages\CreateGiveaway::route('/create'),
            'edit' => \App\Filament\Resources\GiveawayResource\Pages\EditGiveaway::route('/{record}/edit'),
        ];
    }
}
