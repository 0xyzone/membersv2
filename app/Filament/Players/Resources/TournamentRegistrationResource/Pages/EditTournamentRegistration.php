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

    protected function beforeSave(): void
    {
        $playersData = collect($this->data['players'])->mapWithKeys(function ($player) {
            return [
                $player['user_id'] => [
                    'user_team_id' => $this->record->team_id,
                    'custom_fields' => $player['custom_fields'] ?? []
                ]
            ];
        });

        $test = $this->record->players()->sync($playersData->toArray());
    }

    protected function onValidationError(ValidationException $exception): void
    {
        Notification::make()
            ->title($exception->getMessage())
            ->danger()
            ->send();
    }
}
