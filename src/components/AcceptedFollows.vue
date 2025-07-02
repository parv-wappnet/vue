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
import { onMounted, ref } from 'vue'
import axios from '../axios'
import { useRouter } from 'vue-router'

const accepted = ref([])
const router = useRouter()

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

onMounted(loadAccepted)
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