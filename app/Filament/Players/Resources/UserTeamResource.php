<?php

namespace App\Filament\Players\Resources;

use Closure;
use Filament\Forms;
use App\Models\Game;
use App\Models\User;
use Filament\Tables;
use App\Models\UserTeam;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Notifications\TeamInvitationNotification;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Players\Resources\UserTeamResource\Pages;
use Filament\Notifications\Notification as FilamentNotification;
use App\Filament\Players\Resources\UserTeamResource\RelationManagers;

class UserTeamResource extends Resource
{
    protected static ?string $model = UserTeam::class;
    protected static ?string $modelLabel = "Your Teams";
    protected static ?string $slug = "teams";

    protected static ?string $navigationGroup = "Team Management";

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    public static function canCreate(): bool
    {
        $user = auth()->user();

        // Check if there's any game the user ISN'T a member of
        return Game::whereNotIn('id', function ($query) use ($user) {
            $query->select('game_id')
                ->from('user_teams')
                ->join('user_team_members', 'user_teams.id', '=', 'user_team_members.user_team_id')
                ->where('user_team_members.user_id', $user->id);
        })->exists();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Team Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('id')->label('Team ID'),
                                TextEntry::make('name')->label('Team Name'),
                                TextEntry::make('short_name')->label('Short Name'),
                                TextEntry::make('ingame_team_id')->label('In-Game Team ID'),
                                TextEntry::make('game.name')->label('Game'),
                                TextEntry::make('owner.name')->label('Owner'),
                            ]),
                        ImageEntry::make('team_logo_image_path')->label('Team Logo'),
                    ]),

                Section::make('Team Members')
                    ->schema([
                        RepeatableEntry::make('members')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('pivot.role')
                                    ->label('Role'),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Hidden user_id field remains at the top
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),

                // Main team information section
                Forms\Components\Section::make('Team Information')
                    ->schema([
                        Forms\Components\Grid::make()
                            ->schema([
                                Forms\Components\Select::make('game_id')
                                    ->relationship(
                                        name: 'game',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->whereNotIn(
                                            'id',
                                            auth()->user()->ownedTeams()->pluck('game_id')
                                                ->merge(auth()->user()->teams()->pluck('game_id'))
                                                ->unique()
                                        )
                                    )
                                    ->hiddenOn('edit')
                                    ->required()
                                    ->rules([
                                        function () {
                                            return function (string $attribute, $value, Closure $fail) {
                                                if (auth()->user()->teams()->where('game_id', $value)->exists()) {
                                                    $fail("You're already a member of a team in this game!");
                                                }
                                            };
                                        },
                                    ]),

                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->placeholder('Enter team full name'),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('short_name')
                                            ->maxLength(255)
                                            ->placeholder('Team abbreviation')
                                            ->helperText('Short version of team name (e.g., NYF)'),

                                        Forms\Components\TextInput::make('ingame_team_id')
                                            ->maxLength(255)
                                            ->placeholder('In-game identifier')
                                            ->helperText('Team ID from the game system'),
                                    ]),
                            ])
                    ])
                    ->columns(1),

                // Team logo section
                Forms\Components\Section::make('Team Branding')
                    ->schema([
                        Forms\Components\FileUpload::make('team_logo_image_path')
                            ->image()
                            ->directory('team-logos') // Specify upload directory
                            ->avatar() // Makes it display as circular
                            ->alignCenter()
                            ->imageEditor()
                            ->helperText('Recommended size: 1080x1080 pixels, PNG format')
                            ->columnSpanFull()
                            ->downloadable()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1'),
                    ])
                    ->columns(1)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('user_id', auth()->id()))
            ->columns([
                Tables\Columns\ImageColumn::make('team_logo_image_path')
                    ->label(''),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('owner.name'),
                Tables\Columns\TextColumn::make('game.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('short_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ingame_team_id')
                    ->searchable(),
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
                Tables\Actions\Action::make('invite')
                    ->form([
                        Select::make('recipient_id')
                            ->label('User to invite')
                            ->options(User::where('id', '!=', auth()->id())->where('id', '!=', 1)->pluck('name', 'id')) // List of users to invite
                            ->searchable()
                            ->required(),
                            Select::make('role')
                            ->options([
                                'player' => 'Player',
                                'substitute' => 'Substitute',
                            ])
                            ->default('player')
                    ])
                    ->action(function (array $data, UserTeam $team) {
                        $recipient = User::findOrFail($data['recipient_id']);
                        $sender = auth()->user();

                        // Validation: Prevent duplicate invitations
                        $existingInvitation = $team->invitations()
                            ->where('recipient_id', $recipient->id)
                            ->whereIn('status', ['pending', 'accepted'])
                            ->exists();

                        if ($existingInvitation) {
                            FilamentNotification::make()
                                ->title('Invitation already exists!')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Validation: Prevent inviting existing team members
                        if ($team->members()->where('user_team_members.user_id', $recipient->id)->exists()) {
                            FilamentNotification::make()
                                ->title('User is already a team!')
                                ->danger()
                                ->send();
                            return;
                        }

                        // Send the invitation
                        $team->invitations()->create([
                            'sender_id' => $sender->id,
                            'recipient_id' => $recipient->id,
                            'role' => $data['role'],
                            'status' => 'pending',
                        ]);

                        // Send the notification
                        $recipient->notify(new TeamInvitationNotification($team, $sender, $data['role']));

                        FilamentNotification::make()
                            ->title('Invitation sent successfully!')
                            ->success()
                            ->send();
                    })
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
            'index' => Pages\ListUserTeams::route('/'),
            'create' => Pages\CreateUserTeam::route('/create'),
            'edit' => Pages\EditUserTeam::route('/{record}/edit'),
        ];
    }
}
