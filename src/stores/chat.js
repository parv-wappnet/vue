import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import axios from 'axios'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { useAuthStore } from './auth'

export const useChatStore = defineStore('chat', () => {
  const messages = ref([])
  const newMessage = ref('')
  const auth = useAuthStore()

  // Setup Echo connection (Pusher-compatible Laravel WebSockets)
  const initEcho = () => {
    if (!window.Echo) {
      window.Pusher = Pusher
      window.Echo = new Echo({
        broadcaster: 'pusher',
        key: 'local',
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
        authEndpoint: 'http://localhost:8003/api/broadcasting/auth',
        auth: {
          headers: {
            Authorization: `Bearer ${auth.token}`
          }
        }
      })

      // Listen to private channel
      window.Echo.private('chat')
        .listen('MessageSent', (e) => {
          messages.value.push({
            user: e.user,
            message: e.message,
            timestamp: Date.now()
          })
        })
    }
  }

  const sendMessage = async () => {
    if (!newMessage.value.trim()) return

    await axios.post('/api/chat/send', {
      message: newMessage.value
    }, {
      headers: { Authorization: `Bearer ${auth.token}` }
    })

    newMessage.value = ''
  }

  const loadMessages = async () => {
    const res = await axios.get('/api/chat/messages', {
      headers: { Authorization: `Bearer ${auth.token}` }
    })
    messages.value = res.data.messages
  }

  return {
    messages,
    newMessage,
    initEcho,
    sendMessage,
    loadMessages,
  }
})
