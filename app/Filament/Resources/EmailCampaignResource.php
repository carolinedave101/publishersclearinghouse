<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailCampaignResource\Pages;
use App\Models\EmailCampaign;
use App\Models\Winner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class EmailCampaignResource extends Resource
{
    protected static ?string $model = EmailCampaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Portal';

    protected static ?int $navigationSort = 14;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewEmailCampaigns() ?? false;
    }

    public static function form(Form $form): Form
    {
        $states = Winner::whereNotNull('state')
            ->distinct()
            ->orderBy('state')
            ->pluck('state')
            ->toArray();

        return $form
            ->schema([
                Forms\Components\Section::make('Campaign Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Campaign Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Holiday Greetings 2026'),
                        Forms\Components\TextInput::make('subject')
                            ->label('Email Subject')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Your Special Prize Update – {{name}}'),
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Schedule Start (optional)')
                            ->helperText('Leave empty to start immediately when campaign is launched.'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Email Body Variants')
                    ->description('Write 1–3 variants. The system randomly assigns one per recipient to improve deliverability. Use {name} or {firstName} for personalization.')
                    ->schema([
                        Forms\Components\RichEditor::make('body_variant_1')
                            ->label('Variant 1 (Primary)')
                            ->required()
                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'blockquote', 'link'])
                            ->grow(),
                        Forms\Components\RichEditor::make('body_variant_2')
                            ->label('Variant 2 (Alternative — optional)')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'blockquote', 'link'])
                            ->grow(),
                        Forms\Components\RichEditor::make('body_variant_3')
                            ->label('Variant 3 (Alternative — optional)')
                            ->toolbarButtons(['bold', 'italic', 'underline', 'strike', 'bulletList', 'orderedList', 'blockquote', 'link'])
                            ->grow(),
                    ]),

                Forms\Components\Section::make('Recipient Filters')
                    ->description('Select which winners will receive this campaign.')
                    ->schema([
                        Forms\Components\Select::make('recipient_filter.statuses')
                            ->label('Winner Statuses')
                            ->multiple()
                            ->options([
                                'new' => 'New',
                                'under_review' => 'Under Review',
                                'documents_needed' => 'Documents Needed',
                                'processing' => 'Processing',
                                'approved' => 'Approved',
                                'delivered' => 'Delivered',
                            ])
                            ->helperText('Leave empty to include all statuses.'),
                        Forms\Components\Select::make('recipient_filter.is_demo')
                            ->label('Demo Winners')
                            ->options([
                                '' => 'Include all',
                                'exclude' => 'Exclude demo winners',
                                'only' => 'Only demo winners',
                            ])
                            ->default(''),
                        Forms\Components\Select::make('recipient_filter.claim_status')
                            ->label('Claim Status')
                            ->options([
                                '' => 'All winners',
                                'claimed' => 'Only claimed',
                                'unclaimed' => 'Only unclaimed',
                            ])
                            ->default(''),
                        Forms\Components\TextInput::make('recipient_filter.prize_min')
                            ->label('Min Prize Amount ($)')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('recipient_filter.prize_max')
                            ->label('Max Prize Amount ($)')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('recipient_filter.states')
                            ->label('States')
                            ->multiple()
                            ->options(array_combine($states, $states))
                            ->searchable()
                            ->helperText('Leave empty to include all states.'),
                        Forms\Components\DatePicker::make('recipient_filter.created_from')
                            ->label('Created From'),
                        Forms\Components\DatePicker::make('recipient_filter.created_until')
                            ->label('Created Until'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Rate Limiting')
                    ->description('Control how fast emails are sent to avoid hitting SMTP limits.')
                    ->schema([
                        Forms\Components\TextInput::make('rate_per_hour')
                            ->label('Emails per Hour')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(500)
                            ->default(50)
                            ->suffix('emails/hr')
                            ->helperText('StackMail typically handles 50–100/hr safely.'),
                        Forms\Components\TextInput::make('rate_per_day')
                            ->label('Emails per Day')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->maxValue(10000)
                            ->default(1000)
                            ->suffix('emails/day')
                            ->helperText('Max emails sent in a 24-hour period. Resets at midnight.'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sending' => 'warning',
                        'sent' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('total_recipients')
                    ->label('Total')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Sent')
                    ->sortable()
                    ->numeric()
                    ->color('success'),
                Tables\Columns\TextColumn::make('failed_count')
                    ->label('Failed')
                    ->sortable()
                    ->numeric()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('progress_percent')
                    ->label('Progress')
                    ->badge()
                    ->color(fn (float $state): string => $state >= 100 ? 'success' : ($state > 0 ? 'warning' : 'gray'))
                    ->formatStateUsing(fn (float $state): string => "{$state}%"),
                Tables\Columns\TextColumn::make('rate_per_hour')
                    ->label('Rate')
                    ->formatStateUsing(fn ($record) => "{$record->rate_per_hour}/hr • {$record->rate_per_day}/day")
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->sortable()
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sending' => 'Sending',
                        'sent' => 'Sent',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->label('Send')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function (EmailCampaign $record) {
                        $record->update(['status' => 'sending', 'started_at' => now()]);
                        self::resolveRecipients($record);
                        \App\Jobs\DispatchCampaign::dispatch($record, false);
                        \Filament\Notifications\Notification::make()
                            ->title('Campaign started')
                            ->body("Campaign '{$record->name}' is now sending.")
                            ->success()->send();
                    })
                    ->visible(fn (EmailCampaign $record) => $record->status === 'draft'),
                Tables\Actions\Action::make('sendAgain')
                    ->label('Send Again')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Re-send campaign')
                    ->modalDescription('This will clear all existing recipients and re-send the campaign. Continue?')
                    ->action(function (EmailCampaign $record) {
                        $record->recipients()->delete();
                        $record->update([
                            'status' => 'sending',
                            'started_at' => now(),
                            'sent_count' => 0,
                            'failed_count' => 0,
                            'completed_at' => null,
                        ]);
                        self::resolveRecipients($record);
                        \App\Jobs\DispatchCampaign::dispatch($record, false);
                        \Filament\Notifications\Notification::make()
                            ->title('Campaign re-sent')
                            ->body("Campaign '{$record->name}' has been re-sent.")
                            ->success()->send();
                    })
                    ->visible(fn (EmailCampaign $record) => in_array($record->status, ['sent', 'cancelled'])),
                Tables\Actions\Action::make('sendTest')
                    ->label('Send Test')
                    ->icon('heroicon-o-beaker')
                    ->color('gray')
                    ->action(function (EmailCampaign $record) {
                        \App\Jobs\DispatchCampaign::dispatch($record, true);
                        \Filament\Notifications\Notification::make()
                            ->title('Test sent')
                            ->body('Test emails dispatched to demo winners.')
                            ->success()->send();
                    })
                    ->visible(fn (EmailCampaign $record) => $record->status === 'draft'),
                Tables\Actions\Action::make('pause')
                    ->label('Pause')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->action(function (EmailCampaign $record) {
                        $record->update(['status' => 'draft']);
                        \Filament\Notifications\Notification::make()
                            ->title('Campaign paused')
                            ->warning()->send();
                    })
                    ->visible(fn (EmailCampaign $record) => $record->status === 'sending'),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-stop')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (EmailCampaign $record) {
                        $record->update(['status' => 'cancelled', 'completed_at' => now()]);
                        \Filament\Notifications\Notification::make()
                            ->title('Campaign cancelled')
                            ->danger()->send();
                    })
                    ->visible(fn (EmailCampaign $record) => in_array($record->status, ['draft', 'sending'])),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->modalHeading('Delete Campaign')
                    ->modalDescription('This will permanently delete the campaign and all its recipients.')
                    ->action(function (EmailCampaign $record) {
                        $record->recipients()->delete();
                        $record->delete();
                        \Filament\Notifications\Notification::make()
                            ->title('Campaign deleted')
                            ->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function resolveRecipients(EmailCampaign $campaign): void
    {
        if ($campaign->recipients()->count() > 0) {
            return;
        }

        $filter = $campaign->recipient_filter ?? [];
        $query = Winner::query();

        if (!empty($filter['statuses'])) {
            $query->whereIn('status', $filter['statuses']);
        }

        if (!empty($filter['is_demo'])) {
            if ($filter['is_demo'] === 'exclude') {
                $query->where('is_demo', false);
            } elseif ($filter['is_demo'] === 'only') {
                $query->where('is_demo', true);
            }
        }

        if (!empty($filter['claim_status'])) {
            if ($filter['claim_status'] === 'claimed') {
                $query->where('is_claimed', true);
            } elseif ($filter['claim_status'] === 'unclaimed') {
                $query->where('is_claimed', false);
            }
        }

        if (!empty($filter['prize_min'])) {
            $query->where('prize_amount', '>=', (float) $filter['prize_min']);
        }

        if (!empty($filter['prize_max'])) {
            $query->where('prize_amount', '<=', (float) $filter['prize_max']);
        }

        if (!empty($filter['states'])) {
            $query->whereIn('state', $filter['states']);
        }

        if (!empty($filter['created_from'])) {
            $query->whereDate('created_at', '>=', $filter['created_from']);
        }

        if (!empty($filter['created_until'])) {
            $query->whereDate('created_at', '<=', $filter['created_until']);
        }

        $query->whereNotNull('email')->where('email', '!=', '');

        $winners = $query->get();

        $variantsCount = $campaign->body_variants_count;
        $now = now();
        $recipients = [];

        foreach ($winners as $winner) {
            $variantIndex = $variantsCount > 1 ? rand(1, $variantsCount) : 1;
            $recipients[] = [
                'campaign_id' => $campaign->id,
                'winner_id' => $winner->id,
                'email' => $winner->email,
                'first_name' => $winner->first_name,
                'body_variant_used' => $variantIndex,
                'status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $total = count($recipients);

        if ($total > 0) {
            DB::table('email_campaign_recipients')->insert($recipients);
        }

        $campaign->update(['total_recipients' => $total]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailCampaigns::route('/'),
            'create' => Pages\CreateEmailCampaign::route('/create'),
            'edit' => Pages\EditEmailCampaign::route('/{record}/edit'),
            'view' => Pages\ViewEmailCampaign::route('/{record}'),
        ];
    }
}
