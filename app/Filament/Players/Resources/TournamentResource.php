<?php

namespace App\Filament\Players\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Tournament;
use Filament\Tables\Table;
use App\Enums\TournamentTypes;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Enums\TournamentPlatforms;
use Filament\Infolists\Components\Grid;
use Illuminate\Database\Eloquent\Model;
use Filament\Infolists\Components\Group;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Players\Resources\TournamentResource\Pages;
use App\Filament\Players\Resources\TournamentResource\RelationManagers;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $activeNavigationIcon = 'heroicon-s-trophy';
    protected static ?string $navigationGroup = 'Tournament';
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $model): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ImageEntry::make('cover_image_path')
                    ->label('')
                    ->height(300)
                    ->columnSpanFull()
                    ->extraAttributes([
                        'class' => '!max-w-7xl !block mx-auto !lg:-mb-[200px]'
                    ])
                    ->extraImgAttributes([
                        'class' => '!w-full !h-auto rounded-lg',
                    ]),
                ImageEntry::make('logo_image_path')
                    ->label('')
                    ->height(150)
                    ->width(150)
                    ->defaultImageUrl(asset('images/tournament_default.png'))
                    ->extraAttributes([
                        'class' => 'lg:-mt-[350px] -mt-[120px] max-w-max aspect-square z-10 !h-max shadow-2xl'
                    ])
                    ->extraImgAttributes([
                        'class' => '!w-28 lg:!w-40 aspect-square !h-auto bg-gray-800 p-4 rounded-lg border-4 border-gray-200'
                    ]),
                Section::make('Tournament Overview')
                    ->extraAttributes([
                        'class' => 'lg:-mt-[210px] -mt-[16px]'
                    ])
                    ->schema([
                        Grid::make(['md' => 3, 'default' => 2])
                            ->schema([

                                Grid::make(2)
                                    ->schema([

                                        TextEntry::make('name')
                                            ->size('lg')
                                            ->weight('bold'),

                                        TextEntry::make('game.name')
                                            ->badge()
                                            ->color('primary'),
                                        TextEntry::make('platform')
                                            ->badge()
                                            ->color('gray'),

                                        TextEntry::make('type')
                                            ->badge()
                                            ->color('gray'),

                                        TextEntry::make('status')
                                            ->badge()
                                            ->color(fn(string $state): string => match ($state) {
                                                'draft' => 'gray',
                                                'published' => 'success',
                                                'archived' => 'danger',
                                            }),

                                        TextEntry::make('entry_fee')
                                            ->formatStateUsing(fn($state) => $state ? '₹' . number_format($state / 100, 2) : 'Free')
                                            ->badge()
                                            ->color(fn($state) => $state ? 'warning' : 'success'),
                                    ])
                            ])
                    ])
                    ->collapsible(),

                Section::make('Schedule')
                    ->schema([
                        Grid::make(['md' => 2])
                            ->schema([
                                TextEntry::make('start_date')
                                    ->dateTime('M d, Y')
                                    ->label('Start Date'),

                                TextEntry::make('end_date')
                                    ->dateTime('M d, Y')
                                    ->label('End Date'),

                                TextEntry::make('registration_start_date')
                                    ->dateTime('M d, Y')
                                    ->label('Registration Opens'),

                                TextEntry::make('registration_end_date')
                                    ->dateTime('M d, Y')
                                    ->label('Registration Closes'),
                            ])
                    ])
                    ->collapsible(),

                Section::make('Team Requirements')
                    ->schema([
                        Grid::make(['md' => 3])
                            ->schema([
                                TextEntry::make('max_teams')
                                    ->label('Max Teams')
                                    ->default('No limit'),

                                TextEntry::make('min_team_players')
                                    ->label('Min Players/Team'),

                                TextEntry::make('max_team_players')
                                    ->label('Max Players/Team'),

                                TextEntry::make('min_player_age')
                                    ->label('Min Player Age')
                                    ->default('No age restriction'),
                            ])
                    ])
                    ->collapsible(),

                Section::make('Organizer Information')
                    ->schema([
                        Grid::make(['md' => 3])
                            ->schema([
                                TextEntry::make('organizer_name'),

                                TextEntry::make('organizer_contact_email')
                                    ->icon('heroicon-o-envelope'),

                                TextEntry::make('organizer_contact_number')
                                    ->icon('heroicon-o-phone'),
                            ])
                    ])
                    ->collapsible(),

                Section::make('Rules & Content')
                    ->schema([
                        TextEntry::make('description')
                            ->markdown()
                            ->columnSpanFull()
                            ->default('N/a')
                            ->extraAttributes([
                                'class' => 'bg-black/20 px-6 py-2 rounded-lg'
                            ]),

                        TextEntry::make('rules')
                            ->markdown()
                            ->default('N/a')
                            ->extraAttributes([
                                'class' => 'bg-black/20 px-6 py-2 rounded-lg'
                            ]),

                        TextEntry::make('prize_pool')
                            ->markdown()
                            ->default('N/a')
                            ->extraAttributes([
                                'class' => 'bg-black/20 px-6 py-2 rounded-lg'
                            ]),

                        TextEntry::make('road_map')
                            ->markdown()
                            ->columnSpanFull()
                            ->default('N/a')
                            ->extraAttributes([
                                'class' => 'bg-black/20 px-6 py-2 rounded-lg'
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Social Links')
                    ->schema([
                        Grid::make(['md' => 3])
                            ->schema([
                                TextEntry::make('discord_invite_link')
                                    ->icon('heroicon-o-chat-bubble-left')
                                    ->url(fn($state) => $state == 'No invite link aaded!' ? '#' : $state)
                                    ->default('No invite link aaded!'),

                                TextEntry::make('website_url')
                                    ->icon('heroicon-o-globe-alt')
                                    ->url(fn($state) => $state == '-' ? '#' : $state)
                                    ->default('-'),

                                TextEntry::make('stream_url')
                                    ->icon('heroicon-o-video-camera')
                                    ->url(fn($state) => $state == '-' ? '#' : $state)
                                    ->default('-'),
                            ])
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'published'))
            ->columns([
                // Logo Column
                Tables\Columns\ImageColumn::make('logo_image_path')
                    ->label('')
                    ->circular()
                    ->size(50),

                // Name and Game
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Tournament $record) => $record->game->name)
                    ->weight('medium')
                    ->wrap(),

                // Schedule
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Tournament Dates')
                    ->dateTime('M d, Y')
                    ->description(fn(Tournament $record) =>
                        'End: ' . $record->end_date->format('M d, Y'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('registration_start_date')
                    ->label('Registration Dates')
                    ->dateTime('M d, Y')
                    ->description(fn(Tournament $record) =>
                        'End: ' . $record->registration_end_date->format('M d, Y'))
                    ->sortable(),

                // Teams and Players
                Tables\Columns\TextColumn::make('teams_count')
                    ->label('Teams')
                    ->numeric()
                    ->formatStateUsing(fn(Tournament $record) =>
                        $record->teams_count . '/' . $record->max_teams)
                    ->color(fn(Tournament $record) =>
                        $record->teams_count >= $record->max_teams ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('player_requirements')
                    ->label('Players')
                    ->formatStateUsing(fn(Tournament $record) =>
                        $record->min_team_players . '-' .
                        $record->max_team_players . ' players')
                    ->sortable(),

                // Organizer
                Tables\Columns\TextColumn::make('organizer_name')
                    ->label('Organizer')
                    ->description(fn(Tournament $record) =>
                        $record->organizer_contact_email)
                    ->searchable(),

                // Platform and Type
                Tables\Columns\TextColumn::make('platform')
                    ->badge()
                    ->color('gray')
                    ->extraAttributes([
                        'class' => 'capitalize'
                    ]),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-trophy'),

                Tables\Columns\TextColumn::make('entry_fee')
                    ->formatStateUsing(fn($state) => $state == 0 ? 'Free' : 'Rs. ' . $state)
            ])
            ->filters([

                Tables\Filters\Filter::make('registration_open')
                    ->label('Registration Open')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('registration_start_date', '<=', now())
                        ->where('registration_end_date', '>=', now())),

                Tables\Filters\SelectFilter::make('platform')
                    ->options(TournamentPlatforms::filamentOptions()),

                Tables\Filters\SelectFilter::make('type')
                    ->options(TournamentTypes::filamentOptions()),
            ])
            ->actions([
                // Tables\Actions\Action::make('view')
                //     ->url(fn(Tournament $record): string => route('filament.players.resources.tournaments.view', $record))
                //     ->icon('heroicon-o-eye'),

                // Tables\Actions\Action::make('register')
                //     ->label('Register Team')
                //     ->icon('heroicon-o-user-plus')
                //     ->form([
                //         // Your registration form components
                //     ])
                //     ->action(function (Tournament $record, array $data) {
                //         // Registration logic
                //     })
                //     ->visible(fn(Tournament $record): bool =>
                //         $record->status === 'upcoming' &&
                //         $record->registration_end_date->isFuture()),
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Bulk actions if needed
                ]),
            ])
            ->groups([
                // Tables\Grouping\Group::make('status')
                //     ->label('Status')
                //     ->collapsible(),

                // Tables\Grouping\Group::make('game.name')
                //     ->label('Game')
                //     ->collapsible(),
            ])
            ->deferLoading()
            ->paginated([10, 25, 50]);
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
            'index' => Pages\ListTournaments::route('/'),
            'view' => Pages\ViewTournament::route('/{record}'),
        ];
    }
}
