<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicProfileController extends Controller
{
    public function __invoke(User $user)
    {
        // Add privacy checks if needed
        if (!$user->is_active || !$user->is_verified) {
            abort(404);
        }

        return view('public-profile', [
            'user' => $user->load([
                'socials',
                'userGameInfos' => fn($query) => $query->with('game'),
                'ownedTeams' => fn($query) => $query->withCount('members')->with('game'),
                'teams' => fn($query) => $query->withCount('members')->with('game')
            ])
        ]);
    }
}
