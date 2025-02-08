<?php

namespace App\Filament\Players\Resources\UserTeamResource\Pages;

use App\Filament\Players\Resources\UserTeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserTeam extends EditRecord
{
    protected static string $resource = UserTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
