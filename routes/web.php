<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\HandleInvitationAction;
use App\Http\Controllers\InvitationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invitation/{team}/{action}', HandleInvitationAction::class)
    ->name('invitation.action');