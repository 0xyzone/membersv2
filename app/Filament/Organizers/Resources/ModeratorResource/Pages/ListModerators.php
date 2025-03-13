<?php

namespace App\Filament\Organizers\Resources\ModeratorResource\Pages;

use App\Filament\Organizers\Resources\ModeratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListModerators extends ListRecords
{
    protected static string $resource = ModeratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
