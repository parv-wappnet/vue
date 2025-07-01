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
import { echo } from '../services/echo'
import { useAuthStore } from '../stores/auth'
const authStore = useAuthStore()
const requests = ref([])

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

onMounted(() => {
    load()
    echo.channel('follow')
        .listen('.follow-request', () => {
            load()
        })
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
