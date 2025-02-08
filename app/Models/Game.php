<?php

namespace App\Models;

use App\Models\UserTeam;
use App\Models\UserGameInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Game extends Model
{
    /**
     * Get all of the userGameInfos for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userGameInfos(): HasMany
    {
        return $this->hasMany(UserGameInfo::class);
    }

    /**
     * Get all of the teams for the Game
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function teams(): HasMany
    {
        return $this->hasMany(UserTeam::class);
    }
}
