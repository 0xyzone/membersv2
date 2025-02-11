<?php

namespace App\Filament\Resources\KymVerificationResource\Pages;

use App\Filament\Resources\KymVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKymVerifications extends ListRecords
{
    protected static string $resource = KymVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
