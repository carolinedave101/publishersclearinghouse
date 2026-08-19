<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationLinkResource\Pages;
use App\Filament\Resources\RegistrationLinkResource\RelationManagers\WinnersRelationManager;
use App\Models\RegistrationLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class RegistrationLinkResource extends Resource
{
    protected static ?string $model = RegistrationLink::class;

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 15;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewRegistrationLinks() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Link Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Link Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Facebook June Campaign')
                            ->helperText('Internal name so you can recognize this link in reports.'),
                        Forms\Components\TextInput::make('source')
                            ->label('Source Key')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('e.g., facebook-june')
                            ->regex('/^[a-z0-9_-]+$/')
                            ->helperText('Letters, numbers, dashes and underscores only (lowercase). Used in the shareable URL.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive links stop tagging new registrations.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->withCount('winners as registrations_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Link Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->label('Source Key')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Source key copied'),
                Tables\Columns\TextColumn::make('share_url')
                    ->label('Registration Link')
                    ->formatStateUsing(fn (RegistrationLink $record) => url('/register') . '?source=' . $record->source)
                    ->color('primary')
                    ->limit(45)
                    ->copyable()
                    ->copyMessage('Registration link copied'),
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Registrations')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "{$state} winner" . ($state != 1 ? 's' : '')),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            WinnersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrationLinks::route('/'),
            'create' => Pages\CreateRegistrationLink::route('/create'),
            'edit' => Pages\EditRegistrationLink::route('/{record}/edit'),
            'view' => Pages\ViewRegistrationLink::route('/{record}'),
        ];
    }

    public static function getNavigationLabel(): string
    {
        return 'Registration Links';
    }

    public static function getNavigationBadge(): ?string
    {
        $count = RegistrationLink::count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}