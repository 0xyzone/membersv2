<?php

namespace App\Filament\Organizers\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\TournamentRegistration;
use Filament\Infolists\Components\Grid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Organizers\Resources\TournamentRegistrationResource\Pages;
use App\Filament\Organizers\Resources\TournamentRegistrationResource\RelationManagers;
use Str;

class TournamentRegistrationResource extends Resource
{
    protected static ?string $model = TournamentRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $activeNavigationIcon = 'heroicon-s-clipboard-document-list';
    protected static ?string $navigationGroup = 'Tournament Management';
    protected static ?int $navigationSort = 2;
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
                Forms\Components\TextInput::make('team_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
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
                                Grid::make(7)
                                    ->schema([
                                        ImageEntry::make('avatar_url')
                                            ->simpleLightbox()
                                            ->label('')
                                            ->defaultImageUrl(asset('images/user_default.png'))
                                            ->circular()
                                            ->width(50)
                                            ->height(50),

                                        TextEntry::make('name')
                                            ->color('primary')
                                            ->weight('medium')
                                            ->default('N/a'),
                                        TextEntry::make('email')
                                            ->color('primary')
                                            ->weight('medium')
                                            ->default('N/a'),
                                        TextEntry::make('primary_contact_number')
                                        ->label('Contact')
                                            ->weight('medium')
                                            ->color('primary')
                                            ->default('N/a'),
                                        TextEntry::make('gender')
                                        ->formatStateUsing(fn($state) => Str::ucfirst($state))
                                            ->weight('medium')
                                            ->color('primary')
                                            ->default('N/a')
                                            ->columnSpan(2),
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
                                                ->modalWidth('3xl') // Adjust the modal width as needed,
                                                ->modalSubmitAction(false)
                                                ->modalCancelAction(false)
                                        ])
                                        ->alignCenter()
                                        ->extraAttributes(['class' => 'flex items-center justify-center h-full']),
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
                // Filter registrations for tournaments organized by the logged-in user
                $query->whereHas('tournament', function ($q) {
                    $q->where('user_id', auth()->id());
                });
            })
            ->columns([
                Tables\Columns\TextColumn::make('tournament.name'),
                Tables\Columns\TextColumn::make('team.name'),
                Tables\Columns\TextColumn::make('status'),
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
