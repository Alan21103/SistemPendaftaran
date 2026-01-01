import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/js/logout.js',
                'resources/js/pendaftaran-script.js', 
                'resources/js/pendaftaran-action.js',
                'resources/css/pembayaran-admin.css',
            ],
            refresh: true,
        }),
    ],
});
