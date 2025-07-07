<template>
  <div class="mb-4">
    <input v-model="email" placeholder="Search by email..." class="border px-2 py-1 rounded w-full" />

    <div v-if="users.length > 0" class="mt-4 space-y-2">
      <div v-for="user in users" :key="user.id" class="p-2 border rounded bg-gray-50 flex justify-between items-center">
        <div type="button" @click="emit('user-found', user)">
          <p class="font-medium">{{ user.name }} ({{ user.email }})</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from '@services/axios'
import { debounce } from 'lodash'

// State
const email = ref('')
const users = ref([])
const emit = defineEmits(['user-found'])

// Fetch multiple users based on partial match
const fetchUsers = async () => {
  try {
    const res = await axios.get('/follow/search', {
      params: { email: email.value }
    })

    users.value = res.data.users || []
  } catch (error) {
    users.value = []
  }
}

// Debounced search
const debouncedSearch = debounce(fetchUsers, 500)

// Watch email input and trigger search when there's input
watch(email, (val) => {
  if (val.length >= 2) {
    debouncedSearch()
  } else {
    users.value = []
  }
})
</script>
