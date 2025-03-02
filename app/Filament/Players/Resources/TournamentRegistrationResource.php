<?php

namespace App\Filament\Players\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\UserTeam;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\TournamentRegistration;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
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
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('team_id')
                        ->label('Select Team')
                        ->required()
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
                        ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                            $set('selected_members', []);
                        }),

                    \Filament\Forms\Components\CheckboxList::make('selected_members')
                        ->label('Select Participating Players')
                        ->options(function (\Filament\Forms\Get $get) {
                            $team = UserTeam::find($get('team_id'));
                            return $team?->members()
                                ->wherePivot('role', 'player') // Only allow players (not substitutes)
                                ->pluck('name', 'user_team_members.user_id')
                                ->toArray();
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tournament.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                ->badge()
                ->extraAttributes([
                    'class' => 'capitalize'
                ]),
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
        ];
    }
}
