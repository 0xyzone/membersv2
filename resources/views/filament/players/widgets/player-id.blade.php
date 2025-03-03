<x-filament-widgets::widget>
    <x-filament::section class="h-full relative">
        <div class="flex items-center w-full gap-2 select-none">
            <div class="flex flex-col lg:flex-row justify-between lg:items-center w-full gap-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-gray-500">User Id:</p>
                    <div class="font-bold flex p-1 bg-gray-200 rounded-lg text-gray-800 select-all flex-wrap">
                        <x-heroicon-c-hashtag class="w-6 h-6" />
                        <p>{{ $user->id }}</p>
                    </div>
                </div>
                <div class="shrink-0">
                    <p class="lg:text-right text-xs text-gray-500">UUID</p>
                    <p class="font-bold text-nowrap flex-nowrap shrink-0">{{ $user->user_id }}</p>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
