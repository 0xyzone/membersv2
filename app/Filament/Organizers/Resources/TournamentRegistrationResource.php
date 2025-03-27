<?php

namespace App\Filament\Organizers\Resources;

use Str;
use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Tournament;
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

    public static function canView(Model $record): bool
    {
        return $record->tournament->user_id === auth()->id() ||
            $record->tournament->moderators()
                ->where('user_id', auth()->id())
                ->exists();
    }

    // public static function form(Form $form): Form
    // {
    //     return $form
    //         ->schema([
    //             Forms\Components\TextInput::make('team_id')
    //                 ->required()
    //                 ->numeric(),
    //             Forms\Components\TextInput::make('status')
    //                 ->required(),
    //         ]);
    // }

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
                                Grid::make(8)
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
                                                ->modalWidth('3xl')
                                                ->modalSubmitAction(false)
                                                ->modalCancelAction(false)
                                        ])
                                            ->alignCenter()
                                            ->columnSpan(1)
                                            ->extraAttributes(['class' => 'flex items-center justify-center h-full']),

                                        // Custom Fields Section
                                        Section::make('Custom Fields')
                                            ->heading('Additional Information')
                                            ->schema([
                                                TextEntry::make('pivot.custom_fields')
                                                    ->label('')
                                                    ->formatStateUsing(function (Model $player) {
                                                        $customFields = $player->pivot->custom_fields ?? [];
                                                        // dd($player->pivot->custom_fields);
                                                        $fieldDefinitions = static::getCustomFieldDefinitions($player->pivot->tournament_registration_id);

                                                        return collect($customFields)->map(function ($value, $fieldId) use ($fieldDefinitions) {
                                                            // Convert field ID to integer
                                                            $fieldId = (int) $fieldId;
                                                            $field = $fieldDefinitions->get($fieldId);

                                                            return $field
                                                                ? "<p> <span class='text-amber-500 capitalize'><strong>{$field['name']}:</strong></span> {$value}</p>"
                                                                : "<strong>Unknown Field (#{$fieldId}):</strong> {$value}";
                                                        })->implode('<br>');
                                                    })
                                                    ->html()
                                                    ->columnSpanFull()
                                            ])
                                            ->collapsible()
                                            ->columnSpanFull(),
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

    protected static function getCustomFieldLabels(int $registrationId): array
    {
        $registration = TournamentRegistration::with('tournament.customFields')->find($registrationId);

        return $registration->tournament->customFields
            ->pluck('name', 'id')
            ->toArray();
    }

    protected static function getCustomFieldDefinitions(int $registrationId)
    {
        $registration = TournamentRegistration::with('tournament.customFields')->findOrFail($registrationId);

        return $registration->tournament->customFields->mapWithKeys(fn($field) => [
            $field->id => [
                'name' => $field->name,
                'type' => $field->type,
                'options' => $field->options ? explode(',', $field->options) : null
            ]
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('tournament', function ($q) {
                    $q->where('user_id', auth()->id())
                        ->orWhereHas('moderators', function ($q) {
                            $q->where('user_id', auth()->id());
                        });
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
                Tables\Filters\SelectFilter::make('tournament')
                    ->relationship('tournament', 'name')
                    ->searchable()
                    ->visible(function () {
                        $user = auth()->user();

                        // Get count of tournaments user has access to
                        $tournamentCount = Tournament::where('user_id', $user->id)
                            ->orWhereHas('moderators', fn($q) => $q->where('user_id', $user->id))
                            ->count();

                        return $tournamentCount > 0; // Show filter if any tournaments exist
                    })
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
