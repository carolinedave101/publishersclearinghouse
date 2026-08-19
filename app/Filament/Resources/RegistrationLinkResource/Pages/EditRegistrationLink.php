<?php

namespace App\Filament\Resources\RegistrationLinkResource\Pages;

use App\Filament\Resources\RegistrationLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRegistrationLink extends EditRecord
{
    protected static string $resource = RegistrationLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('view')
                ->label('View Link')
                ->url(fn () => RegistrationLinkResource::getUrl('view', ['record' => $this->record])),
            Actions\DeleteAction::make(),
        ];
    }
}