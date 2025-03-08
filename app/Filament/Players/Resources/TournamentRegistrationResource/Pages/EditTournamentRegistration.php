<?php

namespace App\Filament\Players\Resources\TournamentRegistrationResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;
use App\Filament\Players\Resources\TournamentRegistrationResource;

class EditTournamentRegistration extends EditRecord
{
    protected static string $resource = TournamentRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // This will automatically manage timestamps
        $this->record->players()->sync(
            collect($this->data['players'])->mapWithKeys(fn($userId) => [
                $userId => [
                    'user_team_id' => $this->record->team_id,
                    // created_at and updated_at added automatically
                ]
            ])
        );
    }
}
