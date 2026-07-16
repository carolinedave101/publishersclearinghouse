<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SpinAndWinResource\Pages;
use App\Filament\Resources\SpinAndWinResource\RelationManagers\ResultsRelationManager;
use App\Filament\Resources\SpinAndWinResource\RelationManagers\SegmentsRelationManager;
use App\Models\SpinAndWin;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SpinAndWinResource extends Resource
{
    protected static ?string $model = SpinAndWin::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewSpinAndWin() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Game Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Textarea::make('description'),
                        Forms\Components\RichEditor::make('rules'),
                        Forms\Components\Textarea::make('success_message')
                            ->helperText('Use {prize} as a placeholder for the prize label.'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Limits & Requirements')
                    ->schema([
                        Forms\Components\TextInput::make('max_spins_per_day')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->default(3)
                            ->label('Max Spins Per Day'),
                        Forms\Components\TextInput::make('cooldown_minutes')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(1440)
                            ->default(0)
                            ->label('Cooldown Between Spins (minutes)')
                            ->helperText('0 = no cooldown'),
                        Forms\Components\Toggle::make('requires_login')
                            ->label('Requires Login to Play')
                            ->default(false),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Presentation')
                    ->schema([
                        Forms\Components\TextInput::make('image')
                            ->label('Image URL'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
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
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_spins_per_day')
                    ->label('Max/ Day')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cooldown_minutes')
                    ->label('Cooldown')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "{$state} min" : 'None'),
                Tables\Columns\TextColumn::make('segments_count')
                    ->counts('segments')
                    ->label('Segments'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
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
            SegmentsRelationManager::class,
            ResultsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpinAndWins::route('/'),
            'create' => Pages\CreateSpinAndWin::route('/create'),
            'edit' => Pages\EditSpinAndWin::route('/{record}/edit'),
        ];
    }
}
