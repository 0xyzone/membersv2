<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Moderator extends Model
{
    /**
     * Get the user that owns the Moderator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the moderator that owns the Moderator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id', 'id');
    }
}
