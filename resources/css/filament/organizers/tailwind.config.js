import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Organizers/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/filament/organizers/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
