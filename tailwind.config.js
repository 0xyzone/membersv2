import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './resources/views/filament/players/widgets/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
