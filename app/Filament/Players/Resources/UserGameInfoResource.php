<?php

namespace App\Filament\Players\Resources;

use Filament\Forms;
use App\Models\Game;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\UserGameInfo;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Players\Resources\UserGameInfoResource\Pages;
use App\Filament\Players\Resources\UserGameInfoResource\RelationManagers;

class UserGameInfoResource extends Resource
{
    protected static ?string $model = UserGameInfo::class;
    protected static ?string $modelLabel = "Game Info";
    protected static ?string $slug = "game-info";

    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $activeNavigationIcon = 'heroicon-m-identification';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Game Info')
                    ->description('Fill in the details of your game account')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Hidden::make('user_id')
                                    ->default(auth()->user()->id),

                                Forms\Components\Select::make('game_id')
                                ->hiddenOn('edit')
                                    ->relationship(
                                        name: 'game',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn(Builder $query) => $query->whereNotIn('id', function ($query) {
                                            $query->select('game_id')
                                                ->from('user_game_infos')
                                                ->where('user_id', auth()->id());
                                        })
                                    )
                                    ->live()
                                    ->required()
                                    ->label('Game')
                                    ->columnSpanFull(),

                                    TextInput::make('ingame_id')
                                    ->maxLength(255)
                                    ->label(fn(Get $get) => match (optional(Game::find($get('game_id')))->name) {
                                        'CS2', 'DOTA 2' => 'Steam ID (Steam64)',
                                        'eFootball' => 'PSN ID (If available)',
                                        default => 'In-Game ID',
                                    })
                                    ->placeholder('Enter your in-game ID')
                                    ->unique(
                                        table: 'user_game_infos',
                                        column: 'ingame_id',
                                        modifyRuleUsing: fn($rule, Get $get) => $rule->where('game_id', $get('game_id')), ignoreRecord: true
                                    )
                                    ->hidden(fn(Get $get): bool => !$get('game_id')),

                                Forms\Components\TextInput::make('ingame_name')
                                    ->maxLength(255)
                                    ->label('In-Game Name (if available)')
                                    ->placeholder('Enter your in-game name')
                                    ->hidden(fn(Get $get): bool => !$get('game_id')),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', auth()->user()->id))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('game.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ingame_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ingame_name')
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUserGameInfos::route('/'),
            'create' => Pages\CreateUserGameInfo::route('/create'),
            // 'edit' => Pages\EditUserGameInfo::route('/{record}/edit'),
        ];
    }
}
