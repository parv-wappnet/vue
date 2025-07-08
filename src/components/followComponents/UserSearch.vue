<template>
  <div class="user-search-card">
    <div class="input-group">
      <label for="email" class="block text-sm text-gray-600">Search by Email</label>
      <div class="input-wrapper">
        <span class="input-icon">🔍</span>
        <input v-model="email" id="email" placeholder="Search by email..." type="text" required
          class="w-full p-2 border rounded focus:outline-none" />
      </div>
    </div>

    <div v-if="users.length > 0" class="mt-4 space-y-2">
      <div v-for="user in users" :key="user.id"
        class="user-item p-2 border rounded bg-white flex justify-between items-center cursor-pointer hover:bg-gray-100 transition">
        <div @click="emit('user-found', user)">
          <p class="font-medium text-gray-800">{{ user.name }} ({{ user.email }})</p>
        </div>
      </div>
    </div>
    <div v-else-if="email.length >= 2" class="text-center text-lg text-gray-600 mt-4">
      No users found.
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

<style scoped>
.user-search-card {
  background: white;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  width: 100%;
  max-width: 400px;
  text-align: center;
}

.input-group {
  text-align: left;
}

.input-group label {
  display: block;
  font-size: 0.9rem;
  color: #555;
  margin-bottom: 5px;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 10px;
  color: #999;
  font-size: 1.2rem;
}

input {
  width: 100%;
  padding: 10px 10px 10px 40px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 1rem;
  outline: none;
  transition: border-color 0.3s;
}

input:focus {
  border-color: #4facfe;
}

.user-item {
  border-radius: 5px;
  transition: background-color 0.3s;
}

.user-item:hover {
  background-color: #f3f4f6;
}

@media (max-width: 480px) {
  .user-search-card {
    padding: 20px;
  }

  .input-group label {
    font-size: 0.8rem;
  }

  input {
    font-size: 0.9rem;
    padding: 8px 8px 8px 35px;
  }

  .user-item {
    padding: 8px;
    font-size: 0.9rem;
  }
}
</style>