// ecosystem.config.js
module.exports = {
    apps: [
        {
            name: 'vite-dev',
            script: './node_modules/vite/bin/vite.js',
            args: '--host --port 5174',
            interpreter: 'node',
            cwd: './',
            watch: false,
            env: {
                NODE_ENV: 'development'
            },
            user: 'www',
        }
    ]
};
