<?php

namespace App\Filament\Organizers\Resources\TournamentRegistrationResource\Pages;

use App\Filament\Organizers\Resources\TournamentRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTournamentRegistration extends EditRecord
{
    protected static string $resource = TournamentRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
