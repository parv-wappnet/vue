<template>
    <div>
        <input v-model="email" placeholder="Search by email..." />
        <button @click="searchUser">Search</button>

        <div v-if="user">
            <p>{{ user.name }} ({{ user.email }})</p>
            <button @click="sendFollowRequest">Send Follow Request</button>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@services/axios' // 🧠 your configured axios with baseURL & token

const email = ref('')
const user = ref(null)

const searchUser = async () => {
    try {
        const res = await axios.get('/follow/search', {
            params: { email: email.value }
        })
        user.value = res.data
    } catch (error) {
        user.value = null
        // Global error handler in axios.js will show alert
    }
}

const sendFollowRequest = async () => {
    try {
        await axios.post('/follow/request', {
            receiver_id: user.value.id
        })
        alert('Follow request sent')
    } catch (error) {
        // Error handled globally
    }
}
</script>
