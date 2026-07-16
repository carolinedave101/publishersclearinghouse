<?php

namespace App\Filament\Resources;

use App\Models\Winner;
use App\Services\ActivityLogger;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Mail\WinnerNotification;
use App\Helpers\EmailHelper;

class WinnerResource extends Resource
{
    protected static ?string $model = Winner::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewWinners() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required(),
                        Forms\Components\TextInput::make('last_name')
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->email(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('address'),
                        Forms\Components\TextInput::make('city'),
                        Forms\Components\TextInput::make('state'),
                        Forms\Components\TextInput::make('zip'),
                        Forms\Components\DatePicker::make('date_of_birth'),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'M' => 'Male',
                                'F' => 'Female',
                                'O' => 'Other',
                            ]),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Prize Information')
                    ->schema([
                        Forms\Components\TextInput::make('prize_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Textarea::make('prize_description'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Status & Financial Control')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'under_review' => 'Under Review',
                                'documents_needed' => 'Documents Needed',
                                'processing' => 'Processing',
                                'approved' => 'Approved',
                                'delivered' => 'Delivered',
                            ]),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active'),
                        Forms\Components\Toggle::make('is_claimed')
                            ->label('Claimed'),
                        Forms\Components\DateTimePicker::make('claimed_at')
                            ->label('Claimed Date (backdate)'),
                        Forms\Components\TextInput::make('unique_code')
                            ->readOnly()
                            ->visibleOn('edit'),
                        Forms\Components\TextInput::make('prize_amount')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->extraAttributes(['class' => 'font-bold']),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Notes')
                    ->schema([
                        Forms\Components\RichEditor::make('next_steps')
                            ->helperText('Visible to winner'),
                        Forms\Components\RichEditor::make('admin_notes')
                            ->helperText('Internal only'),
                    ])
                    ->columns(2),
                Forms\Components\Section::make('Demographics & Marketing Data')
                    ->description('Additional demographic and marketing information from data enrichment.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\KeyValue::make('demographics')
                            ->keyLabel('Field')
                            ->valueLabel('Value'),
                    ]),
                Forms\Components\Section::make('Feature Overrides')
                    ->description('Override global winner features for this specific winner. Leave toggles unchanged to use global defaults.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Fieldset::make('Feature Sections')
                            ->schema([
                                Forms\Components\Toggle::make('features.show_messages')
                                    ->label('Messages'),
                                Forms\Components\Toggle::make('features.show_documents')
                                    ->label('Documents'),
                                Forms\Components\Toggle::make('features.show_deposits')
                                    ->label('Deposits'),
                                Forms\Components\Toggle::make('features.show_withdrawals')
                                    ->label('Withdrawals'),
                                Forms\Components\Toggle::make('features.show_transactions')
                                    ->label('Transaction History'),
                                Forms\Components\Toggle::make('features.show_orders')
                                    ->label('My Orders'),
                            ])->columns(2),
                        Forms\Components\Fieldset::make('Data Visibility')
                            ->schema([
                                Forms\Components\Toggle::make('features.show_dates')
                                    ->label('Dates'),
                                Forms\Components\Toggle::make('features.show_balance_summary')
                                    ->label('Balance Summary Cards'),
                                Forms\Components\Toggle::make('features.show_winner_code')
                                    ->label('Winner Code'),
                                Forms\Components\Toggle::make('features.show_next_steps')
                                    ->label('Next Steps'),
                                Forms\Components\Toggle::make('features.show_quick_actions')
                                    ->label('Quick Action Cards'),
                            ])->columns(2),
                        Forms\Components\Fieldset::make('External Links')
                            ->schema([
                                Forms\Components\Toggle::make('features.show_giveaways')
                                    ->label('Giveaways Link'),
                                Forms\Components\Toggle::make('features.show_games')
                                    ->label('Games / Spin & Win Link'),
                                Forms\Components\Toggle::make('features.show_shop')
                                    ->label('Shop Link'),
                                Forms\Components\Toggle::make('features.show_memberships')
                                    ->label('Memberships Link'),
                            ])->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->orderBy('is_demo', 'desc')
                ->orderBy('created_at', 'desc')
                ->withCount(['sentCampaignRecipients as campaigns_sent_count'])
            )
            ->searchable()
            ->searchPlaceholder('Search by name, code, or email...')
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unique_code')
                    ->searchable()
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
                Tables\Columns\IconColumn::make('is_claimed')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_demo')
                    ->label('Demo')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseIcon('heroicon-o-x-mark')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('campaigns_sent_count')
                    ->label('Campaigns')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(fn ($state) => "{$state} Campaign" . ($state != 1 ? 's' : ''))
                    ->sortable()
                    ->action(
                        \Filament\Tables\Actions\Action::make('viewCampaigns')
                            ->modalHeading(fn (Winner $record) => "Campaigns: {$record->first_name} {$record->last_name}")
                            ->modalContent(function (Winner $record) {
                                $history = $record->campaign_history;
                                if (empty($history)) {
                                    return view('filament.tables.campaign-history-modal', ['history' => [], 'name' => "{$record->first_name} {$record->last_name}"]);
                                }
                                return view('filament.tables.campaign-history-modal', ['history' => $history, 'name' => "{$record->first_name} {$record->last_name}"]);
                            })
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'under_review' => 'Under Review',
                        'documents_needed' => 'Documents Needed',
                        'processing' => 'Processing',
                        'approved' => 'Approved',
                        'delivered' => 'Delivered',
                    ]),
                TernaryFilter::make('is_active'),
                TernaryFilter::make('is_claimed'),
            ])
            ->actions([
                Tables\Actions\Action::make('sendEmail')
                    ->label('Send Email')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('subject')->required()->maxLength(255),
                        Forms\Components\RichEditor::make('message')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                            ->grow(),
                    ])
                    ->action(function (Winner $record, array $data): void {
                        if (empty($record->email)) {
                            \Filament\Notifications\Notification::make()
                                ->title('No email')
                                ->body('This winner has no email address on file.')
                                ->danger()->send();
                            return;
                        }

                        EmailHelper::send(
                            new WinnerNotification($record, $data['subject'], $data['message']),
                            $record->email,
                            $record->first_name
                        );

                        app(ActivityLogger::class)->log(
                            'admin_email',
                            'winners',
                            (string) $record->id,
                            auth()->id(),
                            null,
                            "Admin emailed {$record->first_name} {$record->last_name}: {$data['subject']}"
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Email sent')
                            ->body("Email sent to {$record->first_name} {$record->last_name}")
                            ->success()->send();
                    }),
                Tables\Actions\Action::make('resetClaim')
                    ->label('Reset Claim')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Winner $record) => $record->is_claimed)
                    ->action(function (Winner $record): void {
                        $record->update(['is_claimed' => false, 'claimed_at' => null]);
                        \Filament\Notifications\Notification::make()
                            ->title('Claim reset')->success()->send();
                    }),
                Tables\Actions\Action::make('regenerateCode')
                    ->label('New Code')
                    ->icon('heroicon-o-key')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (Winner $record): void {
                        $record->update([
                            'unique_code' => app(\App\Services\CodeGenerator::class)->generateUniqueCode(),
                        ]);
                        \Filament\Notifications\Notification::make()
                            ->title('Code regenerated')->success()->send();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('emailSelected')
                    ->label('Email Selected')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('subject')->required()->maxLength(255),
                        Forms\Components\RichEditor::make('message')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'underline', 'bulletList', 'orderedList'])
                            ->grow(),
                    ])
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                        $sent = 0;
                        $skipped = 0;
                        foreach ($records as $record) {
                            if (empty($record->email)) {
                                $skipped++;
                                continue;
                            }
                            EmailHelper::send(
                                new WinnerNotification($record, $data['subject'], $data['message']),
                                $record->email,
                                $record->first_name
                            );

                            app(\App\Services\ActivityLogger::class)->log(
                                'admin_email',
                                'winners',
                                (string) $record->id,
                                auth()->id(),
                                null,
                                "Admin emailed {$record->first_name} {$record->last_name}: {$data['subject']}"
                            );

                            $sent++;
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Emails sent')
                            ->body("{$sent} winner(s) emailed." . ($skipped ? " {$skipped} skipped (no email on file)." : ''))
                            ->success()->send();
                    }),
                Tables\Actions\BulkAction::make('setActive')
                    ->label('Activate')
                    ->icon('heroicon-o-check')
                    ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => true])),
                Tables\Actions\BulkAction::make('setInactive')
                    ->label('Deactivate')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_active' => false])),
                Tables\Actions\BulkAction::make('markClaimed')
                    ->label('Mark Claimed')
                    ->icon('heroicon-o-check-badge')
                    ->action(fn (\Illuminate\Database\Eloquent\Collection $records) => $records->each->update(['is_claimed' => true, 'claimed_at' => now()])),
                Tables\Actions\BulkAction::make('updateStatus')
                    ->label('Set Status')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->form([
                        Forms\Components\Select::make('status')->options([
                            'new' => 'New',
                            'under_review' => 'Under Review',
                            'documents_needed' => 'Documents Needed',
                            'processing' => 'Processing',
                            'approved' => 'Approved',
                            'delivered' => 'Delivered',
                        ])->required(),
                    ])
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                        $records->each->update(['status' => $data['status']]);
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\WinnerResource\RelationManagers\WinnerMessagesRelationManager::class,
            \App\Filament\Resources\WinnerResource\RelationManagers\WinnerDocumentsRelationManager::class,
            \App\Filament\Resources\WinnerResource\RelationManagers\WinnerDepositsRelationManager::class,
            \App\Filament\Resources\WinnerResource\RelationManagers\WinnerWithdrawalsRelationManager::class,
            \App\Filament\Resources\WinnerResource\RelationManagers\WinnerTransactionsRelationManager::class,
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'unique_code', 'email'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return "{$record->first_name} {$record->last_name}";
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Code' => $record->unique_code,
            'Email' => $record->email ?? '—',
            'Status' => ucfirst(str_replace('_', ' ', $record->status)),
            'Balance' => '$' . number_format($record->available_balance, 2),
        ];
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return static::getUrl('edit', ['record' => $record]);
    }

    public static function getGlobalSearchResults(string $search): Collection
    {
        $query = static::getEloquentQuery();

        if (strlen(trim($search)) === 0) {
            return collect();
        }

        $query->where(function (Builder $q) use ($search) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('unique_code', 'like', "%{$search}%");
        });

        return $query->limit(static::getGlobalSearchResultsLimit())->get();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWinners::route('/'),
            'create' => CreateWinner::route('/create'),
            'edit' => EditWinner::route('/{record}/edit'),
        ];
    }
}

class ListWinners extends \Filament\Resources\Pages\ListRecords
{
    protected static string $resource = WinnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}

class CreateWinner extends \Filament\Resources\Pages\CreateRecord
{
    protected static string $resource = WinnerResource::class;

    protected function afterCreate(): void
    {
        app(ActivityLogger::class)->log(
            'created',
            'winners',
            null,
            auth()->id(),
            null,
            "Winner {$this->record->first_name} {$this->record->last_name} was created"
        );
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['features'] = \App\Models\Setting::getWinnerFeaturesConfig();
        return $data;
    }
}

class EditWinner extends \Filament\Resources\Pages\EditRecord
{
    protected static string $resource = WinnerResource::class;

    protected function afterSave(): void
    {
        app(ActivityLogger::class)->log(
            'updated',
            'winners',
            null,
            auth()->id(),
            $this->record->getChanges(),
            "Winner {$this->record->first_name} {$this->record->last_name} was updated"
        );
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!isset($data['features']) || $data['features'] === null) {
            $data['features'] = \App\Models\Setting::getWinnerFeaturesConfig();
        }
        return $data;
    }
}
