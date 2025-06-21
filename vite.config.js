import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
     server: {
        cors: true,
        origin: 'http://localhost',
        host: 'localhost',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: [
                '**/public/images/**',
                '**/storage/**',
                '**/vendor/**', // ✅ add this line
            ],
        },
    },
});
