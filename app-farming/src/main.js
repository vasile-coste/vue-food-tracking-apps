import Vue from "vue";
import VueSession from "vue-session";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import axios from "axios";
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import './assets/css/style.css';

var options = {
    persist: true
}

Vue.use(VueSession, options);

const axiosConfig = {
    baseURL: 'http://127.0.0.1:8000/api/',
    timeout: 30000
};

Vue.prototype.$axios = axios.create(axiosConfig);

Vue.config.productionTip = false;

new Vue({
    router,
    store,
    render: h => h(App)
}).$mount("#app");