<template>
  <div>
    <!-- User Search -->
    <UserSearch @user-found="handleUserFound" />

    <!-- Follow Request UI -->
    <div v-if="user" class="mt-4 p-4 border rounded shadow bg-white">
      <!-- <img v-if="user.avatar" :src="user.avatar" class="w-12 h-12 rounded-full mb-2" />
      <p class="font-semibold text-lg">{{ user.name }} ({{ user.email }})</p> -->
      <button
        @click="sendFollowRequest"
        class="mt-2 px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700"
      >
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
