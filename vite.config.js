import { defineConfig, loadEnv } from 'vite';  // 👈 Добавь loadEnv
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig(({ mode }) => {  // 👈 Функция для mode (dev/prod)
    // Загружаем .env (mode: 'development'/'production')
    const env = loadEnv(mode, process.cwd(), '');  // Читает .env, .env.local, .env.development и т.д.

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/js/pages/Login/login.js',
                    'resources/js/pages/Apteks/apteks.js',
                    'resources/js/components/Apteks/ApteksTable.vue', // Компонент для CRUD фото аптек
                ],
                refresh: true,
            }),
            vue(),
        ],
        server: {
            host: env.VITE_HOST || 'localhost',  // 👈 Из .env, fallback на localhost
            port: parseInt(env.VITE_PORT) || 5173,  // 👈 Из .env, fallback на дефолт
            strictPort: true,
            hmr: {
                host: env.VITE_HMR_HOST || 'localhost',  // 👈 Из .env для HMR
                protocol: 'ws',
                port: parseInt(env.VITE_PORT) || 5173,
            },
            cors: true,
            allowedHosts: [
                'apt.dev.local',  // Или динамично: env.VITE_HOST
                'apt.lll.org.ua',  // Для теста мультидоменов
            ],
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js'),
            },
        },
        define: {  // 👈 Опционально: подставь в глобальный JS (для Vue/Acorn)
            __VITE_APP_URL__: JSON.stringify(env.VITE_APP_URL),
        },
    };
});
