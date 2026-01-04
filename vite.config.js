import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            // PERHATIKAN BARIS INI: Ubah 'css/app.css' menjadi 'sass/app.scss'
            input: [
                'resources/sass/app.scss', 
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
});