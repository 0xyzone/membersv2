<?php

namespace App\Filament\Players\Resources\TournamentRegistrationResource\Pages;

use App\Filament\Players\Resources\TournamentRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTournamentRegistrations extends ListRecords
{
    protected static string $resource = TournamentRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
