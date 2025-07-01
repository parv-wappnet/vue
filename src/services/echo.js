import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
window.Pusher = Pusher

export const createEcho = (token) => {
    // Log broadcasting auth request details
    console.log('Initializing broadcasting auth with:', {
        endpoint: 'http://localhost:8003/broadcasting/auth',
        token: token
    });

    return new Echo({
        broadcaster: 'pusher',
        key: 'bec6814461fa57783faf',
        cluster: 'ap2',
        forceTLS: true,
        authEndpoint: 'http://localhost:8003/broadcasting/auth',
        auth: {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        },
        // // Add auth callback to log when auth requests are made
        // authorizer: (channel, options) => {
        //     return {
        //         // authorize: (socketId, callback) => {
        //         //     console.log('Making auth request for channel:', channel.name);
        //         //     console.log('Socket ID:', socketId);
        //         //     console.log('Auth headers:', options.auth.headers);

        //         //     // Call original authorize method
        //         //     // Pusher.Runtime.auth(options, channel, socketId)
        //         //     //     .then(auth => callback(null, auth))
        //         //     //     .catch(err => callback(err));
        //         // }
        //     };
        // }
    })
}
