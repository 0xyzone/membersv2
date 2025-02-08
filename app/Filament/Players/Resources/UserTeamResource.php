<?php

namespace App\Filament\Players\Resources;

use App\Filament\Players\Resources\UserTeamResource\Pages;
use App\Filament\Players\Resources\UserTeamResource\RelationManagers;
use App\Models\Game;
use App\Models\UserTeam;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserTeamResource extends Resource
{
    protected static ?string $model = UserTeam::class;
    protected static ?string $modelLabel = "Teams";
    protected static ?string $slug = "teams";

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->id()),
                Forms\Components\Select::make('game_id')
                    ->relationship('game', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('short_name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('ingame_team_id')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('team_logo_image_path')
                    ->image(),
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
            'index' => Pages\ListUserTeams::route('/'),
            'create' => Pages\CreateUserTeam::route('/create'),
            'edit' => Pages\EditUserTeam::route('/{record}/edit'),
        ];
    }
}
