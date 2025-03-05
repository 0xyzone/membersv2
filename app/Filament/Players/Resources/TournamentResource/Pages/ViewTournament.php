<?php

namespace App\Filament\Players\Resources\TournamentResource\Pages;

use App\Models\UserTeam;
use Filament\Actions\Action;
use App\Models\TournamentRegistration;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Validation\ValidationException;
use App\Filament\Players\Resources\TournamentResource;
use App\Notifications\TournamentRegistrationNotification;
use App\Notifications\TournamentPlayersRegistrationNotification;

class ViewTournament extends ViewRecord
{
    protected static string $resource = TournamentResource::class;
    public function getHeading(): string
    {
        return __($this->record->name);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('Register')
                ->modalSubheading(function ($record) {
                    $min = $record->min_team_players;
                    $max = $record->max_team_players;
                    return "Team must have between {$min} and {$max} players";
                })
                ->icon('heroicon-o-user-plus')
                ->form([
                    \Filament\Forms\Components\Select::make('team_id')
                        ->label('Select Team')
                        ->required()
                        ->options(function () {
                            $tournament = $this->getRecord();
                            $teams = auth()->user()->ownedTeams()
                                ->where('game_id', $tournament->game_id)
                                ->pluck('name', 'id');

                            if ($teams->isEmpty()) {
                                Notification::make()
                                    ->title('No Teams Available')
                                    ->body('Create a team for this game first!')
                                    ->danger()
                                    ->send();
                            }

                            return $teams;
                        })
                        ->hidden(fn() => !$this->userHasEligibleTeams())
                        ->live()
                        ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                            $set('selected_members', []);
                        }),

                    \Filament\Forms\Components\CheckboxList::make('selected_members')
                        ->label('Select Participating Players')
                        ->options(function (\Filament\Forms\Get $get) {
                            $team = UserTeam::find($get('team_id'));
                            // Get owner details
                            $owner = [
                                $team->owner->id => $team->owner->name . ' (Owner)'
                            ];

                            // Get team members with player role
                            $players = $team->members()
                                ->wherePivot('role', 'player')
                                ->pluck('name', 'user_team_members.user_id')
                                ->toArray();

                            // Combine owner and players, ensuring owner is first
                            return $owner + $players;
                        })
                        ->required()
                        ->rule(function (\Filament\Forms\Get $get) {
                            $tournament = $this->getRecord();
                            return [
                                'array',
                                'min:' . $tournament->min_team_players,
                                'max:' . $tournament->max_team_players,
                            ];
                        })
                        ->hidden(fn(\Filament\Forms\Get $get) => !$get('team_id'))
                        ->validationMessages([
                            'min' => 'Minimum :min players required',
                            'max' => 'Maximum :max players allowed',
                        ])
                ])
                ->action(function (array $data) {
                    $tournament = $this->getRecord();
                    $team = UserTeam::findOrFail($data['team_id']);

                    // Validate registration period
                    if (!now()->between($tournament->registration_start_date, $tournament->registration_end_date)) {
                        throw ValidationException::withMessages([
                            'team_id' => 'Registration is currently closed for this tournament'
                        ]);
                    }

                    // Validate team members
                    $invalidMembers = $team->members()
                        ->whereIn('user_team_members.user_id', $data['selected_members'])
                        ->whereDoesntHave('userGameInfos', fn($q) => $q->where('game_id', $tournament->game_id))
                        ->pluck('name');

                    if ($invalidMembers->isNotEmpty()) {
                        throw ValidationException::withMessages([
                            'selected_members' => "Missing game info for: " . $invalidMembers->join(', ')
                        ]);
                    }

                    // Check for existing registration
                    if ($tournament->registrations()->where('team_id', $team->id)->exists()) {
                        throw ValidationException::withMessages([
                            'team_id' => 'This team is already registered'
                        ]);
                    }

                    // Create registration
                    $registration = TournamentRegistration::create([
                        'tournament_id' => $tournament->id,
                        'team_id' => $team->id,
                        'status' => 'pending',
                    ]);

                    // Attach selected players
                    $registration->players()->attach(
                        collect($data['selected_members'])
                            ->mapWithKeys(fn($userId) => [
                                $userId => ['user_team_id' => $team->id]
                            ])
                    );

                    // Send notifications
                    Notification::make()
                        ->title('Registration Submitted')
                        ->body("Your team {$team->name} has been registered successfully!")
                        ->success()
                        ->send();

                    // Notify organizer and players
                    $tournament->user->notify(new TournamentRegistrationNotification($registration));
                    // - Team owner (new)
                    $team->owner->notify(
                        new TournamentPlayersRegistrationNotification($registration)
                    );

                    // - Selected players (existing, but ensure proper delivery)
                    $registration->players->each(function ($player) use ($registration) {
                        $player->notify(
                            new TournamentPlayersRegistrationNotification($registration)
                        );
                    });
                })
                ->visible(function () {
                    return $this->getRecord()->status === 'published' &&
                        now()->between(
                            $this->getRecord()->registration_start_date,
                            $this->getRecord()->registration_end_date
                        ) &&
                        $this->userHasEligibleTeams();
                })
                ->disabled(function () {
                    return !$this->userHasEligibleTeams() ||
                        $this->userAlreadyRegistered();
                })
                ->tooltip(function () {
                    if (!$this->userHasEligibleTeams()) {
                        return 'Create a team for this game first';
                    }
                    if ($this->userAlreadyRegistered()) {
                        return 'You already registered a team';
                    }
                    return null;
                })
        ];
    }

    protected function userHasEligibleTeams(): bool
    {
        return auth()->user()->ownedTeams()
            ->where('game_id', $this->record->game_id)
            ->exists();
    }

    protected function userAlreadyRegistered(): bool
    {
        return $this->record->registrations()
            ->whereIn('team_id', auth()->user()->ownedTeams()->pluck('id'))
            ->exists();
    }
}
