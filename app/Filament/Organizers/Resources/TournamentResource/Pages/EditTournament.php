<?php

namespace App\Filament\Organizers\Resources\TournamentResource\Pages;

use App\Filament\Organizers\Resources\TournamentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTournament extends EditRecord
{
    protected static string $resource = TournamentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
