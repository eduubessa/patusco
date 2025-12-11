import { createVuetify, type VuetifyOptions } from 'vuetify';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import '@mdi/font/css/materialdesignicons.css';

const vuetifyOptions: VuetifyOptions = {
    components, // <--- Passado
    directives, // <--- Passado
    icons: {
        defaultSet: 'mdi',
    },
};

const vuetify = createVuetify(vuetifyOptions);

export default vuetify;
