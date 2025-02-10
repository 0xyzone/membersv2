<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserTeam;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function accept(UserTeam $team)
    {
        $user = Auth::user();

        // Add the user to the team
        $team->members()->attach($user->id, ['role' => 'member']);

        // Mark the notification as read
        $user->notifications()->where('data->team_id', $team->id)->delete();

        return redirect()->route('filament.players.pages.dashboard')->with('success', 'You have joined the team!');
    }

    public function decline(UserTeam $team)
    {
        $user = Auth::user();

        // Mark the notification as read
        $user->notifications()->where('data->team_id', $team->id)->delete();

        return redirect()->route('filament.players.pages.dashboard')->with('success', 'You have declined the invitation.');
    }
}
