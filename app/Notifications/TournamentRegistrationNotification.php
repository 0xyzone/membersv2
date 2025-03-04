<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\TournamentRegistration;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;
use App\Filament\Players\Resources\TournamentRegistrationResource;

class TournamentRegistrationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TournamentRegistration $registration
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('New Tournament Registration')
        ->markdown('emails.tournament-registrations', [
            'registration' => $this->registration,
            'url' => route('filament.organizers.resources.tournament-registrations.view', $this->registration)
        ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Tournament Registration')
            ->icon('heroicon-o-user-group')
            ->iconColor('success')
            ->body("Team {$this->registration->team->name} registered for {$this->registration->tournament->name}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('View Registration')
                    ->url(route('filament.organizers.resources.tournament-registrations.view', $this->registration))
            ])
            ->getDatabaseMessage();
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'tournament_registration',
            'tournament' => $this->registration->tournament->name,
            'team' => $this->registration->team->name,
            'registration_date' => $this->registration->created_at->toDateTimeString(),
            'message' => 'New registration for your tournament: ' . $this->registration->tournament->name,
        ];
    }

    public function shouldSend(object $notifiable, string $channel): bool
    {
        // Only send notifications for pending registrations
        return $this->registration->status === 'pending';
    }
}