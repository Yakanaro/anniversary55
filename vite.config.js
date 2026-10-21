import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            fonts: [
                bunny('Rubik Mono One', {
                    weights: [400],
                    subsets: ['latin', 'cyrillic'],
                }),
                bunny('Oswald', {
                    weights: [400, 500, 600, 700],
                    subsets: ['latin', 'cyrillic'],
                }),
                bunny('Rubik', {
                    weights: [400, 500],
                    subsets: ['latin', 'cyrillic'],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
