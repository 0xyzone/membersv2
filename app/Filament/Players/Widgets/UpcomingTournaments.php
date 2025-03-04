<?php

namespace App\Filament\Players\Widgets;

use App\Models\Tournament;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingTournaments extends TableWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 2;

    protected function getTableQuery(): Builder
    {
        return Tournament::query()
            ->where('status', 'published')
            ->where('start_date', '>=', now())
            ->orderBy('start_date')
            ->with(['game:id,name,image_path']); // Eager load game data
    }

    protected function getTableColumns(): array
    {
        return [
            ImageColumn::make('game.image_path')
                ->label('Game')
                ->width(80)
                ->height(80)
                ->disk('public'),

            TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->description(fn(Tournament $record) => $record->game->name)
                ->weight('bold'),

            TextColumn::make('schedule')
                ->getStateUsing(function (Tournament $record) {
                    return $record->start_date->format('M d, Y') . ' - ' .
                        $record->end_date->format('M d, Y');
                })
                ->icon('heroicon-o-calendar')
                ->color('primary'),

            TextColumn::make('registration_period')
                ->getStateUsing(function (Tournament $record) {
                    return $record->registration_start_date?->format('M d') . ' - ' .
                        $record->registration_end_date?->format('M d, Y');
                })
                ->icon('heroicon-o-clock')
                ->color('secondary'),

            TextColumn::make('prize_pool')
                ->limit(30)
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            \Filament\Tables\Actions\Action::make('view')
                ->url(fn(Tournament $record) => route('filament.players.resources.tournaments.view', $record))
                ->icon('heroicon-o-eye'),
        ];
    }

    protected function getTableEmptyStateIcon(): ?string
    {
        return 'heroicon-o-calendar';
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No upcoming tournaments found';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Check back later for new tournament announcements!';
    }
}