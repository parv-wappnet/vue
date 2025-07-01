// src/services/useEcho.js
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'

Pusher.logToConsole = true

export const echo = new Echo({
    broadcaster: 'pusher',
    key: 'bec6814461fa57783faf',
    cluster: 'ap2',
    forceTLS: true,
})
