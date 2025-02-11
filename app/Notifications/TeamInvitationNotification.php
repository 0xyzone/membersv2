<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Filament\Notifications\Actions\Action;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;

class TeamInvitationNotification extends Notification
{
    use Queueable;

    public $team;
    public $sender;
    public $role;

    /**
     * Create a new notification instance.
     */
    public function __construct($team, $sender, $role)
    {
        $this->team = $team;
        $this->sender = $sender;
        $this->role = $role;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        // Create the Filament notification
        $notification = FilamentNotification::make()
            ->title('Team Invitation')
            ->body("You have been invited to join the team '{$this->team->name}' by {$this->sender->name}.")
            ->icon('heroicon-o-user-group')
            ->actions([
                Action::make('accept')
                    ->button()
                    ->color('success')
                    ->url(route('invitation.action', ['team' => $this->team->id, 'action' => 'accept'])),
                Action::make('decline')
                    ->button()
                    ->color('danger')
                    ->url(route('invitation.action', ['team' => $this->team->id, 'action' => 'decline'])),
            ])
            ->toDatabase();

        // Convert the notification to an array
        $notificationArray = $notification->toArray();

        // Add team_id to the data array
        $notificationArray['data']['team_id'] = $this->team->id;

        return $notificationArray;

    }
}
