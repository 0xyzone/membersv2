<?php

namespace App\Filament\Resources\KymVerificationResource\Pages;

use App\Filament\Resources\KymVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKymVerification extends EditRecord
{
    protected static string $resource = KymVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
