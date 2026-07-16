<?php

namespace App\Filament\Resources\SpinAndWinResource\Pages;

use App\Filament\Resources\SpinAndWinResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSpinAndWins extends ListRecords
{
    protected static string $resource = SpinAndWinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
