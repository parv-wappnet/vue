// src/services/useEcho.js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// This enables Pusher's debug logging to browser console
// Useful during development to see Pusher connection details and events
// Should be set to false in production
Pusher.logToConsole = false
export const createEcho = (token) => new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: import.meta.env.VITE_PUSHER_FORCE_TLS || true,
    authEndpoint: import.meta.env.VITE_API_BROADCASTING_URL,
    auth: {
        headers: {
            Authorization: `Bearer ${token || ''}`,
        },
    },
})
