<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\TextSize;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Support\Enums\IconPosition;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\UserResource\Pages;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Infolists\Components\Grid as InfoGrid;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Split as IngoSplit;
use App\Filament\Resources\UserResource\RelationManagers;
use Filament\Infolists\Components\TextEntry\TextEntrySize;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $activeNavigationIcon = 'heroicon-m-users';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                IngoSplit::make([
                    ImageEntry::make('avatar_url')
                        ->label('')
                        ->simpleLightbox()
                        ->size(150)
                        ->circular()
                        ->grow(false)
                        ->defaultImageUrl(asset('images/user_default.png')),

                    InfoGrid::make(3)->schema([
                        TextEntry::make('name')
                            ->size(TextEntrySize::Large)
                            ->weight(FontWeight::Bold),

                        TextEntry::make('user_id')
                            ->label('UUID')
                            ->color('gray')
                            ->columnSpan(2)
                            ->weight(FontWeight::Bold)
                            ->copyable()
                            ->copyMessage('Copied!')
                            ->copyMessageDuration('3000'),

                        TextEntry::make('username')
                            ->icon('heroicon-m-at-symbol')
                            ->color('gray'),

                        TextEntry::make('email')
                            ->icon('heroicon-m-envelope')
                            ->copyable()
                            ->copyMessage('Copied!')
                            ->copyMessageDuration('3000')
                            ->color('gray'),

                        TextEntry::make('date_of_birth')
                            ->icon('heroicon-m-cake')
                            ->date(),
                    ]),
                ])->columnSpanFull(),

                Section::make('Personal Information')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('id')
                            ->label('User Id')
                            ->copyable()
                            ->copyMessage('Copied!')
                            ->copyMessageDuration('3000'),
                        TextEntry::make('gender'),
                        TextEntry::make('created_at')
                            ->dateTime('M d, Y h:i A'),
                        TextEntry::make('email_verified_at')
                            ->dateTime('M d, Y h:i A')
                            ->label('Email Verified'),
                    ]),

                Section::make('Social Media Accounts')
                    ->collapsible()
                    ->collapsed()
                    ->icon('heroicon-o-share')
                    ->schema([
                        RepeatableEntry::make('socials')
                            ->label('')
                            ->schema([
                                TextEntry::make('type')
                                    ->label('Platform')
                                    ->icon(fn($state) => self::getSocialIcon($state))
                                    ->badge()
                                    ->color(fn($state) => self::getSocialColor($state)),

                                TextEntry::make('username')
                                    ->copyable()
                                    ->suffixAction(
                                        Action::make('visit-social')
                                            ->icon('heroicon-o-arrow-top-right-on-square')
                                            ->color(fn($record) => self::getSocialColor($record->type))
                                            ->url(fn($record) => $record->link)
                                            ->openUrlInNewTab()
                                            ->iconSize('sm')
                                        // ->tooltip(fn($record) => 'Visit ' . ucfirst($record->type))
                                    )
                            ])
                            ->columns(2)
                            ->grid(2)
                    ]),

                Section::make('Verification Documents')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('verification_document_number'),
                        TextEntry::make('verification_document_issue_date')
                            ->date(),
                        TextEntry::make('verification_document_expiry_date')
                            ->date(),
                        ImageEntry::make('verification_document_image_path')
                            ->label('Document Image')
                            ->simpleLightbox()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }

    private static function getSocialIcon(string $platform): string
    {
        return match (strtolower($platform)) {
            'facebook' => 'bi-facebook',
            'x' => 'bi-x',
            'insta' => 'bi-instagram',
            'twitch' => 'bi-twitch',
            'discord' => 'bi-discord',
            default => 'bi-link',
        };
    }

    private static function getSocialColor(string $platform): string
    {
        return match (strtolower($platform)) {
            'facebook' => '#1877F2',  // Facebook blue
            'x' => '#000000',  // X/Twitter black
            'insta' => '#E1306C',  // Instagram pink
            'twitch' => '#9146FF',  // Twitch purple
            'discord' => '#5865F2',  // Discord blue
            default => 'gray',
        };
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('user_id')
                            ->label('User ID')
                            ->disabled()
                            ->maxLength(36),
                        Forms\Components\TextInput::make('name')
                            ->label('Full Name')
                            ->disabled(),
                        Forms\Components\TextInput::make('username')
                            ->label('Username')
                            ->disabled(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->disabled(),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->label('Date of Birth')
                            ->disabled(),
                        Forms\Components\TextInput::make('gender')
                            ->label('Gender')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Verification Details')
                    ->schema([
                        Forms\Components\TextInput::make('verification_document_number')
                            ->label('Document Number')
                            ->disabled(),
                        Forms\Components\DatePicker::make('verification_document_issue_date')
                            ->label('Issue Date')
                            ->disabled(),
                        Forms\Components\DatePicker::make('verification_document_expiry_date')
                            ->label('Expiry Date')
                            ->disabled(),
                        Forms\Components\FileUpload::make('verification_document_image_path')
                            ->label('Document Image')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Account Settings')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('User Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Verified User')
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active Status')
                            ->required(),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At'),
                    ]),

                Forms\Components\Section::make('Profile Picture')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar_url')
                            ->label('Profile Picture')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Stack::make([
                    Split::make([
                        ImageColumn::make('avatar_url')
                            ->label('')
                            ->circular()
                            ->size(45)
                            ->grow(false)
                            ->defaultImageUrl(asset('images/user_default.png')),
                        TextColumn::make('name')
                            ->weight('bold')
                            ->searchable()
                            ->icon(fn($record) => $record->is_verified ? 'heroicon-s-check-badge' : 'heroicon-s-check-badge')
                            ->iconColor(fn($record) => $record->is_verified ? 'info' : 'gray')
                            ->iconPosition(IconPosition::After)
                            ->description(fn($record) => '@' . $record->username),
                        IconColumn::make('is_active')
                            ->grow(false)
                            ->label('Status')
                            ->icon(fn($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                            ->color(fn($state) => $state ? 'success' : 'danger')
                            ->size('lg'),
                    ]),
                    Split::make([
                        TextColumn::make('roles.name')
                            ->grow(false)
                            ->icon('heroicon-o-shield-check')
                            ->badge()
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'super_admin' => 'Super Admin',
                                'players' => 'Player',
                                'organizers' => 'Organizer',
                                default => Str::headline($state), // Fallback for other roles
                            })
                            ->color(fn($state) => match ($state) {
                                'super_admin' => 'danger',
                                'players' => 'primary',
                                'organizers' => 'info',
                                default => 'gray'
                            }),
                        TextColumn::make('created_at')
                            // ->icon('heroicon-o-clock')
                            ->prefix('Joined: ')
                            // ->dateTime('M d, Y')
                            ->since()
                            ->size('xs')
                            ->color('gray')
                            ->alignEnd(),
                    ]),
                ])
                    ->space(3),
                Panel::make([
                    Stack::make([
                        TextColumn::make('email')
                            ->icon('heroicon-o-envelope')
                            ->iconColor('primary')
                            ->copyable()
                            ->size('sm')
                            ->color('medium_gray')
                            ->suffix(fn($record) => $record->hasVerifiedEmail() ? ' ✅' : ''),
                        TextColumn::make('current_address')
                            ->icon('heroicon-o-map-pin')
                            ->iconColor('primary')
                            ->copyable()
                            ->size('sm')
                            ->color('medium_gray'),
                        TextColumn::make('permanent_address')
                            ->icon('heroicon-s-map')
                            ->iconColor('primary')
                            ->copyable()
                            ->size('sm')
                            ->color('medium_gray'),
                        TextColumn::make('primary_contact_number')
                            ->icon('heroicon-o-megaphone')
                            ->iconColor('primary')
                            ->copyable()
                            ->size('sm')
                            ->color('medium_gray'),
                        TextColumn::make('secondary_contact_number')
                            ->icon('heroicon-o-phone-arrow-up-right')
                            ->iconColor('primary')
                            ->copyable()
                            ->size('sm')
                            ->color('medium_gray'),
                    ])->space(1),
                ])
                    ->collapsible()
                    ->extraAttributes(['class' => 'mt-3']),

                // Mobile view
                Grid::make()
                    ->schema([
                        TextColumn::make('user_id')
                            ->label('ID')
                            ->copyable()
                            ->size('xs'),

                        IconColumn::make('is_verified')
                            ->boolean()
                            ->label('Verified'),
                    ])
                    ->columns(2)
                    ->visibleOn('md')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('is_active')
                    ->label('Active Users')
                    ->toggle()
                    ->query(fn($query) => $query->where('is_active', true)),

                Tables\Filters\Filter::make('verified_users')
                    ->label('Verified Users')
                    ->toggle()
                    ->query(fn($query) => $query->where('is_verified', true)),
            ])
            ->actions([
                ViewAction::make('view'),
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('primary'),

                DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
                ActionGroup::make([
                    TableAction::make('make_organier')
                        ->icon('heroicon-o-trophy')
                        ->action(function ($record) {
                            $record->assignRole('organizers');
                            $record->removeRole('players');
                            $record->removeRole('super_admin');
                            Notification::make()
                                ->title('User updated to organizer.')
                                ->send()
                                ->success();
                        })
                        ->requiresConfirmation()
                        ->visible(fn($record) => !$record->hasRole('organizers')),
                    TableAction::make('make_player')
                        ->icon('heroicon-o-user')
                        ->action(function ($record) {
                            $record->assignRole('players');
                            $record->removeRole('organizers');
                            $record->removeRole('super_admin');
                            Notification::make()
                                ->title('User updated to player.')
                                ->send()
                                ->success();
                        })
                        ->requiresConfirmation()
                        ->visible(fn($record) => !$record->hasRole('players')),
                    TableAction::make('make_admin')
                        ->icon('heroicon-o-shield-exclamation')
                        ->action(function ($record) {
                            $record->assignRole('super_admin');
                            $record->removeRole('players');
                            $record->removeRole('organizers');
                            Notification::make()
                                ->title('User updated to admin.')
                                ->send()
                                ->success();
                        })
                        ->requiresConfirmation()
                        ->visible(fn($record) => !$record->hasRole('super_admin'))
                ])
            ])
            ->recordAction('view')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([9, 12, 36]);
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            // 'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
