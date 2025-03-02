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
                    $this->record->update(['status' => 'approved']);

                    Notification::make()
                        ->title('Registration Approved')
                        ->body('Team has been approved for the tournament')
                        ->success()
                        ->send();
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
                        'status' => 'declined',
                        'notes' => $data['reason']
                    ]);

                    Notification::make()
                        ->title('Registration Declined')
                        ->body('Reason: ' . $data['reason'])
                        ->danger()
                        ->send();
                })
                ->visible(fn() => $this->record->status === 'pending'),
        ];
    }
}
