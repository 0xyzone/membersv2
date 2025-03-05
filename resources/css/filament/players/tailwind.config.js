import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    darkMode: 'class',
    content: [
        './app/Filament/Players/**/*.php',
        './resources/views/**/*.blade.php',
        './resources/views/filament/players/**/*.blade.php',
        './resources/views/filament/players/widgets/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
