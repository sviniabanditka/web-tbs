import Pusher from 'pusher-js'

export default {
    install: (app) => {
        const pusher = new Pusher(import.meta.env.VITE_REVERB_APP_KEY, {
            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: import.meta.env.VITE_REVERB_PORT,
            forceTLS: false,
            cluster: 'mt1',
            enabledTransports: ['ws', 'wss'],
            disableStats: true
        })

        pusher.connection.bind('connected', () => {
            console.log('WebSocket connection was successfully established')
        })
        
        pusher.connection.bind('error', (error) => {
            console.log('WebSocket connection error:', error)
        })

        app.provide('pusher', pusher)
    }
} 