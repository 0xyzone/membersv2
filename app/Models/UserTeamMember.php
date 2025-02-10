<?php

namespace App\Models;

use App\Models\User;
use App\Models\UserTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTeamMember extends Model
{
    /**
     * Get the user that owns the UserTeamMember
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the userTeam that owns the UserTeamMember
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userTeam(): BelongsTo
    {
        return $this->belongsTo(UserTeam::class);
    }
}
