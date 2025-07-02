// src/services/useEcho.js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

// This enables Pusher's debug logging to browser console
// Useful during development to see Pusher connection details and events
// Should be set to false in production
Pusher.logToConsole = true

export const echo = new Echo({
    broadcaster: 'pusher',
    key: 'bec6814461fa57783faf',
    cluster: 'ap2',
    forceTLS: true, authEndpoint: 'http://localhost:8003/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('token') || ''}`,
        },
    },
})
