<?php

namespace App\Filament\Resources\EmailCampaignResource\Pages;

use App\Filament\Resources\EmailCampaignResource;
use App\Jobs\DispatchCampaign;
use App\Models\EmailCampaign;
use Filament\Actions;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailCampaign extends ViewRecord
{
    protected static string $resource = EmailCampaignResource::class;

    public EmailCampaign $campaign;

    public function mount(int|string $record): void
    {
        parent::mount($record);
        $this->campaign = $this->record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('send')
                ->label('Send Campaign')
                ->icon('heroicon-o-play')
                ->color('success')
                ->action(function () {
                    $this->campaign->update(['status' => 'sending', 'started_at' => now()]);
                    EmailCampaignResource::resolveRecipients($this->campaign);
                    DispatchCampaign::dispatch($this->campaign, false);
                    Notification::make()->title('Campaign started')->success()->send();
                    $this->redirect(static::getUrl(['record' => $this->campaign]));
                })
                ->visible(fn () => $this->campaign->status === 'draft'),

            Actions\Action::make('sendTest')
                ->label('Send Test to Demo')
                ->icon('heroicon-o-beaker')
                ->color('gray')
                ->action(function () {
                    $this->campaign->update(['status' => 'sending']);
                    DispatchCampaign::dispatch($this->campaign, true);
                    Notification::make()
                        ->title('Test dispatched')
                        ->body('Test emails being sent to demo winners.')
                        ->success()->send();
                })
                ->visible(fn () => $this->campaign->status === 'draft'),

            Actions\Action::make('pause')
                ->label('Pause')
                ->icon('heroicon-o-pause')
                ->color('warning')
                ->action(function () {
                    $this->campaign->update(['status' => 'draft']);
                    Notification::make()->title('Campaign paused')->warning()->send();
                    $this->redirect(static::getUrl(['record' => $this->campaign]));
                })
                ->visible(fn () => $this->campaign->status === 'sending'),

            Actions\Action::make('cancel')
                ->label('Cancel')
                ->icon('heroicon-o-stop')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->campaign->update(['status' => 'cancelled', 'completed_at' => now()]);
                    Notification::make()->title('Campaign cancelled')->danger()->send();
                    $this->redirect(static::getUrl(['record' => $this->campaign]));
                })
                ->visible(fn () => in_array($this->campaign->status, ['draft', 'sending'])),

            Actions\Action::make('retryFailed')
                ->label('Retry Failed (' . number_format($this->campaign->failed_count) . ')')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->action(function () {
                    foreach ($this->campaign->failedRecipients as $recipient) {
                        $recipient->update(['status' => 'pending', 'error_message' => null]);
                    }
                    DispatchCampaign::dispatch($this->campaign, false);
                    Notification::make()
                        ->title('Retrying')
                        ->body("Re-queued {$this->campaign->failedRecipients()->count()} failed recipients.")
                        ->success()->send();
                    $this->redirect(static::getUrl(['record' => $this->campaign]));
                })
                ->visible(fn () => $this->campaign->failed_count > 0 && $this->campaign->status !== 'sending'),

            Actions\Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->url(fn () => EmailCampaignResource::getUrl('edit', ['record' => $this->campaign]))
                ->visible(fn () => $this->campaign->status === 'draft'),

            Actions\Action::make('exportCsv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $headers = [
                        'Content-Type' => 'text/csv',
                        'Content-Disposition' => "attachment; filename=\"campaign-{$this->campaign->id}-recipients.csv\"",
                    ];
                    $callback = function () {
                        $handle = fopen('php://output', 'w');
                        fputcsv($handle, ['Email', 'First Name', 'Status', 'Sent At', 'Error']);
                        $this->campaign->recipients()->chunk(500, function ($recipients) use ($handle) {
                            foreach ($recipients as $r) {
                                fputcsv($handle, [
                                    $r->email,
                                    $r->first_name,
                                    $r->status,
                                    $r->sent_at?->toDateTimeString() ?? '',
                                    $r->error_message ?? '',
                                ]);
                            }
                        });
                        fclose($handle);
                    };
                    return response()->stream($callback, 200, $headers);
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\EmailCampaignResource\Widgets\CampaignStatsWidget::make(['campaign' => $this->campaign]),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            \App\Filament\Resources\EmailCampaignResource\RelationManagers\CampaignRecipientsRelationManager::class,
        ];
    }
}
