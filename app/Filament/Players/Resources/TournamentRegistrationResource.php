<?php

namespace App\Filament\Players\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\UserTeam;
use Filament\Forms\Form;
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
                    // ->hidden(fn(TournamentRegistration $record) => !$record->userHasEligibleTeams())
                    ->live()
                    ->afterStateHydrated(function ($component, TournamentRegistration $record) {
                        // Force refresh team relation
                        $record->load('team');
                        $component->state($record->team_id);
                    })
                    ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                        $set('players', []);
                    }),

                // \Filament\Forms\Components\TextInput::make('user_team_id')
                //     ->default(fn(Get $get) => $get('team_id')),

                \Filament\Forms\Components\CheckboxList::make('players')
                    ->hint(fn($record) => 'Minimum: ' . $record->tournament->min_team_players . ', Max: ' . $record->tournament->max_team_players . ' players to be selected!')
                    ->label('Participating Players')
                    ->relationship('players', 'id')
                    ->options(function (TournamentRegistration $record) {
                        $team = $record->team;

                        $owner = [
                            (string) $team->user_id => $team->owner->name . ' (Owner)'
                        ];

                        $players = $team->members()
                            ->wherePivot('role', 'player')
                            ->get()
                            ->mapWithKeys(fn($user) => [
                                (string) $user->id => $user->name
                            ])
                            ->toArray();

                        return $owner + $players;
                    })
                    ->columns(3)
                    ->columnSpanFull()
                    ->required()
                    ->pivotData(function (Get $get, TournamentRegistration $record) {
                        return [
                            'user_team_id' => $record->team_id ?? $get('team_id'),
                        ];
                    })
                    ->rule(function (TournamentRegistration $record) {
                        return [
                            'array',
                            'min:' . $record->tournament->fresh()->min_team_players,
                            'max:' . $record->tournament->fresh()->max_team_players,
                        ];
                    })
                    ->validationMessages([
                        'min' => 'Minimum :min players required',
                        'max' => 'Maximum :max players allowed',
                    ])
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
