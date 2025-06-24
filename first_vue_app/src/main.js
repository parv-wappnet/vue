// src/main.js
import { createApp } from 'vue';
import App from './App.vue';

// Simple Plugin Example
const myPlugin = {
    install(app) {
        app.config.globalProperties.$sayHello = () => {
            console.log('Hello from global plugin!');
        };
    },
};

const app = createApp(App);

// Global Error Handler
app.config.errorHandler = (err) => {
    console.error('Global error:', err);
};

// Use Plugin
app.use(myPlugin);

// Mount the app
app.mount('#app');