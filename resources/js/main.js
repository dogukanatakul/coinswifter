import {createApp} from 'vue'
import App from "./App.vue";
import router from "./router";
import store from "./store";
import i18n from './locales'
import {nextFactory} from "./helpers/helpers"
import Notifications from '@kyvg/vue3-notification'
import VueCarousel from '@chenfengyuan/vue-carousel'
import $ from 'jquery';
import VueClipboard from 'vue3-clipboard'
import Maska from 'maska'
import VueProgressBar from "@aacassandra/vue3-progressbar";
import VueSocketIO from 'vue-3-socket.io'
import SocketIO from 'socket.io-client'
// import VueLoading from 'vue-loading-overlay';
// import 'vue-loading-overlay/dist/vue-loading.css';
import CKEditor from '@ckeditor/ckeditor5-vue';

import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import VCalendar from 'v-calendar';

import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

import VueApexCharts from "vue3-apexcharts";

import PrimeVue from 'primevue/config';
import "primevue/resources/primevue.css"
import "primevue/resources/themes/nova/theme.css"


import BootstrapVue3 from 'bootstrap-vue-3'
import 'bootstrap/dist/css/bootstrap.css'

import vue3PhotoPreview from 'vue3-photo-preview';
import 'vue3-photo-preview/dist/index.css';


import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";


import ConfirmationService from 'primevue/confirmationservice';
import 'viewerjs/dist/viewer.css'
import VueViewer from 'v-viewer'

const options = {
    color: "#000461",
    failedColor: "#982c2c",
    thickness: "5px",
    transition: {
        speed: "0.2s",
        opacity: "0.6s",
        termination: 300,
    },
    autoRevert: true,
    location: "top",
    inverse: false,
};

global.$ = $
window.$ = $
global.jQuery = $
window.jQuery = $

import {library} from "@fortawesome/fontawesome-svg-core";
import {fas} from '@fortawesome/free-solid-svg-icons'

library.add(fas);
import {fab} from '@fortawesome/free-brands-svg-icons';

library.add(fab);
import {far} from '@fortawesome/free-regular-svg-icons';

library.add(far);

import {dom} from "@fortawesome/fontawesome-svg-core";

dom.watch();

router.beforeEach((to, from, next) => {
    if (to.meta.middleware) {
        const middleware = Array.isArray(to.meta.middleware)
            ? to.meta.middleware
            : [to.meta.middleware];

        const context = {
            from,
            next,
            to,
            store
        };
        const nextMiddleware = nextFactory(context, middleware, 1);
        return middleware[0]({...context, next: nextMiddleware});
    }
    return next();
});

const app = createApp(App)
app.use(store)
app.use(router)
app.use(VueClipboard, {
    autoSetContainer: true,
    appendToBody: true,
})
app.use(i18n)
app.use(VueProgressBar, options)
app.use(BootstrapVue3)
app.use(Notifications)
app.component(VueCarousel.name, VueCarousel)
// app.use(VueLoading)
app.use(VueApexCharts)
app.use(VCalendar, {})
app.use(Maska)
app.use(Toast, {})
app.use(PrimeVue)
app.use(vue3PhotoPreview);
app.component("font-awesome-icon", FontAwesomeIcon)
app.component('v-select', vSelect);
app.use(ConfirmationService)
app.use(VueViewer)
app.use(CKEditor)
app.use(new VueSocketIO({
        debug: true,
        connection: SocketIO('http://127.0.0.1:5000', {
            transports: ['websocket'],
            withCredentials: true,
            credentials: true,
            reconnection: false,
            extraHeaders: {
                "my-custom-header": "abcd"
            }
        }), //options object is Optional
        vuex: {
            store,
            // actionPrefix: "SOCKET_",
            // mutationPrefix: "SOCKET_"
        },
    })
);


app.config.performance = true
app.config.devtools = true
app.mount("#app");
