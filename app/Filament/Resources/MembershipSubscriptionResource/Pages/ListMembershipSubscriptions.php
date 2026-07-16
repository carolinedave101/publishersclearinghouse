<?php

namespace App\Filament\Resources\MembershipSubscriptionResource\Pages;

use App\Filament\Resources\MembershipSubscriptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMembershipSubscriptions extends ListRecords
{
    protected static string $resource = MembershipSubscriptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
