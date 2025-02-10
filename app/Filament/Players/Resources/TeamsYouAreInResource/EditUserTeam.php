<?php

namespace App\Filament\Players\Resources\TeamsYouAreInResource\Pages;

use App\Filament\Players\Resources\TeamsYouAreInResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserTeam extends EditRecord
{
    protected static string $resource = TeamsYouAreInResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
