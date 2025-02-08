import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/filament/admin/theme.css',
                'resources/css/filament/players/theme.css',
                'resources/css/filament/organizers/theme.css',
            ],
            refresh: true,
        }),
    ],
});
