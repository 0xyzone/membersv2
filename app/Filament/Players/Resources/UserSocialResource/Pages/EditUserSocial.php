<?php

namespace App\Filament\Players\Resources\UserSocialResource\Pages;

use App\Filament\Players\Resources\UserSocialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserSocial extends EditRecord
{
    protected static string $resource = UserSocialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
