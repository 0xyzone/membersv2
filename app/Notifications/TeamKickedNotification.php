<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Filament\Notifications\Actions\Action;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification as FilamentNotification;

class TeamKickedNotification extends Notification
{
    use Queueable;

    public $team;
    public $kicker;  // Renamed from $sender for clarity

    public function __construct($team, $kicker)
    {
        $this->team = $team;
        $this->kicker = $kicker;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return FilamentNotification::make()
            ->title('Removed from Team')
            ->body("You were removed from '{$this->team->name}' by {$this->kicker->name}")
            ->icon('heroicon-o-user-minus')
            ->color('danger')
            ->toDatabase();
    }
}