<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\TournamentRegistration;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;
use App\Filament\Players\Resources\TournamentRegistrationResource;

class TournamentPlayersRegistrationNotification extends Notification implements ShouldQueue
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
            ->subject('Tournament Registration Confirmation')
            ->greeting("Hello {$notifiable->name}!")
            ->line('You have been registered for the tournament:')
            ->line('**Tournament:** ' . $this->registration->tournament->name)
            ->line('**Team:** ' . $this->registration->team->name)
            ->action('View Registration', route('filament.players.resources.tournament-registrations.view', $this->registration))
            ->line('Thank you for participating!');
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Tournament Registration Confirmation')
            ->icon('heroicon-o-user-group')
            ->iconColor('success')
            ->body("You've been registered for {$this->registration->tournament->name} as part of team {$this->registration->team->name}")
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->label('View Registration')
                    ->url(route('filament.players.resources.tournament-registrations.view', $this->registration))
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