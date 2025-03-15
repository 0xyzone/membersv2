<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DemoController;
use App\Livewire\HandleInvitationAction;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\PublicProfileController;

Route::get('/', function () {
    $pageTitle = "Vidanta Portal - Champions Arena";
    $pageDescription = "Join Vidanta Champions Arena, the ultimate esports platform for gamers and tournament organizers. Compete in epic tournaments, manage teams, and build your legacy.";
    $user = auth()->user();
    if($user) {
        if($user->hasRole(['players'])) {
            return redirect(route('filament.players.pages.dashboard'));
        }
        if($user->hasRole(['super_admin'])) {
            return redirect(route('filament.admin.pages.dashboard'));
        }
        if($user->hasRole(['organizers'])) {
            return redirect(route('filament.organizers.pages.dashboard'));
        }
    } else {
        return view('welcome', compact('pageTitle', 'pageDescription'));
    }
})->name('home');

Route::get('/invitation/{team}/{action}', HandleInvitationAction::class)
    ->name('invitation.action');

Route::post('/verify', [VerificationController::class, 'applyForVerification'])->name('verification.apply');
Route::get('/check419', function () {
    return view('errors.419');
});

Route::get('demonstration', [DemoController::class, 'index']);

Route::get('/{user}', PublicProfileController::class)
    ->name('public.profile')
    ->where('user', '[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}');