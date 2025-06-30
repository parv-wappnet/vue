<template>
    <div>
        <h3>Follow Requests</h3>
        <div v-if="requests.length === 0">No pending requests.</div>

        <div v-for="req in requests" :key="req.id" class="request-box">
            <p><strong>{{ req.sender.name }}</strong> ({{ req.sender.email }}) wants to connect</p>
            <button @click="respond(req.id, 'accepted')">✅ Accept</button>
            <button @click="respond(req.id, 'rejected')">❌ Reject</button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from '../axios'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import { useAuthStore } from '../stores/auth'
import { watch } from 'vue'
Pusher.logToConsole = true

const authStore = useAuthStore()
const requests = ref([])

const echo = new Echo({
    broadcaster: 'pusher',
    key: 'bec6814461fa57783faf',
    cluster: 'ap2',
    forceTLS: true,
    disableStats: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${authStore.token}`,
        },
    },
})

echo.connector.pusher.connection.bind('connected', () => {
    console.log('✅ Connected to Pusher successfully')
})

echo.connector.pusher.connection.bind('error', (err) => {
    console.error('❌ Pusher connection error:', err)
})

echo.connector.pusher.connection.bind('state_change', (states) => {
    console.log('🔄 Pusher state changed:', states)
})


watch(
    () => authStore.user,
    (user) => {
        echo.connector.pusher.connection.bind('connected', () => {
            console.log('✅ Connected to Pusher successfully')
        })
        if (user?.id) {
            echo.private(`user.${user.id}`)
                .listen('.follow.request', (e) => {
                    console.log('📩 New follow request from:', e.sender)
                    load()
                })
        }
    },
    { immediate: true }
)
const load = async () => {
    try {
        console.log('🔍 Watching authStore.user:')
        const res = await axios.get('/follow/pending')
        requests.value = res.data
    } catch (err) {
        console.error('Error loading follow requests:', err)
    }
}

const respond = async (id, status) => {
    try {
        await axios.post('/follow/respond', { request_id: id, status })
        await load() // Reload list after response
    } catch (err) {
        console.error('Error updating request:', err)
    }
}

onMounted(() => {
    load()

    if (authStore.user) {
        echo.private(`user.${authStore.user.id}`)
            .listen('.follow.request', (e) => {
                console.log('📩 New follow request from:', e.sender)
                load()
            })
    }
})
</script>


<style scoped>
.request-box {
    margin-bottom: 1rem;
    padding: 0.75rem;
    border: 1px solid #ccc;
    border-radius: 6px;
}

button {
    margin-right: 0.5rem;
}
</style>
