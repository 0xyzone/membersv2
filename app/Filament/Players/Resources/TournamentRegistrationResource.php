<?php

namespace App\Filament\Players\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\UserTeam;
use Filament\Forms\Form;
use App\Models\Tournament;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\View;
use Filament\Forms\Components\Hidden;
use App\Models\TournamentRegistration;
use Filament\Infolists\Components\Grid;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Players\Resources\TournamentRegistrationResource\Pages;
use App\Filament\Players\Resources\TournamentRegistrationResource\RelationManagers;

class TournamentRegistrationResource extends Resource
{
    protected static ?string $model = TournamentRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-list';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationGroup = 'Tournament';
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $model): bool
    {
        $userId = auth()->id();

        // Check if the user is the team owner
        return $model->team->user_id === $userId;
    }

    public static function canView(Model $model): bool
    {
        $userId = auth()->id();

        // Check if the user is the team owner
        $isTeamOwner = $model->team->user_id === $userId;

        // Check if the user is a member of the registered team
        $isTeamMember = $model->team->members()
            ->where('user_team_members.user_id', $userId)
            ->exists();

        return $isTeamOwner || $isTeamMember;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('team_id')
                    ->label('Select Team')
                    ->columnSpanFull()
                    ->required()
                    ->disabledOn('edit')
                    ->options(function (TournamentRegistration $record) {
                        $tournament = $record->tournament;
                        return auth()->user()->ownedTeams()
                            ->where('game_id', $tournament->game_id)
                            ->pluck('name', 'id');
                    })
                    ->live()
                    ->afterStateHydrated(function ($component, TournamentRegistration $record) {
                        $record->load('team');
                        $component->state($record->team_id);
                    })
                    ->afterStateUpdated(function ($state, Set $set) {
                        $set('players', []);
                    }),

                \Filament\Forms\Components\Repeater::make('players')
                    ->hint(fn($record) => 'Minimum: ' . $record->tournament->min_team_players . ', Max: ' . $record->tournament->max_team_players . ' players')
                    ->label('Participating Players')
                    ->relationship('players')
                    ->schema([
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label('Player')
                            ->required()
                            ->searchable()
                            ->options(function (Get $get) {
                                $team = UserTeam::find($get('../../team_id'));
                                $currentUserId = $get('user_id');

                                $selectedUserIds = collect($get('../../players'))
                                    ->pluck('user_id')
                                    ->filter(fn($id) => $id !== $currentUserId)
                                    ->toArray();

                                return $team->members()
                                    ->where(function ($query) use ($team) {
                                        $query->where('user_team_members.user_id', $team->owner_id)
                                            ->orWhere('user_team_members.role', 'player');
                                    })
                                    ->whereNotIn('users.id', $selectedUserIds)
                                    ->pluck('users.name', 'users.id')
                                    ->toArray();
                            })
                            ->reactive()
                            ->live(),

                        \Filament\Forms\Components\Group::make()
                            // ->statePath('pivot.custom_fields')
                            ->statePath('pivot.custom_fields')
                            ->schema(function (Get $get) {
                                $tournamentId = $get('../../tournament_id');
                                $tournament = Tournament::with('customFields')->find($tournamentId);

                                // dd($tournament);
                                if (!$tournament)
                                    return [];

                                return $tournament->customFields->map(function ($field) {

                                    // dd($field);
                                    $component = match ($field->type) {
                                        'text' => \Filament\Forms\Components\TextInput::make("{$field->id}")
                                            ->label($field->name)
                                            ->required($field->is_required),

                                        'number' => \Filament\Forms\Components\TextInput::make("{$field->id}")
                                            ->numeric()
                                            ->label($field->name)
                                            ->required($field->is_required),

                                        'dropdown' => \Filament\Forms\Components\Select::make("{$field->id}")
                                            ->label($field->name)
                                            ->options(explode(',', $field->options))
                                            ->required($field->is_required),

                                        default => null,
                                    };
                                    // dd($component);
                                    return $component->columnSpan(1);
                                })->toArray();
                            })
                            ->columns(2)
                            ->visible(
                                fn(Get $get) =>
                                !empty(Tournament::find($get('../../tournament_id'))?->customFields)
                            )
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->minItems(fn(TournamentRegistration $record) => $record->tournament->min_team_players)
                    ->maxItems(fn(TournamentRegistration $record) => $record->tournament->max_team_players)
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                        // dd($data);
                        return [
                            'user_id' => $data['user_id'],
                            'custom_fields' => json_decode($data['pivot']['custom_fields'] ?? '{}', true),
                        ];
                    })
                    ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                        return [
                            'user_id' => $data['user_id'],
                            'pivot' => [
                                'custom_fields' => json_encode($data['custom_fields'] ?? []), // Ensure pivot fields are encoded properly
                            ]
                        ];
                    })
                    ->addActionLabel('Add Player'),

                Hidden::make('tournament_id')
                    ->default(fn($record) => $record?->tournament_id)
                    ->required()
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Tournament Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('tournament.name')
                                    ->label('Tournament Name')
                                    ->weight('bold'),

                                TextEntry::make('tournament.game.name')
                                    ->label('Game')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('tournament.platform')
                                    ->label('Platform')
                                    ->badge(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextEntry::make('tournament.start_date')
                                    ->dateTime('M d, Y'),

                                TextEntry::make('tournament.end_date')
                                    ->dateTime('M d, Y'),
                            ])
                    ])
                    ->collapsible(),

                Section::make('Team Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('team.name')
                                    ->label('Team Name')
                                    ->weight('bold'),

                                TextEntry::make('team.short_name')
                                    ->label('Team Short Name')
                                    ->weight('bold'),

                                TextEntry::make('team.ingame_team_id')
                                    ->label('In-Game ID')
                                    ->hidden(fn($record): bool => $record->team->ingame_team_id == null),

                                TextEntry::make('team.created_at')
                                    ->label('Created at')
                                    ->dateTime('M d, Y'),
                            ])
                    ])
                    ->collapsible(),

                Section::make('Registered Players')
                    ->schema([
                        RepeatableEntry::make('players')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        ImageEntry::make('avatar_url')
                                            ->simpleLightbox()
                                            ->label('')
                                            ->defaultImageUrl(asset('images/user_default.png'))
                                            ->circular()
                                            ->width(50)
                                            ->height(50),

                                        TextEntry::make('name')
                                            ->weight('medium'),
                                        TextEntry::make('date_of_birth')
                                            ->weight('medium')
                                            ->date('jS F, Y'),
                                        Actions::make([
                                            Action::make('view')
                                                ->label('View Details')
                                                ->icon('heroicon-o-information-circle')
                                                ->color('success')
                                                ->modal()
                                                ->modalHeading(fn($record) => "User Details: {$record->name}")
                                                ->modalContent(fn($record, Model $model) => view('filament.modals.user-details', [
                                                    'user' => $record,
                                                    'model' => $model
                                                ]))
                                                ->modalWidth('xl') // Adjust the modal width as needed,
                                                ->modalActions([])
                                                ->modalSubmitAction(false)
                                                ->modalCancelAction(false)
                                        ]),
                                    ])
                            ])
                            ->columns(1)
                    ])
                    ->collapsible(),

                Section::make('Registration Details')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    }),

                                TextEntry::make('created_at')
                                    ->dateTime('M d, Y H:i'),

                                TextEntry::make('updated_at')
                                    ->dateTime('M d, Y H:i'),
                            ])
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                // Get the logged-in user's ID
                $userId = auth()->id();

                // Filter registrations where the logged-in user is either:
                // 1. The team owner, or
                // 2. A member of the team
                $query->whereHas('team', function ($q) use ($userId) {
                    $q->where('user_id', $userId) // Team owner
                        ->orWhereHas('members', function ($q) use ($userId) {
                            $q->where('user_team_members.user_id', $userId); // Team member
                        });
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('tournament.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->extraAttributes([
                        'class' => 'capitalize'
                    ]),
                Tables\Columns\TextColumn::make('notes'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTournamentRegistrations::route('/'),
            'create' => Pages\CreateTournamentRegistration::route('/create'),
            'edit' => Pages\EditTournamentRegistration::route('/{record}/edit'),
            'view' => Pages\ViewTournamentRegistration::route('/{record}'),
        ];
    }
}
