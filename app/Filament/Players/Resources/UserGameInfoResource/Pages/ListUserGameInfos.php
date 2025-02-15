<?php

namespace App\Filament\Players\Resources\UserGameInfoResource\Pages;

use App\Filament\Players\Resources\UserGameInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserGameInfos extends ListRecords
{
    protected static string $resource = UserGameInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
