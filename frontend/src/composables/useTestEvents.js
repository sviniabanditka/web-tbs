import { inject, onMounted, onUnmounted } from 'vue'

export function useTestEvents(eventId) {
    const pusher = inject('pusher')

    const channel = pusher.subscribe(`test.${eventId}`)

    const subscribe = (eventName, callback) => {
        channel.bind(eventName, callback)
    }

    const unsubscribe = (eventName, callback) => {
        channel.unbind(eventName, callback)
    }

    onUnmounted(() => {
        channel.unbind_all()
        pusher.unsubscribe(`test.${eventId}`)
    })

    return {
        subscribe,
        unsubscribe
    }
} 
