<?php

namespace App\Filament\Resources\EmailCampaignResource\Pages;

use App\Filament\Resources\EmailCampaignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailCampaign extends CreateRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['recipient_filter']) && is_array($data['recipient_filter'])) {
            $data['recipient_filter'] = array_filter($data['recipient_filter'], fn ($v) => $v !== '' && $v !== null && $v !== []);
        }
        return $data;
    }
}
