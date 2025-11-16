import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance';
import { createVuetify } from 'vuetify';

import 'vuetify/styles'
import '@mdi/font/css/materialdesignicons.css'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const vuetify = createVuetify({
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // Crie a instância do aplicativo Vue
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            // 2. Aplique a instância 'vuetify' que você criou
            .use(vuetify)

        app.mount(el);

        // 3. Chame initializeTheme após a montagem se for necessário acesso ao app
        initializeTheme();
    },
    progress: {
        color: '#4B5563',
    },
});
