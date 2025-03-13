<?php

namespace App\Models;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentModerator extends Model
{
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
