<?php

namespace App\Models;

use App\Models\User;
use App\Models\Tournament;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentModerator extends Pivot
{
    protected $table = 'tournament_moderators';
    
    protected $fillable = [
        'tournament_id',
        'user_id',
        'role'
    ];

    /**
     * Get the tournament that owns the TournamentModerator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    /**
     * Get the user that owns the TournamentModerator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'role' => 'string',
    ];
}