<?php

namespace App\Filament\Resources\EmailCampaignResource\Pages;

use App\Filament\Resources\EmailCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailCampaign extends EditRecord
{
    protected static string $resource = EmailCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['recipient_filter']) && is_array($data['recipient_filter'])) {
            foreach ($data['recipient_filter'] as $key => $value) {
                $data["recipient_filter.{$key}"] = $value;
            }
        }
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['recipient_filter']) && is_array($data['recipient_filter'])) {
            $data['recipient_filter'] = array_filter($data['recipient_filter'], fn ($v) => $v !== '' && $v !== null && $v !== []);
        }
        return $data;
    }
}
