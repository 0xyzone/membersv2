<?php

namespace App\Models;

use App\Models\Tournament;
use Illuminate\Database\Eloquent\Model;

class TournamentCustomField extends Model
{
    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }
}
