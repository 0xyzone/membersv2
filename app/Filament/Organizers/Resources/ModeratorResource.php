<?php

namespace App\Filament\Organizers\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Moderator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Organizers\Resources\ModeratorResource\Pages;
use App\Filament\Organizers\Resources\ModeratorResource\RelationManagers;

class ModeratorResource extends Resource
{
    protected static ?string $model = Moderator::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $activeNavigationIcon = 'heroicon-s-user-group';

    public static function canEdit(Model $model): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(auth()->user()->id),
                Forms\Components\Select::make('moderator_id')
                    ->label('Organizer to Add')
                    ->searchable()
                    ->required()
                    ->relationship(
                        name: 'moderator',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->role('organizers')
                    )
                    ->getSearchResultsUsing(
                        fn(string $search): array => User::query()
                            ->where(function ($query) use ($search) {
                                $query->where('email', $search)       // Exact email match
                                    ->orWhere('id', $search)          // Exact ID match
                                    ->orWhere('user_id', $search);    // Exact user_id match
                            })
                            ->whereNotIn('id', [auth()->id()]) // Exclude current user
                            ->role('organizers')
                            ->limit(50)
                            ->get()
                            ->mapWithKeys(fn(User $user) => [
                                $user->id => "({$user->id}) {$user->name} | {$user->user_id}"
                            ])
                            ->toArray()
                    )
                    ->rules([
                        Rule::unique('moderators', 'moderator_id')
                            ->where('user_id', auth()->id()) // Unique per current organizer
                    ])
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('moderator.name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('moderator.email')
                    ->label('Email'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                Tables\Actions\DeleteAction::make()
                    ->label('Kick')
                    ->icon('heroicon-o-no-symbol')
                    ->after(function ($record) {
                        $moderator = $record->moderator;
                        Notification::make()
                            ->title('You\'ve been kicked as a moderator')
                            ->body(auth()->user()->name . ' has kicked you as a moderator')
                            ->sendToDatabase($moderator);
                    }),
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
            'index' => Pages\ListModerators::route('/'),
            'create' => Pages\CreateModerator::route('/create'),
            'edit' => Pages\EditModerator::route('/{record}/edit'),
        ];
    }
}
