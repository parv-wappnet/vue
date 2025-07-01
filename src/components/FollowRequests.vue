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
import { onMounted, ref, watch } from 'vue'
import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import axios from '../axios'
import { useAuthStore } from '../stores/auth'

Pusher.logToConsole = true // Enable debug logs

const authStore = useAuthStore()
const requests = ref([])

// Initialize Echo with Pusher
const echo = new Echo({
  broadcaster: 'pusher',
  key: 'bec6814461fa57783faf',
  cluster: 'ap2',
  forceTLS: true,
  disableStats: true, // optional: now deprecated in favor of enableStats = false
  authEndpoint: '/broadcasting/auth',
  auth: {
    headers: {
      Authorization: `Bearer ${authStore.token}`,
    },
  },
})

// Listen for Pusher connection lifecycle events
echo.connector.pusher.connection.bind('connected', () => {
  console.log('✅ Connected to Pusher successfully')
})

echo.connector.pusher.connection.bind('error', (err) => {
  console.error('❌ Pusher connection error:', err)
})

echo.connector.pusher.connection.bind('state_change', (states) => {
  console.log('🔄 Pusher state changed:', states)
})

// Load pending follow requests
const load = async () => {
  try {
    const res = await axios.get('/follow/pending')
    requests.value = res.data
  } catch (err) {
    console.error('❌ Error loading follow requests:', err)
  }
}

// Accept or reject a follow request
const respond = async (id, status) => {
  try {
    await axios.post('/follow/respond', { request_id: id, status })
    await load()
  } catch (err) {
    console.error('❌ Error updating request:', err)
  }
}

// Subscribe to private channel once user is available
watch(
  () => authStore.user,
  (user) => {
    if (user?.id) {
      const channel = echo.private(`follow`)

      channel.listen('.follow.request', (e) => {
        console.log('📩 New follow request from:', e.sender)
        load()
      })
    }
  },
  { immediate: true }
)

onMounted(() => {
  load()
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
