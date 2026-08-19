<?php

namespace App\Filament\Resources\RegistrationLinkResource\Pages;

use App\Filament\Resources\RegistrationLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRegistrationLink extends ViewRecord
{
    protected static string $resource = RegistrationLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('copy_link')
                ->label('Copy Registration Link')
                ->icon('heroicon-o-clipboard-document')
                ->color('primary')
                ->action(function () {
                    $url = url('/register') . '?source=' . $this->record->source;
                    $this->js("navigator.clipboard.writeText('{$url}'); Filament.notify('success', 'Registration link copied');");
                }),
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}