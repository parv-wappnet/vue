<template>
    <div class="accepted-follows-card">
        <h3 class="accepted-follows-title">Accepted Connections</h3>
        <div v-if="accepted.length === 0" class="text-center text-lg text-gray-600">
            No accepted requests yet.
        </div>

        <ul v-else class="accepted-list">
            <li v-for="user in accepted" :key="user.id" @click="openChat(user.id)"
                class="accepted-item cursor-pointer hover:bg-gray-100">
                {{ user.name }} ({{ user.email }})
            </li>
        </ul>
    </div>
</template>

<script setup>
import { onMounted, ref, onBeforeUnmount } from 'vue'
import axios from '@services/axios'
import { useRouter } from 'vue-router'
import { createEcho } from '@services/echo'
import { useAuthStore } from '@stores/auth'

const accepted = ref([])
const router = useRouter()
const auth = useAuthStore()
const echo = createEcho(auth.token)

const loadAccepted = async () => {
    try {
        const res = await axios.get('follow/accepted')
        accepted.value = res.data
    } catch (e) {
        console.error('Failed to load accepted connections', e)
    }
}

const openChat = async (userId) => {
    const res = await axios.get(`/conversations/private/${userId}`)
    const conversationId = res.data.conversation_id
    router.push({ name: 'ChatWindow', params: { conversationId } })
}

onMounted(() => {
    loadAccepted()
    const userId = auth.user?.id
    if (!userId) return
    echo.private(`user.${userId}`)
        .listen('.follow-accepted', () => {
            console.log('📡 Realtime accepted connection received')
            loadAccepted()
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
.accepted-follows-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 100%;
    max-width: 400px;
    text-align: center;
}

.accepted-follows-title {
    font-size: 2rem;
    color: #333;
    margin-bottom: 20px;
}

.accepted-list {
    list-style: none;
    padding: 0;
}

.accepted-item {
    padding: 10px;
    margin-bottom: 5px;
    border-radius: 5px;
    transition: background-color 0.3s;
}

.accepted-item:hover {
    background-color: #f3f4f6;
}

@media (max-width: 480px) {
    .accepted-follows-card {
        padding: 20px;
    }

    .accepted-follows-title {
        font-size: 1.5rem;
    }

    .accepted-item {
        padding: 8px;
        font-size: 0.9rem;
    }
}
</style>