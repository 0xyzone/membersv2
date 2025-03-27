<?php

namespace App\Models;

use App\Models\User;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Model;

class TournamentRegistration extends Model
{
    public function players()
    {
        return $this->belongsToMany(User::class, 'tournament_registration_players')
            ->using(TournamentRegistrationPlayer::class) // Crucial for casting
            ->withPivot(['custom_fields', 'tournament_registration_id', 'user_id'])
            // ->withPivot(['tournament_registration_id', 'user_id'])
            ->withTimestamps();
    }

    public function team()
    {
        return $this->belongsTo(UserTeam::class, 'team_id', 'id');
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
