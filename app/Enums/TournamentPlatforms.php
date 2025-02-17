<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TournamentPlatforms: string implements HasLabel
{
    case PC = 'pc';
    case PS5 = 'ps5';
    case XBOX_SERIES_X = 'xbox-series-x';
    case NINTENDO_SWITCH = 'nintendo-switch';
    case MOBILE = 'mobile';
    case CLOUD = 'cloud';
    case VR = 'vr';
    case AR = 'ar';
    case EMULATOR = 'emulator';
    case SIMULATOR = 'simulator';
    case ARCADE = 'arcade';
    case PS4 = 'ps4';
    case XBOX_ONE = 'xbox-one';
    case OCULUS = 'oculus';
    case STEAM_DECK = 'steam-deck';
    case GEFORCE_NOW = 'geforce-now';
    case STADIA = 'stadia';
    case RETRO = 'retro';
    case CROSS_PLATFORM = 'cross-platform';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::PC => 'PC',
            self::PS5 => 'PlayStation 5',
            self::XBOX_SERIES_X => 'Xbox Series X/S',
            self::NINTENDO_SWITCH => 'Nintendo Switch',
            self::MOBILE => 'Mobile',
            self::CLOUD => 'Cloud Gaming',
            self::VR => 'Virtual Reality',
            self::AR => 'Augmented Reality',
            self::EMULATOR => 'Emulator',
            self::SIMULATOR => 'Simulator',
            self::ARCADE => 'Arcade Machine',
            self::PS4 => 'PlayStation 4',
            self::XBOX_ONE => 'Xbox One',
            self::OCULUS => 'Oculus',
            self::STEAM_DECK => 'Steam Deck',
            self::GEFORCE_NOW => 'NVIDIA GeForce NOW',
            self::STADIA => 'Google Stadia',
            self::RETRO => 'Retro Console',
            self::CROSS_PLATFORM => 'Cross-Platform',
            self::OTHER => 'Other Platform',
        };
    }

    public static function filamentOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
