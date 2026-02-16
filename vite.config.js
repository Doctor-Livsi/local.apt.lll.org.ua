import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');

    // 👈 Фоллбек на 127.0.0.1, если VITE_HOST не задан
    const viteHost = env.VITE_HOST || '127.0.0.1';
    const vitePort = parseInt(env.VITE_PORT) || 5173;
    const laravelTarget = env.VITE_LARAVEL_TARGET || 'http://127.0.0.1:8000';  // 👈 Новый env для target

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css',
                    'resources/js/app.js',
                    'resources/js/pages/login/login.js',
                    'resources/js/pages/apteks/apteks.js',
                    'resources/js/components/apteks/details.js',
                    'resources/js/components/apteks/ApteksTable.vue', // Компонент для CRUD фото аптек
                ],
                refresh: true,
            }),
            vue(),
        ],
        server: {
            host: viteHost,  // 127.0.0.1 по умолчанию
            port: vitePort,
            strictPort: true,
            hmr: {
                host: viteHost,  // 👈 HMR на IP
                protocol: 'ws',
                port: vitePort,
            },
            cors: true,  // Разрешает cross-origin с куки
            allowedHosts: [
                '127.0.0.1',
                'localhost',
                'apt.dev.local',  // Для теста домена
                'apt.lll.org.ua',  // Мультидомен (m/ps/it.lll.org.ua)
            ],
            proxy: {  // Proxy на 127.0.0.1:8000 для локалки
                '/api': {
                    target: laravelTarget,  // http://127.0.0.1:8000
                    changeOrigin: true,
                    secure: false,
                    rewrite: (path) => path.replace(/^\/api/, '/api'),
                    configure: (proxy, options) => {
                        proxy.on('proxyReq', (proxyReq, req, res) => {
                            if (req.headers.cookie) {
                                proxyReq.setHeader('Cookie', req.headers.cookie);
                            }
                        });
                        proxy.on('proxyRes', (proxyRes, req, res) => {
                            const setCookie = proxyRes.headers['set-cookie'];
                            if (setCookie) {
                                res.setHeader('Set-Cookie', setCookie);
                            }
                        });
                    },
                },
                '/sanctum': {
                    target: laravelTarget,
                    changeOrigin: true,
                    secure: false,
                    configure: (proxy, options) => {
                        proxy.on('proxyReq', (proxyReq, req, res) => {
                            if (req.headers.cookie) {
                                proxyReq.setHeader('Cookie', req.headers.cookie);
                            }
                        });
                        proxy.on('proxyRes', (proxyRes, req, res) => {
                            const setCookie = proxyRes.headers['set-cookie'];
                            if (setCookie) {
                                res.setHeader('Set-Cookie', setCookie);
                            }
                        });
                    },
                },
            },
        },
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js'),
            },
        },
        define: {
            __VITE_APP_URL__: JSON.stringify(env.VITE_APP_URL || 'http://127.0.0.1:5173'),  // Фоллбек на IP
            __VITE_API_BASE__: JSON.stringify(env.VITE_API_BASE || '/api'),
        },
    };
});
