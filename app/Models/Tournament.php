<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserTeam;
use App\Models\TournamentRegistration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tournament extends Model
{
    /**
     * Get the user that owns the Tournament
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the game that owns the Tournament
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    public function registrations()
    {
        return $this->hasMany(TournamentRegistration::class);
    }


    public function getPlayerRequirementsAttribute()
    {
        return "{$this->min_team_players}-{$this->max_team_players} players";
    }

    public function teams()
    {
        return $this->belongsToMany(
            UserTeam::class,
            'tournament_registrations',
            'tournament_id',
            'team_id'
        )->where('status', "approved");
    }

    protected $withCount = ['teams', 'registrations'];
    protected $casts = [
        'meta_tags' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'registration_start_date' => 'date',
        'registration_end_date' => 'date',
    ];

    // public function moderators()
    // {
    //     return $this->hasMany(TournamentModerator::class);
    // }

    public function moderators()
    {
        return $this->hasMany(TournamentModerator::class);
    }
}
