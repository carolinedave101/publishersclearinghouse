<?php

namespace App\Filament\Resources\EmailCampaignResource\RelationManagers;

use App\Jobs\SendCampaignEmail;
use App\Models\EmailCampaignRecipient;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CampaignRecipientsRelationManager extends RelationManager
{
    protected static string $relationship = 'recipients';

    protected static ?string $title = 'Recipients';

    protected static ?string $recordTitleAttribute = 'email';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('winner'))
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied'),
                Tables\Columns\TextColumn::make('first_name')
                    ->label('Name')
                    ->formatStateUsing(fn (EmailCampaignRecipient $record): string => $record->winner
                        ? "{$record->winner->first_name} {$record->winner->last_name}"
                        : ($record->first_name ?: '—')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'sending' => 'warning',
                        'sent' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('body_variant_used')
                    ->label('Variant')
                    ->formatStateUsing(fn ($state) => $state ? "V{$state}" : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(50)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->action(function (EmailCampaignRecipient $record): void {
                        $record->update(['status' => 'pending', 'error_message' => null]);
                        SendCampaignEmail::dispatch($record->campaign, $record);
                        Notification::make()->title('Retrying...')->success()->send();
                    })
                    ->visible(fn (EmailCampaignRecipient $record): bool => $record->status === 'failed'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('retrySelected')
                    ->label('Retry Selected')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                        $count = 0;
                        foreach ($records as $recipient) {
                            if ($recipient->status === 'failed') {
                                $recipient->update(['status' => 'pending', 'error_message' => null]);
                                SendCampaignEmail::dispatch($recipient->campaign, $recipient);
                                $count++;
                            }
                        }
                        Notification::make()
                            ->title("Retrying {$count} recipient(s)")
                            ->success()->send();
                    }),
            ]);
    }
}
