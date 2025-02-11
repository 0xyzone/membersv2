<?php

namespace App\Filament\Players\Resources\UserTeamResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use App\Notifications\TeamInvitationNotification;
use App\Filament\Players\Resources\UserTeamResource;
use Filament\Notifications\Notification as FilamentNotification;

class EditUserTeam extends EditRecord
{
    protected static string $resource = UserTeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('invite')
                ->label('Invite Member')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->form([
                    Select::make('recipient_id')
                        ->label('User to invite')
                        ->options(User::where('id', '!=', auth()->id())
                            ->where('id', '!=', 1)
                            ->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Select::make('role')
                        ->options([
                            'player' => 'Player',
                            'substitute' => 'Substitute',
                        ])
                        ->default('player')
                ])
                ->action(function (array $data) {
                    $team = $this->record;
                    $recipient = User::findOrFail($data['recipient_id']);
                    $sender = auth()->user();

                    if (!$recipient->is_verified) {
                        FilamentNotification::make()
                            ->title('Unverified User')
                            ->body("{$recipient->name} is not verified. Please ask them to verify their account first.")
                            ->danger()
                            ->send();
                        return;
                    }

                    // Existing validation logic
                    if (
                        $team->invitations()
                            ->where('recipient_id', $recipient->id)
                            ->whereIn('status', ['pending', 'accepted'])
                            ->exists()
                    ) {
                        FilamentNotification::make()
                            ->title('Invitation already exists!')
                            ->danger()
                            ->send();
                        return;
                    }
                    if ($team->members()->where('user_team_members.user_id', $recipient->id)->exists()) {
                        FilamentNotification::make()->danger()->title('Already a member!')->send();
                        return;
                    }

                    $isInSameGameTeam = $recipient->teams()
                        ->where('game_id', $team->game_id)
                        ->exists();

                    if ($isInSameGameTeam) {
                        FilamentNotification::make()
                            ->title('User in Another Team')
                            ->body("{$recipient->name} is already part of another team in this game")
                            ->danger()
                            ->send();
                        return;
                    }

                    $team->invitations()->create([
                        'sender_id' => $sender->id,
                        'recipient_id' => $recipient->id,
                        'role' => $data['role'],
                        'status' => 'pending',
                    ]);

                    $recipient->notify(new TeamInvitationNotification($team, $sender, $data['role']));
                    FilamentNotification::make()
                        ->title('Invitation sent!')
                        ->success()
                        ->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
