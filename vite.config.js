import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/custom.css',
                'resources/css/profile.css',
                'resources/css/profile/modals.css',
                'resources/css/profile/variables.css',
                'resources/css/profile/layout.css',
                'resources/css/profile/components.css',

                'resources/js/custom.js',
                // 'resources/js/modal.js',
                'resources/js/profile/create-item-modal.js',
                'resources/js/profile/edit-item-modal.js',
                'resources/js/profile/tag-modal.js',
            ],

            refresh: true,
        }),
    ],
});
