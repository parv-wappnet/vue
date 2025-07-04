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
        if (res.data.user) {
            user.value = res.data.user
        }
        else { alert('User not found') }

    } catch (error) {
        user.value = null
        // Global error handler in axios.js will show alert
    }
}

const sendFollowRequest = async () => {
    try {
        const res = await axios.post('/follow/request', {
            receiver_id: user.value.id
        })
        alert(res.data.message)
    } catch (error) {
        // Error handled globally
    }
}
</script>
