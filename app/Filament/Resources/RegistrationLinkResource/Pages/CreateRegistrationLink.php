<?php

namespace App\Filament\Resources\RegistrationLinkResource\Pages;

use App\Filament\Resources\RegistrationLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistrationLink extends CreateRecord
{
    protected static string $resource = RegistrationLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to Links')
                ->url(RegistrationLinkResource::getUrl('index')),
        ];
    }
}