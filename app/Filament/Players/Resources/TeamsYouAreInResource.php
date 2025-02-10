<?php

namespace App\Filament\Players\Resources;

use Filament\Forms;
use App\Models\Game;
use App\Models\User;
use Filament\Tables;
use App\Models\UserTeam;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Infolists\Components\Table as InfoTable;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Notifications\TeamInvitationNotification;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Players\Resources\TeamsYouAreInResource\Pages;
use Filament\Notifications\Notification as FilamentNotification;
use App\Filament\Players\Resources\UserTeamResource\RelationManagers;

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
