import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import { defineConfig } from 'vite';
import os from 'os';

function getLocalIp() {
    const interfaces = os.networkInterfaces();

    for (const name in interfaces) {
        const ifaceList = interfaces[name];
        if (!Array.isArray(ifaceList)) continue;

        for (const iface of ifaceList) {
            if (iface && iface.family === 'IPv4' && !iface.internal) {
                return iface.address;
            }
        }
    }

    return 'localhost'; // fallback
}

const localIp = getLocalIp();
export default defineConfig({
  server: {
    host: '0.0.0.0',
    port: 5173,
    strictPort: true,
    hmr: {
      host: localIp,
    },
    origin: `http://${localIp}:5173`, // 👈 necessary for correct CORS
    cors: true, // 👈 allow all origins, or customize if needed
  },
    plugins: [
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
