<div class="w-screen min-h-screen flex justify-center items-center bg-gray-900">
<div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
    <h1 class="text-2xl font-bold mb-4 text-gray-800">
        {{ $action === 'accept' ? 'Accept Invitation' : 'Decline Invitation' }}
    </h1>
    <p class="text-gray-600 mb-6">
        Are you sure you want to {{ $action === 'accept' ? 'accept' : 'decline' }} the invitation to join the team
        <span class="font-semibold">{{ $team->name }}</span>?
    </p>
    <form wire:submit.prevent="handleAction" class="flex justify-end space-x-4">
        <a href="{{ route('filament.players.pages.dashboard') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition duration-200">
            Cancel
        </a>
        <button type="submit" class="px-4 py-2 {{ $action === 'accept' ? 'bg-green-500' : 'bg-red-500' }} text-white rounded hover:{{ $action === 'accept' ? 'bg-green-600' : 'bg-red-600' }} transition duration-200">
            {{ $action === 'accept' ? 'Accept' : 'Decline' }}
        </button>
    </form>
</div>
</div>