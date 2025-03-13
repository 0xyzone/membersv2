<?php

namespace App\Filament\Organizers\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Tournament;
use Filament\Tables\Table;
use App\Enums\TournamentTypes;
use Filament\Resources\Resource;
use App\Enums\TournamentPlatforms;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Components\CustomFileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Organizers\Resources\TournamentResource\Pages;
use App\Filament\Organizers\Resources\TournamentResource\RelationManagers;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $activeNavigationIcon = 'heroicon-s-trophy';
    protected static ?string $navigationGroup = 'Tournament Management';

    public static function canEdit(Model $model): bool
    {
        return $model->user_id === auth()->id() ||
            $model->moderators()
                ->where('user_id', auth()->id())
                ->where('role', 'admin')
                ->exists();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('TournamentFormTabs')
                    ->tabs([
                        // General Information Tab
                        Forms\Components\Tabs\Tab::make('General')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Section::make('Basic Information')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\Hidden::make('user_id')
                                            ->default(auth()->id()),

                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),

                                        Forms\Components\Select::make('game_id')
                                            ->relationship('game', 'name')
                                            ->disabledOn('edit')
                                            ->required()
                                            ->searchable()
                                            ->preload(),

                                        Forms\Components\Select::make('type')
                                            ->options(TournamentTypes::filamentOptions())
                                            ->searchable()
                                            ->required(),

                                        Forms\Components\Select::make('platform')
                                            ->options(TournamentPlatforms::filamentOptions())
                                            ->disabledOn('edit')
                                            ->searchable()
                                            ->native(false),
                                    ])
                                    ->columns(3),

                                Forms\Components\Section::make('Media')
                                    ->columns(2)
                                    ->schema([
                                        FileUpload::make('logo_image_path')
                                            ->required()
                                            ->label('Tournament Logo')
                                            ->helperText('Image should be atleast 1080 x 1080 pixels big and it should be in ratio of 1:1')
                                            ->directory('tournaments/logos')
                                            ->panelAspectRatio('1:1')
                                            ->panelLayout('integrated')
                                            ->removeUploadedFileButtonPosition('center')
                                            ->uploadButtonPosition('center'),

                                        FileUpload::make('cover_image_path')
                                            ->label('Cover Image')
                                            ->panelLayout('integrated')
                                            ->uploadButtonPosition('center')
                                            ->directory('tournaments/covers')
                                            ->columnSpan(2)
                                            ->previewable()
                                            ->helperText('Image should be atleast 1920 x 1080 pixels big and it should be in ratio of 16:9')
                                            ->panelAspectRatio('16:9'),
                                    ])
                                    ->columns(3),
                            ]),

                        // Schedule & Structure Tab
                        Forms\Components\Tabs\Tab::make('Schedule & Structure')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Section::make('Dates & Timing')
                                    ->schema([
                                        Forms\Components\DatePicker::make('registration_start_date')
                                            ->required()
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->live(),

                                        Forms\Components\DatePicker::make('registration_end_date')
                                            ->required()
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->minDate(fn(Get $get) => $get('registration_start_date'))
                                            ->live()
                                            ->hidden(fn(Get $get) => !$get('registration_start_date')),

                                        Forms\Components\DatePicker::make('start_date')
                                            ->required()
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->minDate(fn(Get $get) => $get('registration_end_date'))
                                            ->live()
                                            ->hidden(fn(Get $get) => !$get('registration_end_date')),

                                        Forms\Components\DatePicker::make('end_date')
                                            ->native(false)
                                            ->closeOnDateSelection()
                                            ->required()
                                            ->minDate(fn(Get $get) => $get('start_date'))
                                            ->hidden(fn(Get $get) => !$get('start_date')),
                                    ]),

                                Forms\Components\Section::make('Team Configuration')
                                    ->schema([
                                        Forms\Components\TextInput::make('max_teams')
                                            ->numeric()
                                            ->required()
                                            ->minValue(2),

                                        Forms\Components\TextInput::make('min_team_players')
                                            ->numeric()
                                            ->required()
                                            ->minValue(1),

                                        Forms\Components\TextInput::make('max_team_players')
                                            ->numeric()
                                            ->required()
                                            ->gte('min_team_players'),

                                        Forms\Components\TextInput::make('min_player_age')
                                            ->numeric()
                                            ->required(),
                                    ]),
                            ]),

                        // Organizer & Content Tab
                        Forms\Components\Tabs\Tab::make('Details')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Section::make('Organizer Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('organizer_name')
                                            ->maxLength(255)
                                            ->required()
                                            ->columnSpanFull(),

                                        Forms\Components\TextInput::make('organizer_contact_email')
                                            ->email()
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('organizer_contact_number')
                                            ->tel()
                                            ->required()
                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),

                                        Forms\Components\TextInput::make('organizer_alt_contact_number')
                                            ->tel()
                                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                                    ]),

                                Forms\Components\Section::make('Content')
                                    ->schema([
                                        Forms\Components\RichEditor::make('description')
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'blockquote',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('rules')
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('prize_pool')
                                            ->columnSpanFull(),

                                        Forms\Components\RichEditor::make('road_map')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        // Settings & Visibility Tab
                        Forms\Components\Tabs\Tab::make('Settings')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Section::make('Visibility')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_public')
                                            ->inline(),

                                        Forms\Components\TextInput::make('entry_fee')
                                            ->helperText('0 = free entry')
                                            ->numeric()
                                            ->prefix('Rs. ')
                                            ->default(0),
                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'draft' => 'Draft',
                                                'published' => 'Published',
                                                'archived' => 'Archived',
                                            ])
                                            ->default('draft')
                                            ->required(),
                                    ]),

                                Forms\Components\Section::make('SEO')
                                    ->schema([
                                        Forms\Components\TagsInput::make('meta_tags'),

                                        Forms\Components\TextInput::make('meta_title')
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('meta_description')
                                            ->maxLength(255)
                                            ->rows(3),
                                    ]),

                                Forms\Components\Section::make('Social Links')
                                    ->schema([
                                        Forms\Components\TextInput::make('discord_invite_link')
                                            ->url()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('stream_url')
                                            ->label('Streaming Channel Url')
                                            ->url()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('website_url')
                                            ->url()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('twitter_url')
                                            ->url()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('facebook_url')
                                            ->url()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('official_hashtag')
                                            ->maxLength(255),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Moderators')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Repeater::make('moderators')
                                    ->relationship('moderators')
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->label('Moderator')
                                            ->required()
                                            ->searchable()
                                            ->options(function () {
                                                return User::whereHas('moderatorsAdded', fn($q) => $q->where('user_id', auth()->id()))
                                                    ->pluck('name', 'id');
                                            })
                                            ->getSearchResultsUsing(
                                                fn(string $search) =>
                                                User::whereHas('moderatorsAdded', fn($q) => $q->where('user_id', auth()->id()))
                                                    ->where(fn($q) => $q->where('name', 'like', "%$search%")
                                                        ->orWhere('email', 'like', "%$search%")
                                                        ->orWhere('id', $search))
                                                    ->limit(50)
                                                    ->get()
                                                    ->pluck('name', 'id')
                                            ),

                                        Forms\Components\Select::make('role') // Remove pivot. prefix
                                            ->options([
                                                'admin' => 'Admin',
                                                'moderator' => 'Moderator',
                                            ])
                                            ->default('moderator')
                                            ->required()
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->itemLabel(fn(array $state): ?string =>
                                        User::find($state['user_id'])?->name . ' - ' . ($state['role'] ?? 'moderator'))
                            ])

                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn(Builder $query) => $query
                    ->where('user_id', auth()->id())
                    ->orWhereHas(
                        'moderators',
                        fn($q) =>
                        $q->where('user_id', auth()->id())
                    )
            )
            ->columns([
                // Logo Column
                Tables\Columns\ImageColumn::make('logo_image_path')
                    ->label('')
                    ->circular()
                    ->size(50)
                    ->defaultImageUrl(asset('images/tournament_logo_default.png')),

                // Name and Game
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Tournament $record) => $record->game->name)
                    ->weight('medium')
                    ->wrap(),

                // Owner
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Owner')
                    ->description(fn(Tournament $record) => '@' . $record->user->username)
                    ->searchable(),

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
                    ->formatStateUsing(fn(Tournament $record): string =>
                        $record->teams_count . '/' . $record->max_teams)
                    ->color(fn(Tournament $record) =>
                        $record->teams_count >= $record->max_teams ? 'danger' : 'success')
                    ->sortable(),

                // Registrations
                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Registrations')
                    ->numeric()
                    ->formatStateUsing(fn(Tournament $record): string =>
                        $record->registrations_count)
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
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListTournaments::route('/'),
            'create' => Pages\CreateTournament::route('/create'),
            'edit' => Pages\EditTournament::route('/{record}/edit'),
            'view' => Pages\ViewTournament::route('/{record}'),
        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewTournament::class,
            Pages\EditTournament::class,
        ]);
    }
}
