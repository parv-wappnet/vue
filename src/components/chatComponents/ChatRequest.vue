<template>
  <div>
    <!-- User Search -->
    <UserSearch @user-found="handleUserFound" />
    <!-- Follow Request UI -->
    <div v-if="user" class="mt-4 p-4 border rounded shadow bg-white request-card">
      <img v-if="user.avatar" :src="user.avatar" class="w-12 h-12 rounded-full mb-2" alt="Avatar" />
      <p class="font-semibold text-lg">{{ user.name }} ({{ user.email }})</p>
      <button @click="sendFollowRequest"
        class="mt-2 px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition">
        Send Follow Request
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@services/axios'
import UserSearch from '@follow/UserSearch.vue'
import { useAuthStore } from '@stores/auth'

const user = ref(null)
const auth = useAuthStore()

const handleUserFound = (foundUser) => {
  user.value = foundUser
  console.log('User found:', user.value)
}

const sendFollowRequest = async () => {
  try {
    const res = await axios.post('/follow/request', {
      receiver_id: user.value.id
    })
    alert(res.data.message)
  } catch (error) {
    // Handled globally
  }
}
</script>

<style scoped>
.request-card {
  border-radius: 10px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 15px;
  margin-top: 1rem;
}

button {
  padding: 8px 16px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: background-color 0.3s;
}

button:hover {
  opacity: 0.9;
}

@media (max-width: 480px) {
  .request-card {
    padding: 10px;
  }

  button {
    font-size: 0.8rem;
    padding: 6px 12px;
  }
}
</style>