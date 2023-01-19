import Vue from 'vue'
import App from './App.vue'
import router from './router'
import axios from "./axios";
import {i18n, setGuiLocale} from "./i18n/i18n";
import './styles/title'
import './styles/fontawesome'
import './styles/buefy'
import './styles/app.scss'
import './common/filters';
import {CurrentUser} from "@/auth/CurrentUser";

Vue.prototype.$http = axios;

Vue.prototype.$http.get('config').then((response) => {
    Vue.prototype.$config = response.data;
    Vue.prototype.$user = new CurrentUser();
    Vue.prototype.$user.validateToken().then((userData) => {
        setGuiLocale(userData);
        new Vue({
            i18n,
            router,
            render: h => h(App)
        }).$mount('#app');
    });
});
