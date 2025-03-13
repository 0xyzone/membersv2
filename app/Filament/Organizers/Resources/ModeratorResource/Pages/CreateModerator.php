<?php

namespace App\Filament\Organizers\Resources\ModeratorResource\Pages;

use Filament\Actions;
use App\Models\Moderator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Organizers\Resources\ModeratorResource;

class CreateModerator extends CreateRecord
{
    protected static string $resource = ModeratorResource::class;

    protected function afterCreate()
    {
        $moderator = $this->record;
        // Send notification to the added moderator
        Notification::make()
        ->title('You\'ve been added as a moderator')
        ->body(auth()->user()->name . ' has added you as a moderator')
        ->sendToDatabase($moderator->moderator);
    }
}
