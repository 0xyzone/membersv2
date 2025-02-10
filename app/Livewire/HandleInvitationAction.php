<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\UserTeam;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class HandleInvitationAction extends Component
{
    public $team;
    public $action;

    public function mount($team, $action)
    {
        $this->team = UserTeam::findOrFail($team);
        $this->action = $action;
    }

    public function handleAction()
    {
        $user = Auth::user();
        // Validation: Ensure the invitation exists and is pending
        $invitation = $this->team->invitations()
            ->where('recipient_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if (!$invitation) {
            Notification::make()
                ->title('Invalid or Expired Invitation')
                ->body('The invitation is no longer valid or has expired.')
                ->danger()
                ->send();
            return redirect()->route('filament.players.pages.dashboard');
        }

        // Validation: Prevent multiple acceptances
        if ($this->team->members()->where('user_team_members.user_id', $user->id)->exists()) {
            Notification::make()
                ->title('Already a Team Member')
                ->body('You are already a member of this team.')
                ->danger()
                ->send();
            return redirect()->route('filament.players.pages.dashboard');
        }

        if ($this->action === 'accept') {
            // Add the user to the team
            $this->team->members()->attach($user->id, ['role' => $invitation->role]);

            // Update the invitation status
            $invitation->update(['status' => 'accepted']);
            $teamId = $this->team->id;

            $this->deleteNotificationForInvitation($user, $teamId);

            Notification::make()
                ->title('Invitation Accepted')
                ->body('You have successfully joined the team as a ' . $invitation->role . '.')
                ->success()
                ->send();
        } elseif ($this->action === 'decline') {
            // Update the invitation status
            $invitation->update(['status' => 'declined']);

            $this->deleteNotificationForInvitation($user, $this->team->id);

            Notification::make()
                ->title('Invitation Declined')
                ->body('You have declined the invitation.')
                ->success()
                ->send();
        }

        return redirect()->route('filament.players.pages.dashboard');
    }

    protected function deleteNotificationForInvitation($user, $teamId)
    {
        // Find and delete the notification using team_id
        $user->notifications()
            ->where('data->data->team_id', $teamId)
            ->delete();
    }
    public function render()
    {
        // dd('rendered');
        return view('livewire.handle-invitation-action');
    }
}
