<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;
use App\Livewire\HandleInvitationAction;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\VerificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/invitation/{team}/{action}', HandleInvitationAction::class)
    ->name('invitation.action');

Route::post('/verify', [VerificationController::class, 'applyForVerification'])->name('verification.apply');
Route::get('/check419', function () {
    return view('errors.419');
});

Route::get('demonstration', [DemoController::class, 'index']);