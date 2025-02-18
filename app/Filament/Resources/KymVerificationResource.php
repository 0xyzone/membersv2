<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Infolists\Components\ImageEntry;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KymVerification;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KymVerificationResource\Pages;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;
use App\Filament\Resources\KymVerificationResource\RelationManagers;

class KymVerificationResource extends Resource
{
    protected static ?string $model = KymVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $activeNavigationIcon = 'heroicon-m-check-badge';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->orWhere('status', 'revised')->count();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Personal Information')
                ->columns(2)
                ->schema([
                    TextEntry::make('user.name')->label('Full Name'),
                    TextEntry::make('user.username')->label('Username'),
                    TextEntry::make('user.gender')->label('Gender'),
                    TextEntry::make('user.date_of_birth')->label('Date of Birth')->date(),
                    TextEntry::make('user.email')->label('Email Address')->copyable(),
                    TextEntry::make('user.primary_contact_number')->label('Primary Contact')->copyable(),
                    TextEntry::make('user.secondary_contact_number')->label('Secondary Contact')->copyable()->hidden(fn ($record) => !$record->user->secondary_contact_number),
                ]),

            Section::make('Address Details')
                ->columns(1)
                ->schema([
                    TextEntry::make('user.current_address')->label('Current Address')->markdown(),
                    TextEntry::make('user.permanent_address')->label('Permanent Address')->markdown(),
                ]),

            Section::make('Verification Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('user.verification_document_number')->label('Document Number')->copyable(),
                    TextEntry::make('user.verification_document_issue_date')->label('Issue Date')->date(),
                    TextEntry::make('user.verification_document_expiry_date')->label('Expiry Date')->date()->hidden(fn ($record) => !$record->user->verification_document_expiry_date),
                    ImageEntry::make('user.verification_document_image_path')->label('Document Image')->url(fn ($record) => asset($record->user->verification_document_image_path))->openUrlInNewTab()
                    ->simpleLightbox(),
                ]),

            Section::make('Account Information')
                ->columns(2)
                ->schema([
                    TextEntry::make('user.is_verified')->label('Verification Status')
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Not Verified')
                        ->color(fn ($state) => $state ? 'success' : 'danger'),
                    TextEntry::make('user.is_active')->label('Account Status')
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive')
                        ->color(fn ($state) => $state ? 'success' : 'danger'),
                    TextEntry::make('user.created_at')->label('Joined On')->dateTime(),
                ]),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Hidden::make('updated_by')
                    ->default(auth()->id()),
                Textarea::make('reason')
                    ->autosize()
                    ->characterLimit(100)
                    ->maxLength(100)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('reason')
                ->limit(50),
                Tables\Columns\TextColumn::make('approved_at')
                ->date(),
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
            'index' => Pages\ListKymVerifications::route('/'),
            'create' => Pages\CreateKymVerification::route('/create'),
            'view' => Pages\ViewKymVerification::route('/{record}/'),
            'edit' => Pages\EditKymVerification::route('/{record}/edit'),
        ];
    }
}
