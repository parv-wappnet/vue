<template>
  <div class="mb-4">
    <input v-model="email" placeholder="Search by email..." class="border px-2 py-1 rounded" />
    <button @click="searchUser" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded">Search</button>

    <div v-if="user" class="mt-4 p-4 border rounded shadow">
      <img v-if="user.avatar" :src="user.avatar" class="w-10 h-10 rounded-full mb-2" />
      <p>{{ user.name }} ({{ user.email }})</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '@services/axios'

const email = ref('')
const user = ref(null)
const emit = defineEmits(['user-found'])

const searchUser = async () => {
  try {
    const res = await axios.get('/follow/search', {
      params: { email: email.value }
    })
    if (res.data.user) {
      user.value = res.data.user
      console.log('User found in search component:', user.value)
      // Emit user directly after API response
      emit('user-found', user.value)
    } else {
      user.value = null
      alert('User not found')
    }
  } catch (error) {
    user.value = null
  }
}
</script>
