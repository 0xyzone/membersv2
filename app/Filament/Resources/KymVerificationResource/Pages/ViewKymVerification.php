<?php

namespace App\Filament\Resources\KymVerificationResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\KymVerification;
use Filament\Pages\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\KymVerificationResource;
use Schmeits\FilamentCharacterCounter\Forms\Components\Textarea;

class ViewKymVerification extends ViewRecord
{
    protected static string $resource = KymVerificationResource::class;
    protected function getHeaderActions(): array
    {
        return [
            // Approve Action
            Action::make('approve')
                ->label('Approve')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('Approve Verification')
                ->modalSubheading('Are you sure you want to approve this verification?')
                ->action(function (KymVerification $record) {
                    $this->updateStatus($record, 'approved', null, now());
                    User::where('id', $this->record->user->id)->update(['is_verified' => true]);
                }),

            // Needs Revision Action
            Action::make('needs_revision')
                ->label('Needs Revision')
                ->color('warning')
                ->icon('heroicon-o-pencil')
                ->form([
                    Textarea::make('reason')
                        ->label('Reason for Revision')
                        ->autosize()
                        ->characterLimit(100)
                        ->maxLength(100)
                        ->required(),
                ])
                ->action(fn(array $data, KymVerification $record) => $this->updateStatus($record, 'needs_revision', $data['reason'])),

            // Decline Action
            Action::make('decline')
                ->label('Decline')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->form([
                    Textarea::make('reason')
                        ->label('Reason for Decline')
                        ->autosize()
                        ->characterLimit(100)
                        ->maxLength(100)
                        ->required(),
                ])
                ->action(fn(array $data, KymVerification $record) => $this->updateStatus($record, 'decline', $data['reason'])),
        ];
    }

    protected function updateStatus(KymVerification $record, string $status, string $reason = null, $approvedAt = null): void
    {
        $record->update([
            'status' => $status,
            'reason' => $reason,
            'updated_by' => Auth::id(),
            'approved_at' => $approvedAt,
        ]);

        // Notify Admin (who is changing the status)
        Notification::make()
            ->title('Verification Status Updated')
            ->success()
            ->body("Verification has been marked as **$status**.")
            ->send();

        // Notify the User who applied
        $recipient = $record->user;

        // dd($recipient);
        $recipient->notify(
            Notification::make()
                ->title('Your Verification Status Updated')
                ->body($this->getUserNotificationBody($status, $reason))
                ->info()
                ->toDatabase(),
        );
    }

    private function getUserNotificationBody(string $status, ?string $reason): string
    {
        return match ($status) {
            'approved' => 'Congratulations! Your verification has been approved. You are now a verified user.',
            'needs_revision' => "Your verification request needs some changes. Reason: **$reason**. Please update your details and reapply.",
            'decline' => "Unfortunately, your verification request has been declined. Reason: **$reason**.",
            default => 'Your verification status has been updated.',
        };
    }
}
