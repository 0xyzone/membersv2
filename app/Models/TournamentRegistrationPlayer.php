<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TournamentRegistrationPlayer extends Pivot
{
    protected $table = 'tournament_registration_players';
    protected $casts = [
        'custom_fields' => 'array', // Add this line
    ];

    // public function getCustomFieldsAttribute($value)
    // {
    //     return json_decode($value, true) ?? [];
    // }
}
