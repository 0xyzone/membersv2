<?php

namespace App\Models;

use App\Models\Game;
use App\Models\User;
use App\Models\Tournament;
use App\Models\TournamentRegistration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTeam extends Model
{
    /**
     * Get the user that owns the UserTeam
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the game that owns the UserTeam
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'user_team_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    // New relationship for invitations
    public function invitations()
    {
        return $this->hasMany(Invitation::class);
    }

    public function tournamentRegistrations()
    {
        return $this->hasMany(TournamentRegistration::class);
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_registrations');
    }

    public function scopeForGame($query, $gameId)
    {
        return $query->where('game_id', $gameId);
    }
}
