<template>
    <div>
        <h3>Accepted Connections</h3>
        <div v-if="accepted.length === 0">No accepted requests yet.</div>

        <ul v-else>
            <li v-for="user in accepted" :key="user.id">
                {{ user.name }} ({{ user.email }})
            </li>
        </ul>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from '../axios'

const accepted = ref([])

const loadAccepted = async () => {
    try {
        const res = await axios.get('follow/accepted')
        accepted.value = res.data
    } catch (e) {
        console.error('Failed to load accepted connections', e)
    }
}

onMounted(loadAccepted)
</script>

<style scoped>
ul {
    padding-left: 1rem;
}

li {
    margin-bottom: 0.5rem;
}
</style>
