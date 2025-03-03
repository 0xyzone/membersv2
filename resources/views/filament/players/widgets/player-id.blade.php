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
                        <button type="button" class="text-primary-500 hover:text-primary-600 transition-colors relative" x-data="{
        uuid: @js($user->user_id),
        showTooltip: false,
        isError: false,
        tooltipMessage: '',
        copyToClipboard() {
            try {
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(this.uuid).then(() => {
                        this.showFeedback('Copied!', false);
                    });
                } else {
                    // Fallback for older browsers
                    const textarea = document.createElement('textarea');
                    textarea.value = this.uuid;
                    document.body.appendChild(textarea);
                    textarea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textarea);
                    this.showFeedback('Copied!', false);
                }
            } catch (error) {
                this.showFeedback('Failed to copy!', true);
            }
        },
        showFeedback(message, error) {
            this.tooltipMessage = message;
            this.isError = error;
            this.showTooltip = true;
            setTimeout(() => {
                this.showTooltip = false;
            }, 2000);
        }
    }" @click="copyToClipboard">
                            <div class="flex items-center gap-1">
                                <x-heroicon-s-clipboard-document class="w-4 h-4" />
                                <span class="sr-only">Copy UUID</span>

                                <!-- Tooltip -->
                                <div x-show="showTooltip" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute bottom-full left-1/2 transform -translate-x-1/2 -translate-y-2">
                                    <div class="relative">
                                        <div :class="{
                    'bg-green-500': !isError,
                    'bg-red-500': isError
                }" class="text-white text-xs rounded-lg py-1.5 px-3 font-medium flex items-center gap-1">
                                            <template x-if="!isError">
                                                <x-heroicon-o-check class="w-4 h-4" />
                                            </template>
                                            <template x-if="isError">
                                                <x-heroicon-o-x-mark class="w-4 h-4" />
                                            </template>
                                            <span x-text="tooltipMessage"></span>
                                        </div>
                                        <!-- Tooltip arrow -->
                                        <div :class="{
                    'border-t-green-500': !isError,
                    'border-t-red-500': isError
                }" class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent"></div>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
