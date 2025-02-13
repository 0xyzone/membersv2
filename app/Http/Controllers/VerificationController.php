<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KymVerification;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class VerificationController extends Controller
{
    public function applyForVerification(Request $request)
    {
        $user = Auth::user();

        // Check if the user has filled all the required fields
        if (!$user->name || !$user->avatar_url || !$user->verification_document_number || !$user->verification_document_image_path) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('Please complete your profile before applying for verification.')
                ->send();

            return back(); // Redirect back to the form
        }

        // Check if the user has already applied or is verified
        $existingVerification = KymVerification::where('user_id', $user->id)->first();
        if ($existingVerification) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('You have already applied for verification or are already verified.')
                ->send();

            return back(); // Redirect back to the form
        }

        // Create a new verification application
        KymVerification::create([
            'user_id' => $user->id,
            'updated_by' => Auth::id(),
            'status' => 'pending',
        ]);

        Notification::make()
            ->title('Success')
            ->success()
            ->body('Your verification request has been submitted and is under review.')
            ->send();

        return back();
    }
}
