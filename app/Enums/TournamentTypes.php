<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TournamentTypes: string implements HasLabel
{
    case ONLINE = 'online';
    case OFFLINE = 'offline';
    case HYBRID = 'hybrid';
    case LAN = 'lan';
    case MAJOR = 'major';
    case MINOR = 'minor';
    case INVITATIONAL = 'invitational';
    case OPEN = 'open';
    case QUALIFIER = 'qualifier';
    case SHOWMATCH = 'showmatch';
    case CHARITY = 'charity';
    case COMMUNITY = 'community';
    case LEAGUE = 'league';
    case TOURNAMENT_SERIES = 'series';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::ONLINE => 'Online Tournament',
            self::OFFLINE => 'Offline/LAN Event',
            self::HYBRID => 'Hybrid (Online + Offline)',
            self::LAN => 'LAN Party Event',
            self::MAJOR => 'Major Championship',
            self::MINOR => 'Minor Tournament',
            self::INVITATIONAL => 'Invitational Event',
            self::OPEN => 'Open Registration',
            self::QUALIFIER => 'Qualifier Tournament',
            self::SHOWMATCH => 'Exhibition Showmatch',
            self::CHARITY => 'Charity Tournament',
            self::COMMUNITY => 'Community Event',
            self::LEAGUE => 'League Competition',
            self::TOURNAMENT_SERIES => 'Tournament Series',
            self::OTHER => 'Other Tournament Type',
        };
    }

    public static function filamentOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}