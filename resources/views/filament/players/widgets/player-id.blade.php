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
                <div class="shrink-0 flex flex-col lg:items-end gap-2">
                    <p class="lg:text-right text-xs text-gray-500">UUID</p>
                    <div class="flex items-center gap-1">
                        <p class="font-bold text-nowrap flex-nowrap shrink-0 text-xs lg:text-lg">{{ $user->user_id }}</p>
                        <button type="button" class="text-primary-500 hover:text-primary-600 transition-colors" x-data="{
                                uuid: @js($user->user_id),
                                copyToClipboard() {
                                            try {
                                                if (navigator.clipboard) {
                                                    navigator.clipboard.writeText(this.uuid).then(() => {
                                                        // Filament notification system
                                                        $dispatch('notification', {
                                                            title: 'Copied!',
                                                            message: 'UUID copied to clipboard',
                                                            color: 'success',
                                                            icon: 'heroicon-o-clipboard-document-check'
                                                        });
                                                    });
                                                } else {
                                                    // Fallback method
                                                    const textarea = document.createElement('textarea');
                                                    textarea.value = this.uuid;
                                                    document.body.appendChild(textarea);
                                                    textarea.select();
                                                    document.execCommand('copy');
                                                    document.body.removeChild(textarea);
                                                    
                                                    $dispatch('notification', {
                                                        title: 'Copied!',
                                                        message: 'UUID copied to clipboard',
                                                        color: 'success',
                                                        icon: 'heroicon-o-clipboard-document-check'
                                                    });
                                                }
                                            } catch (error) {
                                                $dispatch('notification', {
                                                    title: 'Error!',
                                                    message: 'Failed to copy UUID',
                                                    color: 'danger',
                                                    icon: 'heroicon-o-exclamation-triangle'
                                                });
                                            }
                                        }
                                    }" @click="copyToClipboard">
                            <x-heroicon-s-clipboard-document class="w-4 h-4" />
                            <span class="sr-only">Copy UUID</span>
                        </button>
                        <livewire:notifications />
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
