<?php

namespace App\Models;

use App\Models\User;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Model;

class TournamentRegistration extends Model
{
    public function players()
    {
        return $this->belongsToMany(User::class, 'tournament_registration_players',
        'registration_id', // Correct foreign key for the registration
        'user_id')
        ->with('userGameInfos')
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
