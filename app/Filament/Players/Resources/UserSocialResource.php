<?php

namespace App\Filament\Players\Resources;

use App\Filament\Players\Resources\UserSocialResource\Pages;
use App\Filament\Players\Resources\UserSocialResource\RelationManagers;
use App\Models\UserSocial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Str;

class UserSocialResource extends Resource
{
    protected static ?string $model = UserSocial::class;

    protected static ?string $navigationIcon = 'tabler-social';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->required()
                    ->default(auth()->user()->id),
                Forms\Components\Select::make('type')
                    ->label('Platform')
                    ->required()
                    ->options([
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'x' => 'X (Twitter)',
                        'discord' => 'Discord',
                        'twitch' => 'Twitch'
                    ]),
                Forms\Components\TextInput::make('username')
                    ->maxLength(255),
                Forms\Components\TextInput::make('link')
                    ->url()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_public')
                    ->label('Make Public')
                    ->inline(false)
                    ->helperText('Toggle on if you want to make it visible to public')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('')
                    ->state(
                        static function (Tables\Contracts\HasTable $livewire, \stdClass $rowLoop): string {
                            return (string) (
                                $rowLoop->iteration +
                                ($livewire->getTableRecordsPerPage() * (
                                    $livewire->getTablePage() - 1
                                ))
                            );
                        }
                    ),
                Tables\Columns\TextColumn::make('type')
                    ->label('Platform')
                    ->badge()
                    ->formatStateUsing(fn($state) => Str::ucfirst($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('link')
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_public')
                    ->label('Is Public?')
                    ->afterStateUpdated(
                        fn() =>
                        Notification::make()
                            ->title('Saved successfully')
                            ->success()
                            ->send()
                    ),
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
            'index' => Pages\ListUserSocials::route('/'),
            'create' => Pages\CreateUserSocial::route('/create'),
            'edit' => Pages\EditUserSocial::route('/{record}/edit'),
        ];
    }
}
