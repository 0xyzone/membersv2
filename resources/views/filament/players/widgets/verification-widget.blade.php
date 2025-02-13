<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex md:flex-row flex-col gap-4 justify-between">
            @if (!$verificationStatus)
            <div class="md:w-1/2">If you have completed your profile then you can</div>
            <form wire:submit.prevent="applyForVerification">
                <button type="submit" class="px-6 py-2 bg-primary-500 text-white font-semibold rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-400 transition duration-200 flex-1 flex-shrink-0">
                    Apply for Verification
                </button>
            </form>
            @elseif ($verificationStatus->status === 'approved')
            <p class="text-green-500 py-2">Your account has been verified!</p>
            @elseif($verificationStatus->status === 'needs_revision')
            <div class="md:w-1/2 flex flex-col">
                <p class="font-bold text-primary-500">Application needs revision.</p>
                <span class="text-xs">Reason: {{ $verificationStatus->reason }}</span>
            </div>
            <form wire:submit.prevent="reapplyForVerification">
                <button type="submit" class="px-6 py-2 bg-primary-500 text-white font-semibold rounded-lg hover:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-primary-400 transition duration-200 flex-1 flex-shrink-0">
                    Reapply for Verification
                </button>
            </form>
            @else
            <p class="text-yellow-500 py-2">Your verification is under review.</p>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>