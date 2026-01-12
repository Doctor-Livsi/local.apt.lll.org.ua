import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/pages/Login/login.js',
                'resources/js/pages/Apteks/apteks.js',
                'resources/js/components/Apteks/ApteksTable.vue', // Добавляем компонент
            ],
            refresh: true,
        }),
        vue(),
    ],
    server: {
        host: 'acorn-starter-project.local',
        port: 5174,
        strictPort: true,
        hmr: {
            host: 'acorn-starter-project.local', // 👈 обовʼязково
            protocol: 'ws',
            port: 5174,
        },
        cors: true, // ✅ Додай це, якщо його немає
        allowedHosts: [
            'acorn-starter-project.local', // 👈 обовʼязково
        ],
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
});
