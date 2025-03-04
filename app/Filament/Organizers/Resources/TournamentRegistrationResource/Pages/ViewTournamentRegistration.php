<?php

namespace App\Filament\Organizers\Resources\TournamentRegistrationResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Organizers\Resources\TournamentRegistrationResource;

class ViewTournamentRegistration extends ViewRecord
{
    protected static string $resource = TournamentRegistrationResource::class;
    public function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('approve')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->action(function () {
                    $this->record->update([
                        'status' => 'approved',
                        'notes' => null
                    ]);

                    Notification::make()
                        ->title('Registration Approved')
                        ->body('Team has been approved for the tournament')
                        ->success()
                        ->send();

                    // Get the team and owner
                    $team = $this->record->team;
                    $owner = $team->owner;
                    $players = $this->record->players;

                    // Combine owner and players, filter nulls
                    $recipients = collect([$owner])
                        ->merge($players)
                        ->filter();

                    if ($recipients->isNotEmpty()) {
                        Notification::make()
                            ->title('Tournament Registration Approved')
                            ->body("Team '{$team?->name}' has been approved for the tournament {$this->record->tournament->name}")
                            ->success()
                            ->sendToDatabase($recipients);
                    }
                })
                ->visible(fn() => $this->record->status === 'pending'),

            \Filament\Actions\Action::make('decline')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->form([
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for Decline')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'status' => 'rejected',
                        'notes' => $data['reason']
                    ]);

                    Notification::make()
                        ->title('Registration Declined')
                        ->body('Reason: ' . $data['reason'])
                        ->danger()
                        ->send();

                    // Get the team and owner
                    $team = $this->record->team;
                    $owner = $team->owner;
                    $players = $this->record->players;

                    // Combine owner and players, filter nulls
                    $recipients = collect([$owner])
                        ->merge($players)
                        ->filter();

                    if ($recipients->isNotEmpty()) {
                        Notification::make()
                            ->title('Tournament Registration Declined')
                            ->body("Team '{$team?->name}' was declined for the tournament {$this->record->tournament->name}. Reason: " . $data['reason'])
                            ->danger()
                            ->sendToDatabase($recipients);
                    }
                })
                ->visible(fn() => $this->record->status === 'pending'),
        ];
    }
}
