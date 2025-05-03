import './bootstrap';
import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Configuration Axios
// Commentons cette ligne pour éviter le préfixe /api qui cause des problèmes
// axios.defaults.baseURL = '/api';
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.withCredentials = true;

// Intercepteur pour gérer le token d'authentification
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

// Création de l'application Vue
const app = createApp(App);

// Utilisation du Router
app.use(router);

// Fournir Axios comme propriété globale pour faciliter les appels API
app.config.globalProperties.$axios = axios;

// Montage de l'application sur l'élément #app
app.mount('#app');
