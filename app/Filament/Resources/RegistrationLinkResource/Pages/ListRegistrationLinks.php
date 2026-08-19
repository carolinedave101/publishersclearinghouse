<?php

namespace App\Filament\Resources\RegistrationLinkResource\Pages;

use App\Filament\Resources\RegistrationLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrationLinks extends ListRecords
{
    protected static string $resource = RegistrationLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}