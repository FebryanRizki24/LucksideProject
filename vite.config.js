import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/booking-datetime.js',
                'resources/js/box-barbeman.js',
                'resources/js/midtrans.js',
                'resources/js/scroll-top.js',
                'resources/js/slideshow_detailrecommen.js',
                'resources/js/slideshow-gallery.js',
                'resources/js/toogle-about.js',
                'resources/js/contact.js',
            ],
            refresh: true,
        }),
    ],
});
