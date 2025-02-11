<?php

namespace App\Filament\Players\Resources;

use Filament\Forms;
use App\Models\Game;
use App\Models\User;
use Filament\Tables;
use App\Models\UserTeam;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
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
use Filament\Infolists\Components\Table as InfoTable;
use App\Filament\Players\Resources\TeamsYouAreInResource\Pages;
use Filament\Notifications\Notification as FilamentNotification;
use App\Filament\Players\Resources\UserTeamResource\RelationManagers;
use Filament\Infolists\Components\Group;

class TeamsYouAreInResource extends Resource
{
    protected static ?string $model = UserTeam::class;
    protected static ?string $modelLabel = "Associated Teams";
    protected static ?string $slug = "teamsurin";
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationParentItem = 'Your Teams';
    protected static ?string $navigationGroup = "Team Management";
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    public static function canCreate(): bool
    {
        $gameCount = Game::all()->count();
        $user = auth()->user();
        if ($user->teams->count() === $gameCount) {
            return false;
        }
        return true;
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
                        Grid::make(['md' => 1])
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

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('members', function ($q) {
                    $q->where('users.id', auth()->id());
                });
            })
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
        ];
    }
}
