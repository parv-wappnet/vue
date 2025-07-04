<template>
    <div>
        <h3>Accepted Connections</h3>
        <div v-if="accepted.length === 0">No accepted requests yet.</div>

        <ul v-else>
            <li v-for="user in accepted" :key="user.id" @click="openChat(user.id)"
                class="cursor-pointer hover:bg-gray-100 p-2">
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

const openChat = (userId) => {
    router.push({ name: 'ChatWindow', params: { userId } })
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
ul {
    padding-left: 1rem;
    list-style: none;
}

li {
    margin-bottom: 0.5rem;
}

.cursor-pointer:hover {
    background-color: #f3f4f6;
    padding: 0.5rem;
    border-radius: 0.25rem;
}
</style>