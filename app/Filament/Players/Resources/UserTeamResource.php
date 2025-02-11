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
use Illuminate\Support\Str;
use App\Models\UserTeamMember;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Notifications\TeamKickedNotification;
use Filament\Infolists\Components\ImageEntry;
use App\Notifications\TeamInvitationNotification;
use Filament\Infolists\Components\Actions\Action;
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
                        Grid::make(['md' => 2])
                            ->schema([
                                Group::make([
                                    ImageEntry::make('team_logo_image_path')
                                        ->label('')
                                        ->height(150)
                                        ->width(150)
                                        ->defaultImageUrl(asset('images/team_default.png'))
                                        ->extraImgAttributes(['class' => 'rounded-lg shadow-md']),

                                    TextEntry::make('short_name')
                                        ->label('Team Short name')
                                        ->size('lg')
                                        ->weight('bold'),
                                ])->columnSpan(['md' => 1]),

                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Full Team Name')
                                            ->size('lg')
                                            ->weight('medium'),

                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('id')->label('Team ID'),
                                                TextEntry::make('ingame_team_id')->label('Game Team ID'),
                                                TextEntry::make('game.name')->label('Game')
                                                    ->badge()
                                                    ->color('primary'),
                                                TextEntry::make('created_at')
                                                    ->label('Created')
                                                    ->dateTime('M d, Y'),
                                            ]),
                                    ])->columnSpan(['md' => 1]),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Team Leadership')
                    ->schema([
                        Grid::make(['md' => 2])
                            ->schema([
                                ImageEntry::make('owner.avatar_url')
                                    ->label('Owner Avatar')
                                    ->defaultImageUrl(asset('images/user_default.png'))
                                    ->height(80)
                                    ->width(80)
                                    ->extraImgAttributes(['class' => 'rounded-full']),

                                Group::make([
                                    TextEntry::make('owner.name')
                                        ->label('Owner Name')
                                        ->size('lg'),

                                    TextEntry::make('owner.email')
                                        ->label('Contact Email')
                                        ->icon('heroicon-o-envelope'),
                                ]),
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Team Members')
                    ->schema([
                        Grid::make(['md' => 2])
                            ->schema([
                                RepeatableEntry::make('members')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                TextEntry::make('name')
                                                    ->formatStateUsing(function ($state, $record) {
                                                        $avatarUrl = $record->avatar_url ?? asset('images/user_default.png');
                                                        return <<<HTML
                                                        <div class="flex items-center gap-3">
                                                            <img src="$avatarUrl" 
                                                                class="h-8 w-8 rounded-full object-cover" 
                                                                alt="User avatar"
                                                            >
                                                            <span class="font-medium">$state</span>
                                                        </div>
                                                    HTML;
                                                    })
                                                    ->html()
                                                    ->columnSpan(1),

                                                TextEntry::make('pivot.role')
                                                    ->label('Role')
                                                    ->formatStateUsing(fn(string $state): string => match ($state) {
                                                        'player' => 'Player',
                                                        'substitue' => 'Substitute',
                                                        default => Str::headline($state),
                                                    })
                                                    ->badge()
                                                    ->color(fn(string $state): string => match ($state) {
                                                        'player' => 'success',
                                                        'substitue' => 'warning',
                                                        default => 'gray',
                                                    })
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->columns(1),
                            ])->columns(1),
                    ])
                    ->collapsible(),
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
                                            ->maxLength(5)
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
                            ->required()
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
                    ->columns(1),
                Forms\Components\Section::make('Team Members')
                    ->hidden(fn($record) => $record->members->count() == 0)
                    // ->hiddenOn('create')
                    ->schema([
                        Forms\Components\Repeater::make('members')
                            ->relationship('members')
                            ->schema([
                                Forms\Components\Select::make('user_id')
                                    ->disabled()
                                    ->label('Member')
                                    ->options(User::query()->pluck('name', 'user_id'))
                                    ->searchable()
                                    ->required(),

                                Forms\Components\Select::make('role')
                                    ->disabled()
                                    ->options([
                                        'player' => 'Player',
                                        'substitute' => 'Substitute',
                                    ])
                                    ->default('player')
                                    ->required(),
                            ])
                            ->defaultItems(0)
                            ->addable(false)
                            ->deletable(false)
                            ->columns(3) // Changed to 3 columns to accommodate the action
                            ->columnSpanFull()
                            ->extraItemActions([
                                Forms\Components\Actions\Action::make('kick')
                                    ->label('')
                                    ->tooltip('Remove member immediately')
                                    ->color('danger')
                                    ->icon('heroicon-o-user-minus') // Changed to user-minus icon
                                    ->requiresConfirmation()
                                    ->modalHeading('Remove Team Member')
                                    ->modalSubheading('This action will remove the member immediately!')
                                    ->action(function (array $arguments, Forms\Components\Repeater $component) {
                                        $state = $component->getState();
                                        $index = $arguments['item'];
                                        $userId = $state[$index]['id'];
                                        // dd($userId);
                                        $team = $component->getLivewire()->record;

                                        // Use Eloquent relationship
                                        $team->members()->detach($userId);

                                        // Remove from form state
                                        unset($state[$index]);
                                        $component->state(array_values($state));

                                        // Send notifications
                                        $user = User::find($userId);
                                        if ($user) {
                                            $user->notify(new TeamKickedNotification($team, auth()->user()));
                                            FilamentNotification::make()
                                                ->title('Member Removed')
                                                ->body("{$user->name} was removed immediately")
                                                ->success()
                                                ->send();
                                        }
                                    })
                                    ->visible(function (array $arguments, Forms\Components\Repeater $component) {
                                        $state = $component->getState();
                                        $index = $arguments['item'];
                                        return isset($state[$index]) &&
                                            $state[$index]['user_id'] !== auth()->id();
                                    })
                            ])
                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                return $data;
                            })
                            ->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
                                return $data;
                            })
                    ])
                    ->collapsible(),
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
