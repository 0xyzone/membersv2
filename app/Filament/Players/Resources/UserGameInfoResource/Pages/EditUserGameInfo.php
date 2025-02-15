<?php

namespace App\Filament\Players\Resources\UserGameInfoResource\Pages;

use App\Filament\Players\Resources\UserGameInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserGameInfo extends EditRecord
{
    protected static string $resource = UserGameInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
