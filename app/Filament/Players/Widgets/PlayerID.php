<?php

namespace App\Filament\Players\Widgets;

use Filament\Widgets\Widget;

class PlayerID extends Widget
{
    protected static string $view = 'filament.players.widgets.player-id';
    protected static bool $isLazy = false;
    public $user;

    public function mount() {
        $this->user = auth()->user();
    }
}
