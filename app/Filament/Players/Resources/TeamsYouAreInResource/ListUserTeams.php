<?php

namespace App\Filament\Players\Resources\TeamsYouAreInResource\Pages;

use App\Filament\Players\Resources\TeamsYouAreInResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserTeams extends ListRecords
{
    protected static string $resource = TeamsYouAreInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create New Team'),
        ];
    }
}
