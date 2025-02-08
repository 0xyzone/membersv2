<?php

namespace App\Filament\Players\Resources\UserTeamResource\Pages;

use App\Filament\Players\Resources\UserTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserTeams extends ListRecords
{
    protected static string $resource = UserTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create New Team'),
        ];
    }
}
