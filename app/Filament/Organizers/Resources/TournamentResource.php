<?php

namespace App\Filament\Organizers\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Tournament;
use Filament\Tables\Table;
use App\Enums\TournamentTypes;
use Filament\Resources\Resource;
use App\Enums\TournamentPlatforms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Organizers\Resources\TournamentResource\Pages;
use App\Filament\Organizers\Resources\TournamentResource\RelationManagers;

class TournamentResource extends Resource
{
    protected static ?string $model = Tournament::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $activeNavigationIcon = 'heroicon-s-trophy';
    protected static ?string $navigationGroup = 'Tournament Management';

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
                                        Forms\Components\FileUpload::make('logo_image_path')
                                            ->required()
                                            ->label('Tournament Logo')
                                            ->helperText('Image should be atleast 1080 x 1080 pixels big and it should be in ratio of 1:1')
                                            ->image()
                                            ->directory('tournaments/logos')
                                            ->imageEditor()
                                            ->panelAspectRatio('1:1')
                                            ->panelLayout('integrated')
                                            ->removeUploadedFileButtonPosition('center')
                                            ->uploadButtonPosition('center'),

                                        Forms\Components\FileUpload::make('cover_image_path')
                                            ->required()
                                            ->label('Cover Image')
                                            ->panelLayout('integrated')
                                            ->removeUploadedFileButtonPosition('center')
                                            ->uploadButtonPosition('center')
                                            ->image()
                                            ->directory('tournaments/covers')
                                            ->imageEditor()
                                            ->imageResizeMode('cover')
                                            ->imageCropAspectRatio('16:9')
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
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('user_id', auth()->user()->id))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('game.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo_image_path'),
                Tables\Columns\ImageColumn::make('cover_image_path'),
                Tables\Columns\TextColumn::make('platforms')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('discord_invite_link')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('registration_end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_teams')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('min_team_players')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('max_team_players')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organizer_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('organizer_contact_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organizer_alt_contact_number')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('organizer_contact_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean(),
                Tables\Columns\TextColumn::make('stream_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('official_hashtag')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('twitter_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('facebook_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('min_player_age')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_by')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListTournaments::route('/'),
            'create' => Pages\CreateTournament::route('/create'),
            'edit' => Pages\EditTournament::route('/{record}/edit'),
        ];
    }
}
