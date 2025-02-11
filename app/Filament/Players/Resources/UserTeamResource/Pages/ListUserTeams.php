<?php

namespace App\Filament\Players\Resources\UserTeamResource\Pages;

use App\Filament\Players\Resources\UserTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Hydrat\TableLayoutToggle\Concerns\HasToggleableTable;

class ListUserTeams extends ListRecords
{
    use HasToggleableTable;
    protected static string $resource = UserTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Create New Team'),
        ];
    }
}
