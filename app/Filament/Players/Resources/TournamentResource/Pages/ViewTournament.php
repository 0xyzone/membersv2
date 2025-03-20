<?php

namespace App\Filament\Players\Resources\TournamentResource\Pages;

use App\Models\User;
use App\Models\UserTeam;
use Filament\Actions\Action;
use App\Models\TournamentRegistration;
use Filament\Forms\Components\Repeater;
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
                            $set('players', []);
                        }),

                    Repeater::make('players')
                        ->label('Players Information')
                        ->defaultItems(fn() => $this->record->min_team_players)
                        ->schema([
                            \Filament\Forms\Components\Select::make('user_id')
                                ->label('Player')
                                ->required()
                                ->searchable()
                                ->options(function (\Filament\Forms\Get $get) {
                                    $team = UserTeam::find($get('../../team_id'));
                                    $currentUserId = $get('user_id');

                                    // Get all selected user IDs except current one
                                    $selectedUserIds = collect($get('../../players'))
                                        ->pluck('user_id')
                                        ->filter(fn($id) => $id !== $currentUserId)
                                        ->toArray();

                                    return $team->members()
                                        ->where(function ($query) use ($team) {
                                            $query->where('user_team_members.user_id', $team->owner_id)
                                                ->orWhere('user_team_members.role', 'player');
                                        })
                                        ->whereNotIn('user_team_members.user_id', $selectedUserIds)
                                        ->pluck('name', 'user_team_members.user_id')
                                        ->toArray();
                                })
                                ->reactive()
                                ->live(),

                            \Filament\Forms\Components\Group::make()
                                ->statePath('custom_fields')
                                ->schema(fn() => $this->getCustomFieldsComponents())
                                ->columns(2)
                        ])
                        ->minItems(fn() => $this->record->min_team_players)
                        ->maxItems(fn() => $this->record->max_team_players)
                        ->hidden(fn(\Filament\Forms\Get $get) => !$get('team_id'))
                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                            return $data;
                        })
                        ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                            return $data;
                        })
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
                    $selectedUserIds = collect($data['players'])->pluck('user_id');
                    $invalidMembers = $team->members()
                        ->whereIn('user_team_members.user_id', $selectedUserIds)
                        ->whereDoesntHave('userGameInfos', fn($q) => $q->where('game_id', $tournament->game_id))
                        ->pluck('name');

                    if ($invalidMembers->isNotEmpty()) {
                        throw ValidationException::withMessages([
                            'players' => "Missing game info for: " . $invalidMembers->join(', ')
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

                    // Attach players with custom fields
                    $registration->players()->attach(
                        collect($data['players'])->mapWithKeys(function ($player) use ($team) {
                        return [
                            $player['user_id'] => [
                                'user_team_id' => $team->id,
                                'custom_fields' => $player['custom_fields'] ?? []
                            ]
                        ];
                    })
                    );

                    // Send notifications
                    Notification::make()
                        ->title('Registration Submitted')
                        ->body("Your team {$team->name} has been registered successfully!")
                        ->success()
                        ->send();

                    // Notify organizer and players
                    $tournament->user->notify(new TournamentRegistrationNotification($registration));
                    $team->owner->notify(new TournamentPlayersRegistrationNotification($registration));

                    $registration->players->each(function ($player) use ($registration) {
                        $player->notify(new TournamentPlayersRegistrationNotification($registration));
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

    protected function getCustomFieldsComponents(): array
    {
        return $this->record->customFields->map(function ($field) {
            $component = match ($field->type) {
                'text' => \Filament\Forms\Components\TextInput::make($field->id)
                    ->label($field->name)
                    ->required($field->is_required),

                'number' => \Filament\Forms\Components\TextInput::make($field->id)
                    ->numeric()
                    ->label($field->name)
                    ->required($field->is_required),

                'dropdown' => \Filament\Forms\Components\Select::make($field->id)
                    ->label($field->name)
                    ->options(explode(',', $field->options))
                    ->required($field->is_required),

                default => null,
            };

            return $component->columnSpan(1);
        })->filter()->toArray();
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