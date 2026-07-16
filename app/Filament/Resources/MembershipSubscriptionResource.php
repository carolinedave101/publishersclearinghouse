<?php

namespace App\Filament\Resources;

use App\Models\MembershipSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class MembershipSubscriptionResource extends Resource
{
    protected static ?string $model = MembershipSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Memberships';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewMembershipSubscriptions() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Subscriber Information')
                    ->schema([
                        Forms\Components\TextInput::make('subscriber_name')
                            ->required(),
                        Forms\Components\TextInput::make('subscriber_email')
                            ->email()
                            ->required(),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Subscription Details')
                    ->schema([
                        Forms\Components\Select::make('membership_tier_id')
                            ->relationship('tier', 'name')
                            ->required()
                            ->label('Membership Tier'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'cancelled' => 'Cancelled',
                                'expired' => 'Expired',
                            ])
                            ->default('active'),
                        Forms\Components\DateTimePicker::make('starts_at'),
                        Forms\Components\DateTimePicker::make('ends_at'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscriber_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscriber_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tier.name')
                    ->label('Tier')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'cancelled' => 'danger',
                        'expired' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
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

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\MembershipSubscriptionResource\Pages\ListMembershipSubscriptions::route('/'),
            'create' => \App\Filament\Resources\MembershipSubscriptionResource\Pages\CreateMembershipSubscription::route('/create'),
            'edit' => \App\Filament\Resources\MembershipSubscriptionResource\Pages\EditMembershipSubscription::route('/{record}/edit'),
        ];
    }
}
