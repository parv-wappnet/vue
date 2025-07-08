<template>
    <div class="follow-requests-card">
        <h3 class="follow-requests-title">New Follow Requests</h3>

        <div v-if="requests.length === 0" class="text-center text-lg text-gray-600">
            No new follow requests. 🎈
        </div>

        <ul v-else class="space-y-4">
            <li v-for="req in requests" :key="req.id"
                class="flex justify-between items-center p-4 bg-white border-2 border-purple-200 rounded-xl shadow-md hover:shadow-xl transition-all">
                <div class="flex items-center space-x-3">
                    <div>
                        <p class="text-purple-700 font-semibold text-lg">{{ req.sender.name }}</p>
                        <p class="text-sm text-gray-500">{{ req.sender.email }}</p>
                    </div>
                </div>

                <div class="flex space-x-2">
                    <button @click="respond(req.id, 'accepted')"
                        class="px-4 py-2 text-sm font-semibold text-white bg-green-500 rounded-lg hover:bg-green-600 transition">
                        ✅ Accept
                    </button>
                    <button @click="respond(req.id, 'rejected')"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-500 rounded-lg hover:bg-red-600 transition">
                        ❌ Reject
                    </button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import axios from '@services/axios'
import { createEcho } from '@services/echo'
import { useAuthStore } from '@stores/auth'

const auth = useAuthStore()
const echo = createEcho(auth.token)
const requests = ref([])

// Fetch pending follow requests
const load = async () => {
    try {
        const res = await axios.get('/follow/pending')
        requests.value = res.data
    } catch (err) {
        console.error('❌ Error loading follow requests:', err)
    }
}

// Accept or reject request
const respond = async (id, status) => {
    try {
        await axios.post('/follow/respond', { request_id: id, status })
        await load()
    } catch (err) {
        console.error('❌ Error responding to request:', err)
    }
}

onMounted(() => {
    load()

    // Subscribe to private user channel
    const userId = auth.user?.id
    if (!userId) return

    console.log(`✅ Subscribed to: private-user.${userId}`)
    echo.private(`user.${userId}`)
        .listen('.follow-request', (e) => {
            console.log('🔔 New follow request received:', e)
            load()
        })
})

onBeforeUnmount(() => {
    const userId = auth.user?.id
    if (userId) {
        echo.leave(`private-user.${userId}`)
    }
})
</script>

<style scoped>
.follow-requests-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 600px;
    text-align: center;
}

.follow-requests-title {
    font-size: 2rem;
    color: #333;
    margin-bottom: 20px;
}

ul {
    list-style: none;
    padding: 0;
}

li {
    border-radius: 10px;
    transition: box-shadow 0.3s;
}

button {
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

@media (max-width: 480px) {
    .follow-requests-card {
        padding: 20px;
    }

    .follow-requests-title {
        font-size: 1.5rem;
    }

    li {
        padding: 10px;
    }

    button {
        font-size: 0.8rem;
        padding: 6px 12px;
    }
}
</style>