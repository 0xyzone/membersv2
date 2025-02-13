<?php

namespace App\Filament\Players\Widgets;

use Filament\Widgets\Widget;
use App\Models\KymVerification;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class VerificationWidget extends Widget
{
    protected static string $view = 'filament.players.widgets.verification-widget';
    protected static bool $isLazy = false;
    public $user;
    public $verificationStatus;

    public function mount()
    {
        $this->user = Auth::user();
        $this->verificationStatus = KymVerification::where('user_id', $this->user->id)->first();
    }

    public function applyForVerification()
    {
        $user = $this->user;
        $missingFields = [];

        if (!$user->name)
            $missingFields[] = 'Full Name';
        if (!$user->avatar_url)
            $missingFields[] = 'Profile Picture';
        if (!$user->username)
            $missingFields[] = 'Username';
        if (!$user->gender)
            $missingFields[] = 'Gender';
        if (!$user->date_of_birth)
            $missingFields[] = 'Date of Birth';
        if (!$user->verification_document_number)
            $missingFields[] = 'Document Number';
        if (!$user->verification_document_issue_date)
            $missingFields[] = 'Document Issue Date';
        if (!$user->verification_document_image_path)
            $missingFields[] = 'Document Image';
        if (!$user->primary_contact_number)
            $missingFields[] = 'Primary Contact Number';
        if (!$user->current_address)
            $missingFields[] = 'Current Address';
        if (!$user->permanent_address)
            $missingFields[] = 'Permanent Address';

        // Check if any required field is missing
        if (!empty($missingFields)) {
            Notification::make()
                ->title('Incomplete Profile')
                ->danger()
                ->body('Before applying for verification, please complete the following fields:  
                    <ul class="list-disc list-inside text-red-500">' .
                    implode('', array_map(fn($field) => "<li>$field</li>", $missingFields)) .
                    '</ul>')
                ->actions([
                    Action::make('Edit Profile')
                        ->button()
                        ->color('primary')
                        ->url(route('filament.players.pages.my-profile'), shouldOpenInNewTab: true)
                ])
                ->send();

            return;
        }

        // Check if user has already applied or is verified
        if ($this->verificationStatus) {
            Notification::make()
                ->title('Already Applied')
                ->danger()
                ->body('You have already applied for verification or are already verified.')
                ->send();

            return;
        }

        // Create a new verification application
        KymVerification::create([
            'user_id' => $user->id,
            'updated_by' => Auth::id(),
            'status' => 'pending',
        ]);

        // Refresh the verification status
        $this->verificationStatus = KymVerification::where('user_id', $this->user->id)->first();

        // Notify user about the successful submission
        Notification::make()
            ->title('Verification Request Submitted')
            ->success()
            ->body('Your verification request has been submitted and is under review.')
            ->send();
    }

    public function reapplyForVerification()
    {
        $user = Auth::user();

        // Fetch the current verification record
        $verification = KymVerification::where('user_id', $user->id)
            ->where('status', 'needs_revision')
            ->first();

        // If no record found or the status is not needs_revision, abort
        if (!$verification) {
            Notification::make()
                ->title('Error')
                ->danger()
                ->body('You can only reapply for verification if your application is in "Needs Revision" status.')
                ->send();

            return;
        }

        // Update the verification status to "revised"
        $verification->update([
            'status' => 'revised',  // or 'needs_revision' if you want it in that state
            'updated_by' => Auth::id(),  // Set the current logged-in user as the one who updated it
            'approved_at' => now(),  // Optionally, set the approval time if applicable
        ]);

        // Notify the user about the reapplication
        Notification::make()
            ->title('Verification Reapplication')
            ->success()
            ->body('Your verification status has been updated to revised and is under review.')
            ->sendToDatabase($user);

        // Optionally, you could add more logic or event triggers here
    }
}
