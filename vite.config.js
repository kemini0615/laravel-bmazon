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
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        // watch: {
        //     ignored: ['**/storage/framework/views/**'],
        // },
        host: '0.0.0.0', // 모든 IP 주소(컨테이너 외부)에서 접근 가능
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost', // 브라우저가 HMR(핫리로드) 연결할 때 사용할 주소
        },
    },
});
